<?php
/**
 * Moksha Construction Admin — Edit Project
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/upload.php';

requireAuth();

$currentUser = getCurrentUser($db);

// ---------------------------------------------------------------------------
// Load project by ID
// ---------------------------------------------------------------------------

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id < 1) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Invalid project ID.'];
    header('Location: /admin/projects.php');
    exit;
}

$stmtProject = $db->prepare(
    'SELECT id, title, slug, type, size, location, year, description,
            featured_image, status, sort_order
     FROM projects
     WHERE id = ?
     LIMIT 1'
);
$stmtProject->execute([$id]);
$project = $stmtProject->fetch();

if (!$project) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Project not found.'];
    header('Location: /admin/projects.php');
    exit;
}

// Load existing gallery images
$stmtGallery = $db->prepare(
    'SELECT id, image_path, alt_text, sort_order
     FROM project_images
     WHERE project_id = ?
     ORDER BY sort_order ASC, id ASC'
);
$stmtGallery->execute([$id]);
$existingGallery = $stmtGallery->fetchAll();

// Load existing project videos
$stmtVideos = $db->prepare(
    'SELECT id, video_type, video_path, video_url, title, sort_order
     FROM project_videos WHERE project_id = ? ORDER BY sort_order ASC, id ASC'
);
$stmtVideos->execute([$id]);
$existingVideos = $stmtVideos->fetchAll();

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ---------------------------------------------------------------------------
// POST handler
// ---------------------------------------------------------------------------

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CSRF check ---
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Invalid security token. Please go back and try again.');
    }

    // --- Collect & sanitise ---
    $title          = trim($_POST['title']       ?? '');
    $slug           = trim($_POST['slug']        ?? '');
    $type           = trim($_POST['type']        ?? '');
    $size           = trim($_POST['size']        ?? '');
    $location       = trim($_POST['location']    ?? '');
    $yearRaw        = trim($_POST['year'] ?? '');
    $year           = $yearRaw !== '' ? (int)$yearRaw : null;
    $description    = trim($_POST['description'] ?? '');
    $status         = trim($_POST['status']      ?? 'draft');

    // Image management arrays from Alpine drag-reorder component
    $imageOrder     = array_map('intval', (array)($_POST['image_order']    ?? []));
    $deleteImageIds = array_map('intval', (array)($_POST['delete_images']  ?? []));

    // --- Validate required fields ---
    if ($title === '') {
        $errors[] = 'Project title is required.';
    }

    $allowedTypes = ['residential', 'commercial', 'industrial', 'hospitality', 'religious'];
    if (!in_array($type, $allowedTypes, true)) {
        $errors[] = 'Please select a valid project type.';
    }

    $allowedStatuses = ['published', 'draft', 'hidden'];
    if (!in_array($status, $allowedStatuses, true)) {
        $status = 'draft';
    }

    if ($year !== null) {
        $year = ($year >= 1900 && $year <= (int)date('Y') + 5) ? $year : null;
    }

    // --- Generate / sanitise slug ---
    $slug = $slug !== '' ? generateSlug($slug) : generateSlug($title);

    if ($slug === '') {
        $errors[] = 'Could not generate a valid slug from the title.';
    }

    // --- Check slug uniqueness (exclude current project) ---
    if (empty($errors)) {
        $stmtSlug = $db->prepare(
            'SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?'
        );
        $stmtSlug->execute([$slug, $id]);
        if ((int)$stmtSlug->fetchColumn() > 0) {
            $errors[] = 'Another project already uses this slug. Please edit the slug field.';
        }
    }

    // --- Process new featured image (optional — keep existing if none uploaded) ---
    $featuredImagePath = $project['featured_image'];
    $oldFeaturedPath   = $project['featured_image'];

    if (empty($errors) && !empty($_FILES['featured_image']['tmp_name'])) {
        $fi = $_FILES['featured_image'];

        if ($fi['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Featured image upload error (code ' . (int)$fi['error'] . '). Please try again.';
        } else {
            try {
                $destDir           = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                $featuredImagePath = processImage($fi['tmp_name'], $destDir, 'hero');

                // Remove old featured image file if it changed
                if ($oldFeaturedPath && $oldFeaturedPath !== $featuredImagePath) {
                    $absOld = PUBLIC_ROOT . $oldFeaturedPath;
                    if (is_file($absOld)) {
                        @unlink($absOld);
                    }
                }
            } catch (RuntimeException $e) {
                $errors[] = 'Featured image: ' . $e->getMessage();
            }
        }
    }

    // --- Process new gallery uploads ---
    $newGalleryPaths = [];

    if (empty($errors) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
        $gallery = $_FILES['gallery_images'];
        $count   = count($gallery['tmp_name']);

        for ($i = 0; $i < $count; $i++) {
            if ($gallery['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpPath = $gallery['tmp_name'][$i] ?? '';
            if ($tmpPath === '') {
                continue;
            }

            try {
                $destDir           = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                $newGalleryPaths[] = processImage($tmpPath, $destDir, 'gallery-' . ($i + 1));
            } catch (RuntimeException $e) {
                error_log('Gallery image #' . ($i + 1) . ' skipped: ' . $e->getMessage());
            }
        }
    }

    // --- Save ---
    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // 1. Delete marked gallery images (DB + filesystem)
            if (!empty($deleteImageIds)) {
                $placeholders = implode(',', array_fill(0, count($deleteImageIds), '?'));

                $stmtFetch = $db->prepare(
                    "SELECT id, image_path FROM project_images
                     WHERE id IN ({$placeholders}) AND project_id = ?"
                );
                $stmtFetch->execute([...$deleteImageIds, $id]);
                $toDelete = $stmtFetch->fetchAll();

                foreach ($toDelete as $row) {
                    $absPath = PUBLIC_ROOT . $row['image_path'];
                    if (is_file($absPath)) {
                        @unlink($absPath);
                    }
                }

                $stmtDel = $db->prepare(
                    "DELETE FROM project_images
                     WHERE id IN ({$placeholders}) AND project_id = ?"
                );
                $stmtDel->execute([...$deleteImageIds, $id]);
            }

            // 2. Reorder existing images according to drag-reorder result
            if (!empty($imageOrder)) {
                $stmtReorder = $db->prepare(
                    'UPDATE project_images SET sort_order = :sort_order
                     WHERE id = :img_id AND project_id = :project_id'
                );
                foreach ($imageOrder as $sortPos => $imgId) {
                    $stmtReorder->execute([
                        ':sort_order'  => $sortPos,
                        ':img_id'      => $imgId,
                        ':project_id'  => $id,
                    ]);
                }
            }

            // 3. Insert new gallery images (appended after existing)
            if (!empty($newGalleryPaths)) {
                $stmtMaxOrder = $db->prepare(
                    'SELECT COALESCE(MAX(sort_order), -1) FROM project_images WHERE project_id = ?'
                );
                $stmtMaxOrder->execute([$id]);
                $nextOrder = (int)$stmtMaxOrder->fetchColumn() + 1;

                $stmtIns = $db->prepare(
                    'INSERT INTO project_images (project_id, image_path, sort_order)
                     VALUES (:project_id, :image_path, :sort_order)'
                );

                foreach ($newGalleryPaths as $path) {
                    $stmtIns->execute([
                        ':project_id' => $id,
                        ':image_path' => $path,
                        ':sort_order' => $nextOrder++,
                    ]);
                }
            }

            // 4. Handle video deletions
            $deleteVideoIds = array_map('intval', (array)($_POST['delete_videos'] ?? []));
            if (!empty($deleteVideoIds)) {
                foreach ($deleteVideoIds as $vid) {
                    $vStmt = $db->prepare('SELECT video_type, video_path FROM project_videos WHERE id = ? AND project_id = ?');
                    $vStmt->execute([$vid, $id]);
                    $vRow = $vStmt->fetch();
                    if ($vRow && $vRow['video_type'] === 'upload' && $vRow['video_path']) {
                        $vFile = PUBLIC_ROOT . $vRow['video_path'];
                        if (is_file($vFile)) unlink($vFile);
                    }
                }
                $placeholders = implode(',', array_fill(0, count($deleteVideoIds), '?'));
                $db->prepare("DELETE FROM project_videos WHERE id IN ($placeholders) AND project_id = ?")->execute([...$deleteVideoIds, $id]);
            }

            // 5. Handle video reorder
            $videoOrder = array_map('intval', (array)($_POST['video_order'] ?? []));
            foreach ($videoOrder as $pos => $vid) {
                $db->prepare('UPDATE project_videos SET sort_order = ? WHERE id = ? AND project_id = ?')->execute([$pos + 1, $vid, $id]);
            }

            // 6. Handle new video URL
            $newVideoUrl = trim($_POST['new_video_url'] ?? '');
            if ($newVideoUrl !== '') {
                $videoType = 'youtube';
                $embedUrl  = $newVideoUrl;

                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $newVideoUrl, $m)) {
                    $videoType = 'youtube';
                    $embedUrl  = $newVideoUrl;
                } elseif (preg_match('/vimeo\.com\/(\d+)/', $newVideoUrl, $m)) {
                    $videoType = 'vimeo';
                    $embedUrl  = $newVideoUrl;
                }

                $newVideoTitle = trim($_POST['new_video_title'] ?? '');
                $maxSort = (int)($db->query("SELECT COALESCE(MAX(sort_order),0) FROM project_videos WHERE project_id = {$id}")->fetchColumn());

                $db->prepare('INSERT INTO project_videos (project_id, video_type, video_url, title, sort_order) VALUES (?, ?, ?, ?, ?)')->execute([
                    $id, $videoType, $embedUrl, $newVideoTitle ?: null, $maxSort + 1
                ]);
            }

            // 7. Handle new video file upload
            if (!empty($_FILES['new_video_file']['tmp_name']) && $_FILES['new_video_file']['error'] === UPLOAD_ERR_OK) {
                $vTmp  = $_FILES['new_video_file']['tmp_name'];
                $vName = $_FILES['new_video_file']['name'];
                $vSize = $_FILES['new_video_file']['size'];

                $allowedVideoMime = ['video/mp4', 'video/quicktime', 'video/webm'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $vMime = $finfo->file($vTmp);

                if (in_array($vMime, $allowedVideoMime) && $vSize <= 100 * 1024 * 1024) {
                    $vExt = match($vMime) {
                        'video/mp4'       => 'mp4',
                        'video/quicktime' => 'mov',
                        'video/webm'      => 'webm',
                        default           => 'mp4',
                    };
                    $vSlug = $slug;
                    $vDir  = PUBLIC_ROOT . '/assets/videos/projects/' . $vSlug;
                    if (!is_dir($vDir)) mkdir($vDir, 0755, true);
                    $vFilename = bin2hex(random_bytes(6)) . '-' . preg_replace('/[^a-z0-9-]/', '', strtolower(pathinfo($vName, PATHINFO_FILENAME))) . '.' . $vExt;
                    $vDest = $vDir . '/' . $vFilename;

                    if (move_uploaded_file($vTmp, $vDest)) {
                        $vWebPath      = '/assets/videos/projects/' . $vSlug . '/' . $vFilename;
                        $newVideoTitle = trim($_POST['new_video_title'] ?? '') ?: pathinfo($vName, PATHINFO_FILENAME);
                        $maxSort       = (int)($db->query("SELECT COALESCE(MAX(sort_order),0) FROM project_videos WHERE project_id = {$id}")->fetchColumn());

                        $db->prepare('INSERT INTO project_videos (project_id, video_type, video_path, title, sort_order) VALUES (?, "upload", ?, ?, ?)')->execute([
                            $id, $vWebPath, $newVideoTitle, $maxSort + 1
                        ]);
                    }
                }
            }

            // 8. Update the project row
            $stmtUpdate = $db->prepare(
                'UPDATE projects
                 SET title          = :title,
                     slug           = :slug,
                     type           = :type,
                     size           = :size,
                     location       = :location,
                     year           = :year,
                     description    = :description,
                     featured_image = :featured_image,
                     status         = :status
                 WHERE id = :id'
            );

            $stmtUpdate->execute([
                ':title'          => $title,
                ':slug'           => $slug,
                ':type'           => $type,
                ':size'           => $size !== '' ? $size : null,
                ':location'       => $location !== '' ? $location : null,
                ':year'           => $year,
                ':description'    => $description !== '' ? $description : null,
                ':featured_image' => $featuredImagePath,
                ':status'         => $status,
                ':id'             => $id,
            ]);

            $db->commit();

            // Activity log
            require_once __DIR__ . '/../includes/activity.php';
            logActivity($db, 'project_edit', 'Updated project "' . $title . '"');

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Project "' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" updated successfully.',
            ];

            header('Location: /admin/projects.php');
            exit;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log('Project edit DB error: ' . $e->getMessage());
            $errors[] = 'A database error occurred. Please try again.';
        }
    }

    // Repopulate project with submitted values on validation failure
    $project = array_merge($project, compact(
        'title', 'slug', 'type', 'size', 'location', 'year', 'description', 'status'
    ));
}

// ---------------------------------------------------------------------------
// Template helpers
// ---------------------------------------------------------------------------

function e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function sel(string $option, string $current): string
{
    return $option === $current ? ' selected' : '';
}

function chk(string $option, string $current): string
{
    return $option === $current ? ' checked' : '';
}

// ---------------------------------------------------------------------------
// Page setup
// ---------------------------------------------------------------------------

$admin_page = 'projects';

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Edit Project</h1>
    <p class="page-subtitle"><?= e((string)$project['title']) ?></p>
  </div>
  <a href="/admin/projects.php" class="btn btn-ghost">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Back to Projects
  </a>
</div>

<?php if (!empty($errors)): ?>
<div class="flash flash-error" role="alert">
  <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
    <circle cx="12" cy="12" r="10"/>
    <line x1="12" y1="8" x2="12" y2="12"/>
    <line x1="12" y1="16" x2="12.01" y2="16"/>
  </svg>
  <div>
    <strong>Please fix the following:</strong>
    <ul style="margin:.35rem 0 0 1rem;padding:0;">
      <?php foreach ($errors as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>
<?php endif; ?>

<form
  method="POST"
  enctype="multipart/form-data"
  action="/admin/project-edit.php?id=<?= (int)$project['id'] ?>"
  novalidate
  x-data="projectForm()"
  style="display:contents"
>
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

  <!-- Two-column layout: main fields on left, sidebar on right -->
  <div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start;" class="project-form-grid">

    <!-- =====================================================================
         LEFT COLUMN
    ====================================================================== -->
    <div style="display:flex;flex-direction:column;gap:1.25rem;">

      <!-- Project Details Card -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Project Details</h2>
        </div>
        <div class="card-body" style="display:flex;flex-direction:column;gap:.25rem;">

          <!-- Title -->
          <div class="form-group">
            <label class="form-label" for="title">
              Project Title <span style="color:var(--gold)">*</span>
            </label>
            <input
              type="text"
              id="title"
              name="title"
              class="form-input"
              value="<?= e((string)$project['title']) ?>"
              placeholder="e.g. Downtown Mixed-Use Development"
              required
              maxlength="255"
              x-model="title"
              @input="onTitleInput()"
            >
          </div>

          <!-- Slug -->
          <div class="form-group">
            <label class="form-label" for="slug">
              URL Slug
              <span style="font-weight:400;color:var(--text-3);margin-left:.25rem;">Editable — changing this breaks existing links</span>
            </label>
            <div style="display:flex;border:1px solid rgba(255,255,255,.12);border-radius:var(--radius-sm);overflow:hidden;transition:border-color .18s,box-shadow .18s;" :style="slugFocused ? 'border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-glow)' : ''">
              <span style="padding:.625rem .75rem;background:var(--overlay);border-right:1px solid rgba(255,255,255,.08);color:var(--text-3);font-size:.8125rem;white-space:nowrap;display:flex;align-items:center;">/projects/</span>
              <input
                type="text"
                id="slug"
                name="slug"
                style="flex:1;padding:.625rem .875rem;background:var(--raised);border:none;color:var(--text);font-family:inherit;font-size:.9375rem;outline:none;"
                value="<?= e((string)$project['slug']) ?>"
                placeholder="downtown-mixed-use-development"
                maxlength="255"
                pattern="[a-z0-9\-]+"
                x-model="slug"
                @input="slugEdited = true"
                @focus="slugFocused = true"
                @blur="slugFocused = false"
              >
            </div>
            <p class="form-hint">
              Only lowercase letters, numbers, and hyphens.
              Full URL: <code style="font-family:monospace;font-size:.8rem;background:rgba(255,255,255,.06);padding:.1rem .35rem;border-radius:4px;color:var(--gold);" x-text="'/projects/' + (slug || 'your-slug-here')"></code>
            </p>
          </div>

          <!-- Type -->
          <div class="form-group">
            <label class="form-label" for="type">
              Project Type <span style="color:var(--gold)">*</span>
            </label>
            <select id="type" name="type" class="form-select" required>
              <option value="" disabled<?= $project['type'] === '' ? ' selected' : '' ?>>Select a type&hellip;</option>
              <option value="residential"<?= sel('residential', (string)$project['type']) ?>>Residential</option>
              <option value="commercial"<?= sel('commercial',  (string)$project['type']) ?>>Commercial</option>
              <option value="industrial"<?= sel('industrial',  (string)$project['type']) ?>>Industrial</option>
              <option value="hospitality"<?= sel('hospitality', (string)$project['type']) ?>>Hospitality</option>
              <option value="religious"<?= sel('religious',   (string)$project['type']) ?>>Religious</option>
            </select>
          </div>

          <!-- Size + Location row -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="size">Size</label>
              <input
                type="text"
                id="size"
                name="size"
                class="form-input"
                value="<?= e((string)($project['size'] ?? '')) ?>"
                placeholder="e.g. 280,000 sq ft"
                maxlength="100"
              >
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="location">Location</label>
              <input
                type="text"
                id="location"
                name="location"
                class="form-input"
                value="<?= e((string)($project['location'] ?? '')) ?>"
                placeholder="e.g. Nashville, TN"
                maxlength="255"
              >
            </div>
          </div>

          <!-- Year -->
          <div class="form-group" style="max-width:160px;margin-top:1rem;">
            <label class="form-label" for="year">Year Completed <span style="font-weight:400;color:var(--text-3)">(optional — leave blank for active projects)</span></label>
            <input
              type="number"
              id="year"
              name="year"
              class="form-input"
              value="<?= $project['year'] ? e((string)$project['year']) : '' ?>"
              min="1900"
              max="<?= (int)date('Y') + 5 ?>"
              step="1"
            >
          </div>

        </div>
      </div><!-- /.card -->

      <!-- Description Card -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Description</h2>
        </div>
        <div class="card-body">
          <div class="form-group" style="margin-bottom:0">
            <label class="form-label" for="description">
              Project Description
            </label>
            <textarea
              id="description"
              name="description"
              style="min-height:300px;visibility:hidden;"
            ><?= htmlspecialchars((string)($project['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
          </div>
        </div>
      </div><!-- /.card -->

      <!-- Gallery Card -->
      <div class="card" x-data="galleryReorder()">
        <div class="card-header">
          <h2 class="card-title">Gallery Images</h2>
        </div>
        <div class="card-body">

          <!-- Existing images with drag-reorder -->
          <template x-if="images.length > 0">
            <div>
              <p style="font-size:.8125rem;color:var(--text-3);margin-bottom:1rem;line-height:1.6;">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;opacity:.5"><path d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                Drag to reorder &nbsp;&middot;&nbsp; Click <strong style="color:var(--red)">×</strong> to mark for deletion
              </p>
              <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1.25rem;">
                <template x-for="(img, index) in images" :key="img.id">
                  <div
                    draggable="true"
                    @dragstart="dragStart(index)"
                    @dragover.prevent="dragOver(index)"
                    @dragend="dragEnd()"
                    style="position:relative;cursor:grab;transition:transform .15s;"
                    @mouseover="$el.style.transform='scale(1.02)'"
                    @mouseout="$el.style.transform=''"
                  >
                    <!-- Image with rounded corners -->
                    <img
                      :src="img.path"
                      :alt="img.alt || 'Gallery image'"
                      :style="img.delete
                        ? 'width:100%;aspect-ratio:4/3;object-fit:cover;display:block;border-radius:10px;border:2px solid var(--red);opacity:.4;transition:opacity .2s'
                        : 'width:100%;aspect-ratio:4/3;object-fit:cover;display:block;border-radius:10px;border:2px solid var(--border);transition:opacity .2s'"
                    >

                    <!-- Order badge — below image -->
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-top:.5rem;padding:0 2px;">
                      <span style="background:rgba(255,233,7,.1);color:var(--gold);font-size:.7rem;font-weight:700;padding:2px 10px;border-radius:4px;line-height:1.6;">
                        #<span x-text="index + 1"></span>
                      </span>
                      <span style="font-size:.65rem;color:var(--text-3);">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:11px;height:11px;vertical-align:-1px;margin-right:2px;"><path d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                        drag
                      </span>
                    </div>

                    <!-- Delete button — top right, outside the overflow -->
                    <button
                      type="button"
                      @click="toggleDelete(index)"
                      :style="img.delete ? 'background:var(--red);color:#fff;border-color:var(--red)' : 'background:var(--surface);color:var(--text-3);border-color:var(--border-md)'"
                      style="position:absolute;top:-8px;right:-8px;width:28px;height:28px;border:2px solid;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:15px;line-height:1;padding:0;transition:background .15s,color .15s;z-index:5;box-shadow:0 2px 8px rgba(0,0,0,.4);"
                      @mouseover="$el.style.background='var(--red)';$el.style.color='#fff';$el.style.borderColor='var(--red)'"
                      @mouseout="!img.delete && ($el.style.background='var(--surface)', $el.style.color='var(--text-3)', $el.style.borderColor='var(--border-md)')"
                      :title="img.delete ? 'Undo delete' : 'Mark for deletion'"
                    >×</button>

                    <!-- Hidden inputs -->
                    <template x-if="!img.delete">
                      <input type="hidden" name="image_order[]" :value="img.id">
                    </template>
                    <template x-if="img.delete">
                      <input type="hidden" name="delete_images[]" :value="img.id">
                    </template>
                  </div>
                </template>
              </div>
            </div>
          </template>

          <template x-if="images.length === 0">
            <p style="font-size:.875rem;color:var(--text-3);text-align:center;padding:1rem 0;">No gallery images yet. Add some using the card below.</p>
          </template>

        </div>
      </div><!-- /.card gallery -->

      <!-- ── Add New Photos ── -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Add New Photos</h2>
        </div>
        <div class="card-body" x-data="galleryPreviewer()">
          <p style="font-size:.8125rem;color:var(--text-3);margin-bottom:1rem;">
            Upload additional project photos. JPEG, PNG, or WebP — max 10 MB each. New images will appear after existing ones.
          </p>

          <!-- Drop zone -->
          <div
            style="border:2px dashed;border-radius:var(--radius-md);padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;"
            :style="dragOver ? 'border-color:var(--gold);background:var(--gold-glow)' : 'border-color:rgba(255,255,255,.12);background:transparent'"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            @click="$refs.galleryInput.click()"
            role="button"
            tabindex="0"
            @keypress.enter.space.prevent="$refs.galleryInput.click()"
            aria-label="Upload gallery images"
          >
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:var(--text-3);margin:0 auto .75rem;display:block;">
              <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 16M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p style="font-size:.9375rem;color:var(--text);margin-bottom:.25rem;">
              Drop photos here or <span style="color:var(--gold);text-decoration:underline;cursor:pointer;">click to browse</span>
            </p>
            <p style="font-size:.75rem;color:var(--text-3);">JPEG &middot; PNG &middot; WebP &nbsp;|&nbsp; Max 10 MB per file</p>
          </div>

          <input
            type="file"
            id="gallery_images"
            name="gallery_images[]"
            accept="image/jpeg,image/png,image/webp"
            multiple
            style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;clip:rect(0,0,0,0);"
            x-ref="galleryInput"
            @change="handleFiles($event.target.files)"
          >

          <!-- New image preview grid -->
          <div
            x-show="previews.length > 0"
            x-cloak
            style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:.75rem;margin-top:1rem;"
          >
            <template x-for="(preview, index) in previews" :key="index">
              <div style="position:relative;border-radius:var(--radius-sm);overflow:hidden;background:var(--raised);aspect-ratio:1;">
                <img :src="preview.url" :alt="'New image ' + (index + 1)" style="width:100%;height:100%;object-fit:cover;display:block;">
                <button
                  type="button"
                  style="position:absolute;top:4px;right:4px;width:22px;height:22px;border-radius:50%;background:rgba(0,0,0,.75);border:1px solid rgba(255,255,255,.15);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;padding:0;transition:background .15s;"
                  @click.prevent="removePreview(index)"
                  :title="'Remove ' + preview.name"
                  @mouseover="$el.style.background='var(--red)'"
                  @mouseout="$el.style.background='rgba(0,0,0,.75)'"
                  aria-label="Remove image"
                >
                  <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <span
                  style="position:absolute;bottom:0;left:0;right:0;font-size:.6rem;color:#fff;background:linear-gradient(transparent,rgba(0,0,0,.7));padding:.375rem .375rem .25rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                  x-text="preview.name"
                ></span>
              </div>
            </template>
          </div>

        </div>
      </div><!-- /.card add new photos -->

      <!-- Project Videos Card -->
      <div class="card" x-data="{
        videos: <?= htmlspecialchars(json_encode(array_map(fn($v) => [
            'id'     => $v['id'],
            'type'   => $v['video_type'],
            'path'   => $v['video_path'],
            'url'    => $v['video_url'],
            'title'  => $v['title'] ?? '',
            'delete' => false,
        ], $existingVideos)), ENT_QUOTES, 'UTF-8') ?>,
        dragIndex: null,
        dragStart(i) { this.dragIndex = i },
        dragOver(i) { if (this.dragIndex === null || this.dragIndex === i) return; const item = this.videos.splice(this.dragIndex, 1)[0]; this.videos.splice(i, 0, item); this.dragIndex = i },
        dragEnd() { this.dragIndex = null },
        toggleDelete(i) { this.videos[i].delete = !this.videos[i].delete },
        getEmbed(v) {
          if (v.type === 'youtube') {
            const m = v.url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/);
            return m ? 'https://www.youtube.com/embed/' + m[1] : '';
          }
          if (v.type === 'vimeo') {
            const m = v.url.match(/vimeo\.com\/(\d+)/);
            return m ? 'https://player.vimeo.com/video/' + m[1] : '';
          }
          return '';
        }
      }">
        <div class="card-header">
          <h2 class="card-title">Project Videos</h2>
        </div>
        <div class="card-body">

          <!-- Existing videos -->
          <template x-if="videos.length > 0">
            <div>
              <p style="font-size:.8125rem;color:var(--text-3);margin-bottom:1rem;">
                Drag to reorder. Click &times; to mark for deletion.
              </p>
              <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem;margin-bottom:1rem;">
                <template x-for="(v, index) in videos" :key="v.id">
                  <div
                    draggable="true"
                    @dragstart="dragStart(index)"
                    @dragover.prevent="dragOver(index)"
                    @dragend="dragEnd()"
                    :style="v.delete
                      ? 'position:relative;border-radius:var(--radius-md);overflow:hidden;border:2px solid var(--red);opacity:.5;cursor:grab;transition:opacity .2s'
                      : 'position:relative;border-radius:var(--radius-md);overflow:hidden;border:2px solid var(--border);cursor:grab;transition:opacity .2s'"
                  >
                    <!-- Video preview -->
                    <template x-if="v.type === 'upload'">
                      <video :src="v.path" style="width:100%;aspect-ratio:16/9;object-fit:cover;display:block;background:#000;" muted preload="metadata"></video>
                    </template>
                    <template x-if="v.type === 'youtube' || v.type === 'vimeo'">
                      <div style="width:100%;aspect-ratio:16/9;background:#000;display:flex;align-items:center;justify-content:center;position:relative;">
                        <svg fill="none" viewBox="0 0 24 24" stroke="var(--text-3)" stroke-width="1.5" style="width:32px;height:32px"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                        <span style="position:absolute;bottom:6px;left:8px;font-size:.65rem;color:var(--text-3);text-transform:uppercase;" x-text="v.type"></span>
                      </div>
                    </template>

                    <!-- Title -->
                    <div style="padding:.5rem .75rem;background:var(--raised);font-size:.75rem;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" x-text="v.title || 'Untitled'"></div>

                    <!-- Order badge -->
                    <span style="position:absolute;top:8px;left:8px;background:rgba(13,5,16,.85);color:var(--gold);font-size:.7rem;font-weight:700;padding:2px 8px;border-radius:4px;backdrop-filter:blur(4px);" x-text="index + 1"></span>

                    <!-- Delete button -->
                    <button type="button" @click="toggleDelete(index)"
                      :style="v.delete ? 'background:var(--red);color:#fff' : 'background:rgba(13,5,16,.8);color:var(--text-3)'"
                      style="position:absolute;top:8px;right:8px;width:26px;height:26px;border:1px solid rgba(255,255,255,.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:16px;padding:0;backdrop-filter:blur(4px);"
                    >&times;</button>

                    <!-- Hidden inputs -->
                    <template x-if="!v.delete">
                      <input type="hidden" name="video_order[]" :value="v.id">
                    </template>
                    <template x-if="v.delete">
                      <input type="hidden" name="delete_videos[]" :value="v.id">
                    </template>
                  </div>
                </template>
              </div>
            </div>
          </template>

          <!-- Add new video -->
          <div style="border-top:1px solid var(--border);padding-top:1.25rem;margin-top:.5rem;">
            <p style="font-size:.875rem;font-weight:500;color:var(--text);margin-bottom:1rem;">Add a Video</p>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem;">
              <!-- Option 1: YouTube/Vimeo URL -->
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="new_video_url">YouTube or Vimeo URL</label>
                <input type="url" id="new_video_url" name="new_video_url" class="form-input" placeholder="https://youtube.com/watch?v=..." style="font-size:.8125rem">
              </div>

              <!-- Option 2: Upload file -->
              <div class="form-group" style="margin-bottom:0">
                <label class="form-label" for="new_video_file">Or Upload a Video File</label>
                <input type="file" id="new_video_file" name="new_video_file" accept="video/mp4,video/quicktime,video/webm" class="form-input" style="font-size:.8125rem;padding:.45rem .75rem;">
              </div>
            </div>

            <div class="form-group" style="margin-bottom:0">
              <label class="form-label" for="new_video_title">Video Title <span style="font-weight:400;color:var(--text-3)">(optional)</span></label>
              <input type="text" id="new_video_title" name="new_video_title" class="form-input" placeholder="e.g. Drone flyover, Construction timelapse..." style="font-size:.8125rem">
            </div>

            <p class="form-hint" style="margin-top:.5rem;">MP4, MOV, or WebM. Max 100 MB for uploads. One video per save — add more after saving.</p>
          </div>

        </div>
      </div><!-- /.card videos -->


    </div>
    <!-- END LEFT COLUMN -->

    <!-- =====================================================================
         RIGHT COLUMN — sidebar
    ====================================================================== -->
    <div style="display:flex;flex-direction:column;gap:1.25rem;position:sticky;top:calc(var(--topbar-h) + 1.5rem);">

      <!-- Publish Settings -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Publish Settings</h2>
        </div>
        <div class="card-body">

          <p class="form-label" style="margin-bottom:.75rem;">Status</p>

          <!-- Published -->
          <label
            :style="'display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;border:1px solid;border-radius:6px;cursor:pointer;margin-bottom:.5rem;transition:background .15s,border-color .15s;'
              + (status === 'published' ? 'border-color:rgba(34,197,94,.4);background:rgba(34,197,94,.08);' : 'border-color:rgba(255,255,255,.12);background:rgba(255,255,255,.02);')"
          >
            <input type="radio" name="status" value="published"<?= chk('published', (string)$project['status']) ?> style="position:absolute;opacity:0;width:0;height:0;" x-model="status">
            <span style="width:12px;height:12px;border-radius:50%;background:var(--green);flex-shrink:0;"></span>
            <span style="display:flex;flex-direction:column;">
              <strong style="font-size:.875rem;color:var(--text);">Published</strong>
              <small style="font-size:.75rem;color:var(--text-3);">Visible on the public website</small>
            </span>
          </label>

          <!-- Draft -->
          <label
            :style="'display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;border:1px solid;border-radius:6px;cursor:pointer;margin-bottom:.5rem;transition:background .15s,border-color .15s;'
              + (status === 'draft' ? 'border-color:rgba(234,179,8,.4);background:rgba(234,179,8,.08);' : 'border-color:rgba(255,255,255,.12);background:rgba(255,255,255,.02);')"
          >
            <input type="radio" name="status" value="draft"<?= chk('draft', (string)$project['status']) ?> style="position:absolute;opacity:0;width:0;height:0;" x-model="status">
            <span style="width:12px;height:12px;border-radius:50%;background:var(--yellow);flex-shrink:0;"></span>
            <span style="display:flex;flex-direction:column;">
              <strong style="font-size:.875rem;color:var(--text);">Draft</strong>
              <small style="font-size:.75rem;color:var(--text-3);">Saved but not yet live</small>
            </span>
          </label>

          <!-- Hidden -->
          <label
            :style="'display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;border:1px solid;border-radius:6px;cursor:pointer;transition:background .15s,border-color .15s;'
              + (status === 'hidden' ? 'border-color:rgba(255,255,255,.25);background:rgba(255,255,255,.06);' : 'border-color:rgba(255,255,255,.12);background:rgba(255,255,255,.02);')"
          >
            <input type="radio" name="status" value="hidden"<?= chk('hidden', (string)$project['status']) ?> style="position:absolute;opacity:0;width:0;height:0;" x-model="status">
            <span style="width:10px;height:10px;border-radius:50%;background:var(--text-3);flex-shrink:0;"></span>
            <span style="display:flex;flex-direction:column;">
              <strong style="font-size:.875rem;color:var(--text);">Hidden</strong>
              <small style="font-size:.75rem;color:var(--text-3);">Not visible anywhere</small>
            </span>
          </label>

          <!-- Action buttons -->
          <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid var(--border);display:flex;flex-direction:column;gap:.5rem;">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
              <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path d="M5 13l4 4L19 7"/>
              </svg>
              Save Changes
            </button>
            <a href="/admin/projects.php" class="btn btn-ghost" style="width:100%;justify-content:center;">Cancel</a>
          </div>

        </div>
      </div><!-- /.card publish -->

      <!-- Featured Image -->
      <div class="card" x-data="featuredPreviewer()">
        <div class="card-header">
          <h2 class="card-title">Featured Image</h2>
        </div>
        <div class="card-body">
          <p style="font-size:.8125rem;color:var(--text-3);margin-bottom:.875rem;line-height:1.6;">
            Cover photo shown on the projects listing and detail page. Max 10 MB.
          </p>

          <?php if (!empty($project['featured_image'])): ?>
          <!-- Current image — shown until a new file is selected -->
          <div x-show="!previewUrl" style="margin-bottom:.75rem;">
            <p style="font-size:.75rem;color:var(--text-3);margin-bottom:.5rem;font-weight:500;text-transform:uppercase;letter-spacing:.05em;">Current Image</p>
            <div style="width:100%;aspect-ratio:16/9;border-radius:var(--radius-md);overflow:hidden;border:1px solid var(--border);">
              <img
                src="<?= e((string)$project['featured_image']) ?>"
                alt="Current featured image"
                style="width:100%;height:100%;object-fit:cover;display:block;"
              >
            </div>
            <button
              type="button"
              class="btn btn-ghost"
              style="margin-top:.625rem;padding:.375rem .75rem;font-size:.8125rem;width:100%;justify-content:center;"
              @click="$refs.featuredInput.click()"
            >
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 16M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              Change Image
            </button>
          </div>
          <?php endif; ?>

          <!-- Drop / preview area (shown when a new file is staged) -->
          <div
            x-show="previewUrl || <?= empty($project['featured_image']) ? 'true' : 'false' ?>"
            x-cloak
            style="width:100%;aspect-ratio:16/9;border:2px dashed;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;cursor:pointer;overflow:hidden;transition:border-color .2s,background .2s;margin-bottom:.75rem;"
            :style="previewUrl
              ? 'border-color:rgba(255,255,255,.1);border-style:solid;'
              : (dragOver ? 'border-color:var(--gold);background:var(--gold-glow)' : 'border-color:rgba(255,255,255,.12);background:rgba(255,255,255,.02)')"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            @click="$refs.featuredInput.click()"
            role="button"
            tabindex="0"
            @keypress.enter.space.prevent="$refs.featuredInput.click()"
            aria-label="Upload featured image"
          >
            <template x-if="!previewUrl">
              <div style="display:flex;flex-direction:column;align-items:center;gap:.5rem;color:var(--text-3);">
                <svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                  <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 16M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span style="font-size:.8125rem;">Click or drop image</span>
              </div>
            </template>
            <template x-if="previewUrl">
              <img :src="previewUrl" alt="New featured image preview" style="width:100%;height:100%;object-fit:cover;display:block;">
            </template>
          </div>

          <input
            type="file"
            id="featured_image"
            name="featured_image"
            accept="image/jpeg,image/png,image/webp"
            style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;clip:rect(0,0,0,0);"
            x-ref="featuredInput"
            @change="handleFile($event.target.files[0])"
          >

          <!-- Actions once a new file is staged -->
          <div x-show="previewUrl" x-cloak style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;">
            <button
              type="button"
              class="btn btn-ghost"
              style="padding:.375rem .75rem;font-size:.8125rem;"
              @click="clearPreview()"
            >
              Cancel
            </button>
            <span style="font-size:.75rem;color:var(--text-3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex:1;" x-text="filename"></span>
          </div>

          <p class="form-hint" x-show="!previewUrl">
            JPEG, PNG, or WebP. Resized to max 2,000 px wide.
          </p>

        </div>
      </div><!-- /.card featured -->

    </div>
    <!-- END RIGHT COLUMN -->

  </div><!-- /grid -->

</form>

<script>
// ============================================================================
// Alpine.js components — project edit form
// ============================================================================

function projectForm() {
  return {
    title: <?= json_encode((string)$project['title']) ?>,
    slug:  <?= json_encode((string)$project['slug']) ?>,
    slugEdited:  true, // always treat as edited on the edit page — don't auto-overwrite
    slugFocused: false,
    status: <?= json_encode((string)$project['status']) ?>,

    onTitleInput() {
      if (!this.slugEdited) {
        this.slug = this.toSlug(this.title);
      }
    },

    toSlug(text) {
      return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9\s\-]/g, '')
        .trim()
        .replace(/[\s\-]+/g, '-')
        .replace(/^-+|-+$/g, '');
    },
  };
}

function galleryReorder() {
  return {
    images: <?= json_encode(array_map(fn($img) => [
        'id'     => $img['id'],
        'path'   => $img['image_path'],
        'alt'    => $img['alt_text'] ?? '',
        'delete' => false,
    ], $existingGallery)) ?>,
    dragIndex: null,

    dragStart(index) { this.dragIndex = index; },
    dragOver(index) {
      if (this.dragIndex === null || this.dragIndex === index) return;
      const item = this.images.splice(this.dragIndex, 1)[0];
      this.images.splice(index, 0, item);
      this.dragIndex = index;
    },
    dragEnd() { this.dragIndex = null; },
    toggleDelete(index) { this.images[index].delete = !this.images[index].delete; },
  };
}

function featuredPreviewer() {
  return {
    previewUrl: null,
    filename:   '',
    dragOver:   false,

    handleFile(file) {
      if (!file || !this.isValid(file)) {
        if (file) alert('Please select a JPEG, PNG, or WebP image under 10 MB.');
        return;
      }
      this.filename   = file.name;
      this.previewUrl = URL.createObjectURL(file);
    },

    handleDrop(event) {
      this.dragOver = false;
      const file = event.dataTransfer.files[0];
      if (!file) return;
      const dt = new DataTransfer();
      dt.items.add(file);
      this.$refs.featuredInput.files = dt.files;
      this.handleFile(file);
    },

    clearPreview() {
      if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
      this.previewUrl = null;
      this.filename   = '';
      this.$refs.featuredInput.value = '';
    },

    isValid(file) {
      const ok = ['image/jpeg', 'image/png', 'image/webp'];
      return ok.includes(file.type) && file.size <= 10 * 1024 * 1024;
    },
  };
}

function galleryPreviewer() {
  return {
    previews: [],
    allFiles: [],
    dragOver: false,

    handleFiles(fileList) {
      Array.from(fileList).forEach(file => {
        if (!this.isValid(file)) return;
        this.previews.push({ url: URL.createObjectURL(file), name: file.name });
        this.allFiles.push(file);
      });
      this.syncInput();
    },

    handleDrop(event) {
      this.dragOver = false;
      this.handleFiles(event.dataTransfer.files);
    },

    removePreview(index) {
      URL.revokeObjectURL(this.previews[index].url);
      this.previews.splice(index, 1);
      this.allFiles.splice(index, 1);
      this.syncInput();
    },

    syncInput() {
      const dt = new DataTransfer();
      this.allFiles.forEach(f => dt.items.add(f));
      this.$refs.galleryInput.files = dt.files;
    },

    isValid(file) {
      const ok = ['image/jpeg', 'image/png', 'image/webp'];
      return ok.includes(file.type) && file.size <= 10 * 1024 * 1024;
    },
  };
}
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
