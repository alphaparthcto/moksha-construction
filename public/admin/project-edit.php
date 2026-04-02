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
// Load project by ID (GET param)
// ---------------------------------------------------------------------------

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id < 1) {
    http_response_code(404);
    require_once __DIR__ . '/../404.php';
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
    http_response_code(404);
    require_once __DIR__ . '/../404.php';
    exit;
}

// Load gallery images for this project
$stmtGallery = $db->prepare(
    'SELECT id, image_path, alt_text, sort_order
     FROM project_images
     WHERE project_id = ?
     ORDER BY sort_order ASC, id ASC'
);
$stmtGallery->execute([$id]);
$existingGallery = $stmtGallery->fetchAll();

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Invalid CSRF token. Please go back and try again.');
    }
}

// ---------------------------------------------------------------------------
// POST handler
// ---------------------------------------------------------------------------

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    // ---- Collect & sanitise input ----
    $title       = trim($_POST['title'] ?? '');
    $slug        = trim($_POST['slug'] ?? '');
    $type        = trim($_POST['type'] ?? '');
    $size        = trim($_POST['size'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $year        = (int)($_POST['year'] ?? date('Y'));
    $description = trim($_POST['description'] ?? '');
    $status      = trim($_POST['status'] ?? 'draft');

    // IDs of gallery images the user wants to delete
    $deleteImageIds = array_map('intval', (array)($_POST['delete_images'] ?? []));

    // ---- Validate ----
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

    $year = ($year >= 1900 && $year <= (int)date('Y') + 5) ? $year : (int)date('Y');

    // ---- Slug: re-generate or sanitise ----
    if ($slug === '') {
        $slug = generateSlug($title);
    } else {
        $slug = generateSlug($slug);
    }

    if ($slug === '') {
        $errors[] = 'Could not generate a valid slug from the title.';
    }

    // ---- Check slug uniqueness (exclude current project) ----
    if (empty($errors)) {
        $stmtSlug = $db->prepare(
            'SELECT COUNT(*) FROM projects WHERE slug = ? AND id != ?'
        );
        $stmtSlug->execute([$slug, $id]);
        if ((int)$stmtSlug->fetchColumn() > 0) {
            $errors[] = 'Another project already uses this slug. Please edit the slug field.';
        }
    }

    // ---- Process new featured image (optional) ----
    $featuredImagePath = $project['featured_image']; // keep existing by default
    $oldFeaturedPath   = $project['featured_image'];

    if (empty($errors) && !empty($_FILES['featured_image']['tmp_name'])) {
        $fi = $_FILES['featured_image'];

        if ($fi['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Featured image upload error (code ' . $fi['error'] . ').';
        } else {
            try {
                $destDir           = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                $featuredImagePath = processImage($fi['tmp_name'], $destDir, 'hero');

                // Delete old featured image file if it changed
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

    // ---- Process new gallery uploads ----
    $newGalleryPaths = [];

    if (empty($errors) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
        $gallery = $_FILES['gallery_images'];
        $count   = count($gallery['tmp_name']);

        for ($i = 0; $i < $count; $i++) {
            if ($gallery['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpPath = $gallery['tmp_name'][$i];
            if (empty($tmpPath)) {
                continue;
            }

            try {
                $destDir           = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                $imgPath           = processImage($tmpPath, $destDir, 'gallery-' . ($i + 1));
                $newGalleryPaths[] = $imgPath;
            } catch (RuntimeException $e) {
                error_log('Gallery image #' . ($i + 1) . ' failed: ' . $e->getMessage());
            }
        }
    }

    // ---- Save ----
    if (empty($errors)) {
        try {
            $db->beginTransaction();

            // 1. Delete marked gallery images (DB + filesystem)
            if (!empty($deleteImageIds)) {
                // Fetch their paths first so we can remove files
                $placeholders = implode(',', array_fill(0, count($deleteImageIds), '?'));
                $stmtFetch    = $db->prepare(
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

            // 2. Insert new gallery images
            if (!empty($newGalleryPaths)) {
                // Find the current max sort_order for this project
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

            // 3. UPDATE the project row
            $stmtUpdate = $db->prepare(
                'UPDATE projects
                 SET title = :title,
                     slug = :slug,
                     type = :type,
                     size = :size,
                     location = :location,
                     year = :year,
                     description = :description,
                     featured_image = :featured_image,
                     status = :status
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

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Project "' . htmlspecialchars($title) . '" updated successfully.',
            ];

            header('Location: /admin/');
            exit;

        } catch (PDOException $e) {
            $db->rollBack();
            error_log('Project edit DB error: ' . $e->getMessage());
            $errors[] = 'A database error occurred. Please try again.';
        }
    }

    // Repopulate the project array with submitted values on error so the
    // form re-renders with what the user typed.
    $project = array_merge($project, compact(
        'title', 'slug', 'type', 'size', 'location', 'year', 'description', 'status'
    ));
}

// ---------------------------------------------------------------------------
// Template helpers
// ---------------------------------------------------------------------------

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function selected(string $option, string $current): string
{
    return $option === $current ? ' selected' : '';
}

function checked(string $option, string $current): string
{
    return $option === $current ? ' checked' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Project — Moksha Admin</title>
  <meta name="robots" content="noindex, nofollow">
  <?php require_once __DIR__ . '/includes/admin-header.php'; ?>
</head>
<body class="admin-body">

<?php require_once __DIR__ . '/includes/admin-nav.php'; ?>

<div class="admin-page">

  <!-- Page header -->
  <div class="admin-page-header">
    <div>
      <h1 class="admin-page-title">Edit Project</h1>
      <p class="admin-page-subtitle"><?= e($project['title']) ?></p>
    </div>
    <a href="/admin/" class="admin-btn admin-btn-ghost">
      <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      Back to Dashboard
    </a>
  </div>

  <?php if (!empty($errors)): ?>
  <div class="admin-alert admin-alert-error" role="alert">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
      <strong>Please fix the following errors:</strong>
      <ul class="admin-error-list">
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
  >
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

    <div class="admin-form-grid">

      <!-- ================================================================
           LEFT COLUMN — Main fields
           ================================================================ -->
      <div class="admin-form-main">

        <!-- Project Details -->
        <div class="admin-card">
          <h2 class="admin-card-title">Project Details</h2>

          <!-- Title -->
          <div class="admin-field">
            <label class="admin-label" for="title">
              Project Title <span class="admin-required">*</span>
            </label>
            <input
              type="text"
              id="title"
              name="title"
              class="admin-input"
              value="<?= e((string)$project['title']) ?>"
              placeholder="e.g. Downtown Mixed-Use Development"
              required
              maxlength="255"
              x-model="title"
              @input="updateSlug()"
            >
          </div>

          <!-- Slug -->
          <div class="admin-field">
            <label class="admin-label" for="slug">
              URL Slug
              <span class="admin-label-hint">Editable — changing this breaks existing links</span>
            </label>
            <div class="admin-input-group">
              <span class="admin-input-prefix">/projects/</span>
              <input
                type="text"
                id="slug"
                name="slug"
                class="admin-input admin-input-prefixed"
                value="<?= e((string)$project['slug']) ?>"
                placeholder="downtown-mixed-use-development"
                maxlength="255"
                pattern="[a-z0-9\-]+"
                x-model="slug"
                @input="slugEdited = true"
              >
            </div>
            <p class="admin-field-hint">
              Only lowercase letters, numbers, and hyphens.
              Full URL: <code x-text="'/projects/' + (slug || 'your-slug-here')"></code>
            </p>
          </div>

          <!-- Type -->
          <div class="admin-field">
            <label class="admin-label" for="type">
              Project Type <span class="admin-required">*</span>
            </label>
            <select id="type" name="type" class="admin-select" required>
              <option value="" disabled>Select a type&hellip;</option>
              <option value="residential"<?= selected('residential', (string)$project['type']) ?>>Residential</option>
              <option value="commercial"<?= selected('commercial', (string)$project['type']) ?>>Commercial</option>
              <option value="industrial"<?= selected('industrial', (string)$project['type']) ?>>Industrial</option>
              <option value="hospitality"<?= selected('hospitality', (string)$project['type']) ?>>Hospitality</option>
              <option value="religious"<?= selected('religious', (string)$project['type']) ?>>Religious</option>
            </select>
          </div>

          <!-- Size + Location -->
          <div class="admin-field-row">
            <div class="admin-field">
              <label class="admin-label" for="size">Size</label>
              <input
                type="text"
                id="size"
                name="size"
                class="admin-input"
                value="<?= e((string)($project['size'] ?? '')) ?>"
                placeholder="e.g. 280,000 sq ft"
                maxlength="100"
              >
            </div>
            <div class="admin-field">
              <label class="admin-label" for="location">Location</label>
              <input
                type="text"
                id="location"
                name="location"
                class="admin-input"
                value="<?= e((string)($project['location'] ?? '')) ?>"
                placeholder="e.g. Nashville, TN"
                maxlength="255"
              >
            </div>
          </div>

          <!-- Year -->
          <div class="admin-field admin-field-narrow">
            <label class="admin-label" for="year">Year</label>
            <input
              type="number"
              id="year"
              name="year"
              class="admin-input"
              value="<?= e((string)($project['year'] ?? date('Y'))) ?>"
              min="1900"
              max="<?= (int)date('Y') + 5 ?>"
              step="1"
            >
          </div>
        </div>

        <!-- Description -->
        <div class="admin-card">
          <h2 class="admin-card-title">Description</h2>
          <div class="admin-field">
            <label class="admin-label" for="description">
              Project Description
              <span class="admin-label-hint">Plain text or basic HTML</span>
            </label>
            <textarea
              id="description"
              name="description"
              class="admin-textarea"
              rows="10"
              placeholder="Describe the project scope, materials used, unique challenges, and outcome…"
            ><?= e((string)($project['description'] ?? '')) ?></textarea>
          </div>
        </div>

        <!-- Existing Gallery + New Uploads -->
        <div class="admin-card">
          <h2 class="admin-card-title">Gallery Images</h2>

          <?php if (!empty($existingGallery)): ?>
          <p class="admin-card-desc">
            Check the box on any image to delete it when you save.
          </p>
          <div class="admin-gallery-existing">
            <?php foreach ($existingGallery as $img): ?>
            <div class="admin-gallery-item admin-gallery-existing-item">
              <img
                src="<?= e($img['image_path']) ?>"
                alt="<?= e($img['alt_text'] ?? 'Gallery image') ?>"
                class="admin-gallery-thumb"
                loading="lazy"
              >
              <label class="admin-gallery-delete-label" title="Delete this image">
                <input
                  type="checkbox"
                  name="delete_images[]"
                  value="<?= (int)$img['id'] ?>"
                  class="admin-gallery-delete-check"
                >
                <span class="admin-gallery-delete-x">
                  <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </span>
              </label>
            </div>
            <?php endforeach; ?>
          </div>
          <?php else: ?>
          <p class="admin-card-desc">No gallery images yet. Upload some below.</p>
          <?php endif; ?>

          <div class="admin-divider"></div>

          <p class="admin-card-desc">Add more images:</p>

          <div
            class="admin-dropzone"
            :class="{ 'admin-dropzone-over': dragOver }"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            @click="$refs.galleryInput.click()"
            x-data="galleryPreviewer()"
          >
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" class="admin-dropzone-icon"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 16M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="admin-dropzone-text">
              Drop photos here or <span class="admin-dropzone-link">click to browse</span>
            </p>
            <p class="admin-dropzone-hint">JPEG &middot; PNG &middot; WebP &nbsp;|&nbsp; Max 10 MB per file</p>
          </div>

          <div x-data="galleryPreviewer()">
            <input
              type="file"
              id="gallery_images"
              name="gallery_images[]"
              accept="image/jpeg,image/png,image/webp"
              multiple
              class="admin-file-hidden"
              x-ref="galleryInput"
              @change="handleFiles($event.target.files)"
            >

            <div class="admin-gallery-grid" x-show="previews.length > 0" x-cloak>
              <template x-for="(preview, index) in previews" :key="index">
                <div class="admin-gallery-item">
                  <img :src="preview.url" :alt="'New image ' + (index + 1)" class="admin-gallery-thumb">
                  <button
                    type="button"
                    class="admin-gallery-remove"
                    @click.prevent="removePreview(index)"
                    :title="'Remove ' + preview.name"
                    aria-label="Remove image"
                  >
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M6 18L18 6M6 6l12 12"/></svg>
                  </button>
                  <span class="admin-gallery-name" x-text="preview.name"></span>
                </div>
              </template>
            </div>
          </div>
        </div>

      </div>

      <!-- ================================================================
           RIGHT COLUMN — Sidebar
           ================================================================ -->
      <div class="admin-form-sidebar">

        <!-- Publish Settings -->
        <div class="admin-card">
          <h2 class="admin-card-title">Publish Settings</h2>

          <div class="admin-field">
            <span class="admin-label">Status</span>
            <div class="admin-radio-group">
              <label class="admin-radio-label">
                <input type="radio" name="status" value="published"<?= checked('published', (string)$project['status']) ?> class="admin-radio">
                <span class="admin-radio-indicator admin-radio-published"></span>
                <span class="admin-radio-text">
                  <strong>Published</strong>
                  <small>Visible on the public website</small>
                </span>
              </label>
              <label class="admin-radio-label">
                <input type="radio" name="status" value="draft"<?= checked('draft', (string)$project['status']) ?> class="admin-radio">
                <span class="admin-radio-indicator admin-radio-draft"></span>
                <span class="admin-radio-text">
                  <strong>Draft</strong>
                  <small>Saved but not yet live</small>
                </span>
              </label>
              <label class="admin-radio-label">
                <input type="radio" name="status" value="hidden"<?= checked('hidden', (string)$project['status']) ?> class="admin-radio">
                <span class="admin-radio-indicator admin-radio-hidden"></span>
                <span class="admin-radio-text">
                  <strong>Hidden</strong>
                  <small>Not visible anywhere</small>
                </span>
              </label>
            </div>
          </div>

          <div class="admin-card-actions">
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-full">
              <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
              Save Changes
            </button>
            <a href="/admin/" class="admin-btn admin-btn-ghost admin-btn-full">Cancel</a>
          </div>
        </div>

        <!-- Featured Image -->
        <div class="admin-card" x-data="featuredPreviewer()">
          <h2 class="admin-card-title">Featured Image</h2>
          <p class="admin-card-desc">Upload a new image to replace the current one. Max 10 MB.</p>

          <?php if (!empty($project['featured_image'])): ?>
          <!-- Current image -->
          <div class="admin-featured-current" x-show="!previewUrl">
            <img
              src="<?= e($project['featured_image']) ?>"
              alt="Current featured image"
              class="admin-featured-preview"
              loading="lazy"
            >
            <p class="admin-field-hint" style="margin-top:.5rem;">Current featured image</p>
          </div>
          <?php endif; ?>

          <!-- New image preview -->
          <div
            class="admin-featured-drop"
            :class="{ 'has-preview': previewUrl, 'admin-dropzone-over': dragOver }"
            x-show="previewUrl"
            x-cloak
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            @click="$refs.featuredInput.click()"
          >
            <template x-if="previewUrl">
              <img :src="previewUrl" alt="New featured image preview" class="admin-featured-preview">
            </template>
          </div>

          <!-- Upload trigger when no new preview -->
          <div x-show="!previewUrl" style="margin-top:.75rem;">
            <button
              type="button"
              class="admin-btn admin-btn-ghost admin-btn-sm"
              @click="$refs.featuredInput.click()"
            >
              <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
              <?= !empty($project['featured_image']) ? 'Change Image' : 'Upload Image' ?>
            </button>
          </div>

          <input
            type="file"
            id="featured_image"
            name="featured_image"
            accept="image/jpeg,image/png,image/webp"
            class="admin-file-hidden"
            x-ref="featuredInput"
            @change="handleFile($event.target.files[0])"
          >

          <div x-show="previewUrl" x-cloak class="admin-featured-actions">
            <button type="button" class="admin-btn admin-btn-ghost admin-btn-sm" @click="clearPreview()">
              Cancel change
            </button>
            <span class="admin-featured-filename" x-text="filename"></span>
          </div>

          <p class="admin-field-hint" style="margin-top:.5rem;">
            JPEG, PNG, or WebP. Will be resized to max 2000 px wide.
          </p>
        </div>

      </div>
    </div><!-- /.admin-form-grid -->

  </form>
</div><!-- /.admin-page -->

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>

<script>
function projectForm() {
  return {
    title: <?= json_encode((string)$project['title']) ?>,
    slug:  <?= json_encode((string)$project['slug']) ?>,
    slugEdited: true, // always treat slug as manually set on edit

    updateSlug() {
      if (this.slugEdited) return;
      this.slug = this.generateSlug(this.title);
    },

    generateSlug(text) {
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

function featuredPreviewer() {
  return {
    previewUrl: null,
    filename: '',
    dragOver: false,

    handleFile(file) {
      if (!file) return;
      if (!this.isValidImage(file)) {
        alert('Please select a JPEG, PNG, or WebP image under 10 MB.');
        return;
      }
      this.filename   = file.name;
      this.previewUrl = URL.createObjectURL(file);
    },

    handleDrop(event) {
      this.dragOver = false;
      const file = event.dataTransfer.files[0];
      if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        this.$refs.featuredInput.files = dt.files;
        this.handleFile(file);
      }
    },

    clearPreview() {
      this.previewUrl = null;
      this.filename   = '';
      this.$refs.featuredInput.value = '';
    },

    isValidImage(file) {
      const allowed = ['image/jpeg', 'image/png', 'image/webp'];
      return allowed.includes(file.type) && file.size <= 10 * 1024 * 1024;
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
        if (!this.isValidImage(file)) return;
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

    isValidImage(file) {
      const allowed = ['image/jpeg', 'image/png', 'image/webp'];
      return allowed.includes(file.type) && file.size <= 10 * 1024 * 1024;
    },
  };
}

// Visually mark gallery items that are checked for deletion
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.admin-gallery-delete-check').forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
      const item = this.closest('.admin-gallery-existing-item');
      if (item) {
        item.classList.toggle('marked-for-delete', this.checked);
      }
    });
  });
});
</script>

<style>
/* ---- Edit-page-specific styles ---- */
.admin-gallery-existing {
  display: flex;
  flex-wrap: wrap;
  gap: .75rem;
  margin-bottom: .5rem;
}

.admin-gallery-existing-item {
  position: relative;
  flex-shrink: 0;
}

.admin-gallery-delete-label {
  position: absolute;
  top: 4px;
  right: 4px;
  cursor: pointer;
}

.admin-gallery-delete-check {
  position: absolute;
  opacity: 0;
  width: 0;
  height: 0;
}

.admin-gallery-delete-x {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 22px;
  height: 22px;
  background: rgba(13,5,16,.7);
  border: 1px solid rgba(255,255,255,.15);
  border-radius: 50%;
  color: var(--text-3);
  transition: background .15s, color .15s;
}

.admin-gallery-delete-check:checked + .admin-gallery-delete-x {
  background: var(--red);
  border-color: var(--red);
  color: #fff;
}

.admin-gallery-existing-item.marked-for-delete .admin-gallery-thumb {
  opacity: .35;
  outline: 2px solid var(--red);
  outline-offset: 2px;
}

.admin-featured-current {
  margin-bottom: .75rem;
}

.admin-divider {
  height: 1px;
  background: var(--border);
  margin: 1.25rem 0;
}
</style>

</body>
</html>
