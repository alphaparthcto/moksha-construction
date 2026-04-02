<?php
/**
 * Moksha Construction Admin — Delete Project (POST only)
 *
 * Deletes the project row (cascade removes project_images) and removes
 * the project's image directory from the filesystem.
 *
 * The confirm dialog is handled by JavaScript on the dashboard before
 * this endpoint is ever reached.
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/upload.php';

requireAuth();

// ---------------------------------------------------------------------------
// POST only
// ---------------------------------------------------------------------------

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method Not Allowed');
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

$token = $_POST['csrf_token'] ?? '';
if (!validateCsrfToken($token)) {
    http_response_code(403);
    exit('Invalid CSRF token. Please go back and try again.');
}

// ---------------------------------------------------------------------------
// Validate project ID
// ---------------------------------------------------------------------------

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id < 1) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Invalid project ID.',
    ];
    header('Location: /admin/');
    exit;
}

// ---------------------------------------------------------------------------
// Load project (need slug for image directory path)
// ---------------------------------------------------------------------------

$stmt = $db->prepare('SELECT id, title, slug FROM projects WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'Project not found.',
    ];
    header('Location: /admin/');
    exit;
}

// ---------------------------------------------------------------------------
// Delete from database (ON DELETE CASCADE removes project_images rows)
// ---------------------------------------------------------------------------

try {
    $stmtDel = $db->prepare('DELETE FROM projects WHERE id = ?');
    $stmtDel->execute([$id]);
} catch (PDOException $e) {
    error_log('Project delete DB error: ' . $e->getMessage());

    $_SESSION['flash'] = [
        'type'    => 'error',
        'message' => 'A database error occurred. The project was not deleted.',
    ];
    header('Location: /admin/');
    exit;
}

// ---------------------------------------------------------------------------
// Delete image directory from filesystem
// ---------------------------------------------------------------------------

$projectDir = PUBLIC_ROOT . '/assets/images/projects/' . $project['slug'];
deleteProjectImages($projectDir);

// ---------------------------------------------------------------------------
// Redirect with success flash
// ---------------------------------------------------------------------------

$_SESSION['flash'] = [
    'type'    => 'success',
    'message' => 'Project "' . htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') . '" deleted successfully.',
];

header('Location: /admin/');
exit;
