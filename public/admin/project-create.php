<?php
/**
 * Moksha Construction Admin — Create New Project
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/upload.php';

requireAuth();

$currentUser = getCurrentUser($db);

// ---------------------------------------------------------------------------
// CSRF helpers
// ---------------------------------------------------------------------------

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ---------------------------------------------------------------------------
// POST handler
// ---------------------------------------------------------------------------

$errors = [];
$old    = []; // repopulate form on validation failure

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- CSRF check ---
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Invalid security token. Please go back and try again.');
    }

    // --- Collect & sanitise ---
    $title       = trim($_POST['title']       ?? '');
    $slug        = trim($_POST['slug']        ?? '');
    $type        = trim($_POST['type']        ?? '');
    $size        = trim($_POST['size']        ?? '');
    $location    = trim($_POST['location']    ?? '');
    $yearRaw     = trim($_POST['year'] ?? '');
    $year        = $yearRaw !== '' ? (int)$yearRaw : null;
    $description = trim($_POST['description'] ?? '');
    $status      = trim($_POST['status']      ?? 'draft');

    $old = compact('title', 'slug', 'type', 'size', 'location', 'year', 'description', 'status');

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

    // --- Generate / validate slug ---
    $slug = $slug !== '' ? generateSlug($slug) : generateSlug($title);

    if ($slug === '') {
        $errors[] = 'Could not generate a valid slug from the title.';
    }

    // --- Check slug uniqueness ---
    if (empty($errors)) {
        $stmtCheck = $db->prepare('SELECT COUNT(*) FROM projects WHERE slug = ?');
        $stmtCheck->execute([$slug]);
        if ((int)$stmtCheck->fetchColumn() > 0) {
            // Append a short random suffix and retry once
            $slug .= '-' . substr(bin2hex(random_bytes(3)), 0, 5);
            $stmtCheck->execute([$slug]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                $errors[] = 'A project with this slug already exists. Please edit the slug field and try again.';
            }
        }
        $old['slug'] = $slug;
    }

    // --- Process featured image ---
    $featuredImagePath = null;

    if (empty($errors) && !empty($_FILES['featured_image']['tmp_name'])) {
        $fi = $_FILES['featured_image'];

        if ($fi['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Featured image upload error (code ' . (int)$fi['error'] . '). Please try again.';
        } else {
            try {
                $destDir           = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                $featuredImagePath = processImage($fi['tmp_name'], $destDir, 'hero');
            } catch (RuntimeException $e) {
                $errors[] = 'Featured image: ' . $e->getMessage();
            }
        }
    }

    // --- Process gallery images ---
    $galleryPaths = [];

    if (empty($errors) && !empty($_FILES['gallery_images']['tmp_name'][0])) {
        $gallery = $_FILES['gallery_images'];
        $count   = count($gallery['tmp_name']);

        for ($i = 0; $i < $count; $i++) {
            if ($gallery['error'][$i] !== UPLOAD_ERR_OK) {
                continue; // skip individual failed uploads silently
            }

            $tmpPath = $gallery['tmp_name'][$i] ?? '';
            if ($tmpPath === '') {
                continue;
            }

            try {
                $destDir       = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                $galleryPaths[] = processImage($tmpPath, $destDir, 'gallery-' . ($i + 1));
            } catch (RuntimeException $e) {
                // Non-fatal: log and continue with remaining images
                error_log('Gallery image #' . ($i + 1) . ' skipped: ' . $e->getMessage());
            }
        }
    }

    // --- Save to database ---
    if (empty($errors)) {
        try {
            $db->beginTransaction();

            $stmtInsert = $db->prepare(
                'INSERT INTO projects
                    (title, slug, type, size, location, year, description,
                     featured_image, status, created_by)
                 VALUES
                    (:title, :slug, :type, :size, :location, :year, :description,
                     :featured_image, :status, :created_by)'
            );

            $stmtInsert->execute([
                ':title'          => $title,
                ':slug'           => $slug,
                ':type'           => $type,
                ':size'           => $size !== '' ? $size : null,
                ':location'       => $location !== '' ? $location : null,
                ':year'           => $year,
                ':description'    => $description !== '' ? $description : null,
                ':featured_image' => $featuredImagePath,
                ':status'         => $status,
                ':created_by'     => $currentUser['id'],
            ]);

            $projectId = (int)$db->lastInsertId();

            // Insert gallery rows
            if (!empty($galleryPaths)) {
                $stmtGallery = $db->prepare(
                    'INSERT INTO project_images (project_id, image_path, sort_order)
                     VALUES (:project_id, :image_path, :sort_order)'
                );
                foreach ($galleryPaths as $order => $path) {
                    $stmtGallery->execute([
                        ':project_id' => $projectId,
                        ':image_path' => $path,
                        ':sort_order' => $order,
                    ]);
                }
            }

            $db->commit();

            $_SESSION['flash'] = [
                'type'    => 'success',
                'message' => 'Project "' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '" created successfully.',
            ];

            header('Location: /admin/');
            exit;

        } catch (PDOException $e) {
            $db->rollBack();

            // Roll back any files we already wrote
            if ($featuredImagePath !== null || !empty($galleryPaths)) {
                $projectDir = PUBLIC_ROOT . '/assets/images/projects/' . $slug;
                if (is_dir($projectDir)) {
                    deleteProjectImages($projectDir);
                }
            }

            error_log('Project create DB error: ' . $e->getMessage());
            $errors[] = 'A database error occurred. Please try again.';
        }
    }
}

// ---------------------------------------------------------------------------
// Defaults for GET / failed POST
// ---------------------------------------------------------------------------

$old = array_merge([
    'title'       => '',
    'slug'        => '',
    'type'        => '',
    'size'        => '',
    'location'    => '',
    'year'        => '',
    'description' => '',
    'status'      => 'draft',
], $old);

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
// Page setup for admin-header.php
// ---------------------------------------------------------------------------

$admin_page = 'projects';

require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Add New Project</h1>
    <p class="page-subtitle">Fill in the details below and upload project photos.</p>
  </div>
  <a href="/admin/" class="btn btn-ghost">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path d="M19 12H5M12 19l-7-7 7-7"/>
    </svg>
    Back to Dashboard
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
  action="/admin/project-create.php"
  novalidate
  x-data="projectForm()"
  style="display:contents"
>
  <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">

  <!-- Two-column layout: main fields on left, sidebar on right -->
  <div style="display:grid;grid-template-columns:1fr 300px;gap:1.25rem;align-items:start;">

    <!-- =====================================================================
         LEFT COLUMN
    ====================================================================== -->
    <div>

      <!-- Project Details Card -->
      <div class="card" style="margin-bottom:1.25rem;">
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
              value="<?= e($old['title']) ?>"
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
              <span style="font-weight:400;color:var(--text-3);margin-left:.25rem;">Auto-generated · editable</span>
            </label>
            <div style="display:flex;border:1px solid rgba(255,255,255,.12);border-radius:var(--radius-sm);overflow:hidden;transition:border-color .18s,box-shadow .18s;" :style="slugFocused ? 'border-color:var(--gold);box-shadow:0 0 0 3px var(--gold-glow)' : ''">
              <span style="padding:.625rem .75rem;background:var(--overlay);border-right:1px solid rgba(255,255,255,.08);color:var(--text-3);font-size:.8125rem;white-space:nowrap;display:flex;align-items:center;">/projects/</span>
              <input
                type="text"
                id="slug"
                name="slug"
                style="flex:1;padding:.625rem .875rem;background:var(--raised);border:none;color:var(--text);font-family:inherit;font-size:.9375rem;outline:none;"
                value="<?= e($old['slug']) ?>"
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
              <option value="" disabled<?= $old['type'] === '' ? ' selected' : '' ?>>Select a type&hellip;</option>
              <option value="residential"<?= sel('residential', $old['type']) ?>>Residential</option>
              <option value="commercial"<?= sel('commercial',  $old['type']) ?>>Commercial</option>
              <option value="industrial"<?= sel('industrial',  $old['type']) ?>>Industrial</option>
              <option value="hospitality"<?= sel('hospitality', $old['type']) ?>>Hospitality</option>
              <option value="religious"<?= sel('religious',   $old['type']) ?>>Religious</option>
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
                value="<?= e($old['size']) ?>"
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
                value="<?= e($old['location']) ?>"
                placeholder="e.g. Nashville, TN"
                maxlength="255"
              >
            </div>
          </div>

          <!-- Year -->
          <div class="form-group" style="max-width:160px;margin-top:1rem;">
            <label class="form-label" for="year">Year Completed <span style="font-weight:400;color:var(--text-3)">(optional)</span></label>
            <input
              type="number"
              id="year"
              name="year"
              class="form-input"
              value="<?= e((string)$old['year']) ?>"
              min="1900"
              max="<?= (int)date('Y') + 5 ?>"
              step="1"
            >
          </div>

        </div>
      </div><!-- /.card -->

      <!-- Description Card -->
      <div class="card" style="margin-bottom:1.25rem;">
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
            ><?= e($old['description']) ?></textarea>
          </div>
        </div>
      </div><!-- /.card -->

      <!-- Gallery Card -->
      <div class="card" x-data="galleryPreviewer()">
        <div class="card-header">
          <h2 class="card-title">Gallery Images</h2>
        </div>
        <div class="card-body">
          <p style="font-size:.8125rem;color:var(--text-3);margin-bottom:1rem;line-height:1.6;">
            Upload multiple project photos. JPEG, PNG, or WebP — max 10 MB each.
            Images are automatically resized and converted to WebP.
          </p>

          <!-- Drop zone -->
          <div
            style="border:2px dashed;border-radius:var(--radius-md);padding:2rem 1rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;margin-bottom:1rem;"
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

          <!-- Preview grid -->
          <div
            x-show="previews.length > 0"
            x-cloak
            style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:.75rem;margin-top:.5rem;"
          >
            <template x-for="(preview, index) in previews" :key="index">
              <div style="position:relative;border-radius:var(--radius-sm);overflow:hidden;background:var(--raised);aspect-ratio:1;">
                <img :src="preview.url" :alt="'Gallery image ' + (index + 1)" style="width:100%;height:100%;object-fit:cover;display:block;">
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
      </div><!-- /.card gallery -->

    </div>
    <!-- END LEFT COLUMN -->

    <!-- =====================================================================
         RIGHT COLUMN — sidebar
    ====================================================================== -->
    <div>

      <!-- Publish Settings -->
      <div class="card" style="margin-bottom:1.25rem;">
        <div class="card-header">
          <h2 class="card-title">Publish Settings</h2>
        </div>
        <div class="card-body">

          <p class="form-label" style="margin-bottom:.75rem;">Status</p>

          <!-- Published -->
          <label style="display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;border:1px solid var(--border-md);border-radius:var(--radius-sm);cursor:pointer;margin-bottom:.5rem;transition:background .15s,border-color .15s;background:rgba(255,255,255,.02);"
            :style="status === 'published' ? 'border-color:rgba(34,197,94,.3);background:rgba(34,197,94,.05)' : ''"
          >
            <input type="radio" name="status" value="published"<?= chk('published', $old['status']) ?> style="position:absolute;opacity:0;width:0;height:0;" x-model="status">
            <span style="width:10px;height:10px;border-radius:50%;background:var(--green);flex-shrink:0;"></span>
            <span style="display:flex;flex-direction:column;">
              <strong style="font-size:.875rem;color:var(--text);">Published</strong>
              <small style="font-size:.75rem;color:var(--text-3);">Visible on the public website</small>
            </span>
          </label>

          <!-- Draft -->
          <label style="display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;border:1px solid var(--border-md);border-radius:var(--radius-sm);cursor:pointer;margin-bottom:.5rem;transition:background .15s,border-color .15s;background:rgba(255,255,255,.02);"
            :style="status === 'draft' ? 'border-color:rgba(234,179,8,.3);background:rgba(234,179,8,.05)' : ''"
          >
            <input type="radio" name="status" value="draft"<?= chk('draft', $old['status']) ?> style="position:absolute;opacity:0;width:0;height:0;" x-model="status">
            <span style="width:10px;height:10px;border-radius:50%;background:var(--yellow);flex-shrink:0;"></span>
            <span style="display:flex;flex-direction:column;">
              <strong style="font-size:.875rem;color:var(--text);">Draft</strong>
              <small style="font-size:.75rem;color:var(--text-3);">Saved but not yet live</small>
            </span>
          </label>

          <!-- Hidden -->
          <label style="display:flex;align-items:center;gap:.75rem;padding:.625rem .875rem;border:1px solid var(--border-md);border-radius:var(--radius-sm);cursor:pointer;transition:background .15s,border-color .15s;background:rgba(255,255,255,.02);"
            :style="status === 'hidden' ? 'border-color:rgba(255,255,255,.2);background:rgba(255,255,255,.04)' : ''"
          >
            <input type="radio" name="status" value="hidden"<?= chk('hidden', $old['status']) ?> style="position:absolute;opacity:0;width:0;height:0;" x-model="status">
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
              Save Project
            </button>
            <a href="/admin/" class="btn btn-ghost" style="width:100%;justify-content:center;">Cancel</a>
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

          <!-- Drop / preview area -->
          <div
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
              <img :src="previewUrl" alt="Featured image preview" style="width:100%;height:100%;object-fit:cover;display:block;">
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

          <!-- Actions once a file is selected -->
          <div x-show="previewUrl" x-cloak style="display:flex;align-items:center;gap:.75rem;margin-bottom:.5rem;">
            <button
              type="button"
              class="btn btn-ghost"
              style="padding:.375rem .75rem;font-size:.8125rem;"
              @click="clearPreview()"
            >
              Remove
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
// Alpine.js components — project create form
// ============================================================================

function projectForm() {
  return {
    title: <?= json_encode($old['title']) ?>,
    slug:  <?= json_encode($old['slug']) ?>,
    slugEdited:  <?= json_encode($old['slug'] !== '') ?>,
    slugFocused: false,
    status: <?= json_encode($old['status']) ?>,

    onTitleInput() {
      if (!this.slugEdited) {
        this.slug = this.toSlug(this.title);
      }
    },

    toSlug(text) {
      return text
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')   // strip diacritics
        .replace(/[^a-z0-9\s\-]/g, '')     // remove special chars
        .trim()
        .replace(/[\s\-]+/g, '-')          // spaces → hyphens
        .replace(/^-+|-+$/g, '');          // trim edges
    },
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
    previews:  [],
    allFiles:  [],
    dragOver:  false,

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
