<?php
/**
 * Crop existing image endpoint.
 *
 * Receives a cropped image blob (multipart) for an existing project image.
 * Saves the new image, updates the DB row, deletes the old file.
 *
 * Required POST fields:
 *   - image_type: 'gallery' | 'featured'
 *   - project_id: int (required for both)
 *   - image_id:   int (required for gallery only)
 *   - image:      file (the cropped JPEG/PNG/WebP blob)
 */

header('Content-Type: application/json');

// Resolve includes/ — local dev vs live server
$_inc = file_exists(__DIR__ . '/../../includes/db.php')
    ? __DIR__ . '/../..'
    : __DIR__ . '/..';

require_once $_inc . '/includes/auth.php';
requireAuth();

require_once $_inc . '/includes/db.php';
require_once $_inc . '/includes/upload.php';
require_once $_inc . '/includes/activity.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$image_type = $_POST['image_type'] ?? '';
$project_id = (int)($_POST['project_id'] ?? 0);
$image_id   = (int)($_POST['image_id']   ?? 0);

if (!in_array($image_type, ['gallery', 'featured'], true) || $project_id <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'Invalid request parameters.']);
    exit;
}

if (empty($_FILES['image']['tmp_name']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(422);
    echo json_encode(['error' => 'No image uploaded.']);
    exit;
}

// Look up the project for the slug (used for the dest dir)
$stmt = $db->prepare("SELECT id, slug, title FROM projects WHERE id = ? LIMIT 1");
$stmt->execute([$project_id]);
$project = $stmt->fetch();
if (!$project) {
    http_response_code(404);
    echo json_encode(['error' => 'Project not found.']);
    exit;
}

$slug    = $project['slug'];
$destDir = PUBLIC_ROOT . '/assets/images/projects/' . $slug;

try {

    if ($image_type === 'featured') {
        // Process the cropped blob
        $newPath = processImage(
            $_FILES['image']['tmp_name'],
            $destDir,
            'hero-cropped'
        );

        // Read old path, then update DB
        $oldStmt = $db->prepare("SELECT featured_image FROM projects WHERE id = ?");
        $oldStmt->execute([$project_id]);
        $oldPath = $oldStmt->fetchColumn();

        $upd = $db->prepare("UPDATE projects SET featured_image = ?, updated_at = NOW() WHERE id = ?");
        $upd->execute([$newPath, $project_id]);

        // Delete old file
        if ($oldPath && $oldPath !== $newPath) {
            $absOld = PUBLIC_ROOT . $oldPath;
            if (is_file($absOld)) @unlink($absOld);
        }

        logActivity($db, 'project_image_crop', "Cropped featured image for project #{$project_id} ({$project['title']})");

        echo json_encode(['success' => true, 'new_path' => $newPath]);
        exit;
    }

    // ----- gallery -----
    if ($image_id <= 0) {
        http_response_code(422);
        echo json_encode(['error' => 'image_id required for gallery crop.']);
        exit;
    }

    // Verify image belongs to this project
    $imgStmt = $db->prepare("SELECT id, image_path FROM project_images WHERE id = ? AND project_id = ? LIMIT 1");
    $imgStmt->execute([$image_id, $project_id]);
    $imgRow = $imgStmt->fetch();
    if (!$imgRow) {
        http_response_code(404);
        echo json_encode(['error' => 'Gallery image not found.']);
        exit;
    }

    $newPath = processImage(
        $_FILES['image']['tmp_name'],
        $destDir,
        'gallery-cropped'
    );

    $upd = $db->prepare("UPDATE project_images SET image_path = ? WHERE id = ?");
    $upd->execute([$newPath, $image_id]);

    // Delete old file
    if ($imgRow['image_path'] && $imgRow['image_path'] !== $newPath) {
        $absOld = PUBLIC_ROOT . $imgRow['image_path'];
        if (is_file($absOld)) @unlink($absOld);
    }

    logActivity($db, 'project_image_crop', "Cropped gallery image #{$image_id} for project #{$project_id} ({$project['title']})");

    echo json_encode(['success' => true, 'new_path' => $newPath]);

} catch (RuntimeException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    error_log('Crop image DB error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error.']);
}
