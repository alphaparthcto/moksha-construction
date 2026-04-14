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
                $errors[] = 'Gallery image #' . ($i + 1) . ': ' . $e->getMessage();
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
          <!-- Drop zone -->
          <div
            style="border:2px dashed rgba(255,255,255,.10);border-radius:12px;padding:2.5rem 1.5rem;text-align:center;cursor:pointer;transition:all .2s;position:relative;overflow:hidden;margin-bottom:1rem;"
            :style="{ borderColor: dragOver ? 'var(--gold)' : '', background: dragOver ? 'rgba(201,162,95,.06)' : '' }"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            @click="$refs.galleryInput.click()"
            role="button"
            tabindex="0"
            @keypress.enter.space.prevent="$refs.galleryInput.click()"
            aria-label="Upload gallery images"
          >
            <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"
                 style="margin:0 auto 1rem;display:block;transition:color .2s"
                 :style="{ color: dragOver ? 'var(--gold)' : 'var(--text-3)' }">
              <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
              <polyline points="17 8 12 3 7 8"/>
              <line x1="12" y1="3" x2="12" y2="15"/>
            </svg>
            <p style="font-size:.9375rem;font-weight:600;color:var(--text);margin-bottom:.25rem;"
               x-text="dragOver ? 'Release to upload' : 'Drag & drop photos here'">Drag & drop photos here</p>
            <p style="font-size:.8125rem;color:var(--text-3);">
              or <span style="color:var(--gold);font-weight:500;">browse files</span>
              <span style="margin:0 .375rem;opacity:.3">·</span>
              Any format, any size
            </p>

            <div x-show="optimizing" x-cloak style="position:absolute;inset:0;background:rgba(13,5,16,.9);display:flex;flex-direction:column;align-items:center;justify-content:center;border-radius:10px;backdrop-filter:blur(4px);">
              <svg style="width:28px;height:28px;color:var(--gold);animation:mokspin .8s linear infinite;margin-bottom:.625rem" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path d="M21 12a9 9 0 11-6.219-8.56"/>
              </svg>
              <span style="color:var(--gold);font-size:.8125rem;font-weight:600;">Optimizing<span x-show="optimizing_count > 0"> (<span x-text="optimizing_count"></span> remaining)</span>...</span>
            </div>
          </div>

          <input
            type="file"
            id="gallery_images"
            name="gallery_images[]"
            accept="image/*,.dng,.cr2,.cr3,.nef,.arw,.raf,.rw2,.orf,.heic,.heif,.tiff,.tif"
            multiple
            style="position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;clip:rect(0,0,0,0);"
            x-ref="galleryInput"
            @change="handleFiles($event.target.files)"
          >

          <!-- Preview grid -->
          <div
            x-show="previews.length > 0"
            x-cloak
            style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1rem;margin-top:1.25rem;"
          >
            <template x-for="(preview, index) in previews" :key="index">
              <div style="position:relative;border-radius:10px;overflow:hidden;background:var(--raised);border:1px solid rgba(255,255,255,.06);">
                <div style="aspect-ratio:4/3;overflow:hidden;">
                  <img :src="preview.url" :alt="'Gallery image ' + (index + 1)" style="width:100%;height:100%;object-fit:cover;display:block;">
                </div>
                <button
                  type="button"
                  style="position:absolute;top:6px;right:6px;width:24px;height:24px;border-radius:50%;background:rgba(0,0,0,.8);border:1px solid rgba(255,255,255,.15);color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;padding:0;transition:background .15s;z-index:2;"
                  @click.prevent="removePreview(index)"
                  :title="'Remove ' + preview.name"
                  @mouseover="$el.style.background='var(--red)'"
                  @mouseout="$el.style.background='rgba(0,0,0,.8)'"
                  aria-label="Remove image"
                >
                  <svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <template x-if="preview.optimized">
                  <span style="position:absolute;top:6px;left:6px;background:rgba(52,211,153,.15);color:var(--green);border:1px solid rgba(52,211,153,.3);font-size:.6rem;font-weight:700;padding:2px 6px;border-radius:4px;backdrop-filter:blur(4px);">OPTIMIZED</span>
                </template>
                <div style="padding:.5rem .625rem;display:flex;flex-direction:column;gap:.2rem;">
                  <span style="font-size:.65rem;color:var(--text-2);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500;" x-text="preview.name"></span>
                  <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <span x-show="preview.sizeMsg" style="font-size:.6rem;color:var(--text-3);" x-text="preview.sizeMsg"></span>
                    <span x-show="preview.dims" style="font-size:.6rem;color:var(--text-3);" x-text="preview.dims"></span>
                  </div>
                </div>
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
            Cover photo shown on the projects listing and detail page. <strong style="color:var(--gold)">Crop it yourself in the popup, then auto-optimized.</strong>
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
            accept="image/*,.dng,.cr2,.cr3,.nef,.arw,.raf,.rw2,.orf,.heic,.heif,.tiff,.tif"
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

          <p class="form-hint" x-show="!previewUrl && !optimizing">
            Any image — JPEG, PNG, WebP, HEIC, drone DNG/RAW.<br>
            <span style="color:var(--gold)">✓ Crop it yourself in the popup · Auto-resized &amp; compressed</span>
          </p>
          <p class="form-hint" x-show="optimizing" x-cloak style="color:var(--gold)">
            <svg style="width:14px;height:14px;display:inline-block;vertical-align:-2px;margin-right:4px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
              <path d="M21 12a9 9 0 11-6.219-8.56" style="animation:mokspin 1s linear infinite;transform-origin:center"/>
            </svg>
            Optimizing image...
          </p>
          <p class="form-hint" x-show="optimizedMsg && !optimizing" x-cloak style="color:var(--green)">
            ✓ <span x-text="optimizedMsg"></span>
          </p>

        </div>
      </div><!-- /.card featured -->

    </div>
    <!-- END RIGHT COLUMN -->

  </div><!-- /grid -->

</form>

<style>
/* ===== Cropper.js modal (Moksha-themed) ===== */
.mok-crop-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,0.85); backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  padding: 1.5rem; animation: mokFadeIn .15s ease-out;
}
@keyframes mokFadeIn { from { opacity: 0 } to { opacity: 1 } }
@keyframes mokspin   { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }
.mok-crop-modal {
  background: var(--raised, #1a1320);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 12px;
  width: 100%; max-width: 900px;
  max-height: 90vh;
  display: flex; flex-direction: column;
  box-shadow: 0 20px 60px rgba(0,0,0,.6);
}
.mok-crop-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,.06);
}
.mok-crop-header h3 { margin: 0; font-size: 1rem; font-weight: 600; color: var(--text); }
.mok-crop-close {
  background: none; border: none; color: var(--text-3); font-size: 2rem;
  line-height: 1; cursor: pointer; padding: 0; width: 32px; height: 32px;
}
.mok-crop-close:hover { color: var(--text); }
.mok-crop-body {
  flex: 1; padding: 1rem; overflow: hidden;
  display: flex; align-items: center; justify-content: center;
  min-height: 50vh; max-height: 65vh;
}
.mok-crop-img { max-width: 100%; max-height: 100%; display: block; }
.mok-crop-footer {
  padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,.06);
  display: flex; align-items: center; justify-content: space-between;
  gap: 1rem; flex-wrap: wrap;
}
.mok-crop-aspect { display: flex; gap: .375rem; flex-wrap: wrap; }
.mok-crop-aspect button {
  background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
  color: var(--text-2); padding: .375rem .75rem; border-radius: 6px;
  font-size: .75rem; cursor: pointer; transition: all .15s; font-weight: 500;
}
.mok-crop-aspect button:hover { color: var(--text); border-color: rgba(255,255,255,.18); }
.mok-crop-aspect button.active { background: var(--gold, #c9a25f); color: #000; border-color: var(--gold, #c9a25f); }
.mok-crop-actions { display: flex; gap: .5rem; }
.mok-crop-cancel, .mok-crop-apply {
  padding: .5rem 1rem; border-radius: 6px; font-size: .8125rem; font-weight: 600;
  cursor: pointer; transition: all .15s; border: 1px solid;
}
.mok-crop-cancel { background: rgba(255,255,255,.04); color: var(--text-2); border-color: rgba(255,255,255,.08); }
.mok-crop-cancel:hover { color: var(--text); }
.mok-crop-apply { background: var(--gold, #c9a25f); color: #000; border-color: var(--gold, #c9a25f); }
.mok-crop-apply:hover { filter: brightness(1.1); }
</style>

<script>
// ============================================================================
// Alpine.js components — project create form
// ============================================================================

const BROWSER_READABLE_MIME = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
function isBrowserReadable(file) { return file && BROWSER_READABLE_MIME.includes(file.type); }

async function optimizeImage(file, maxWidth = 2000, targetBytes = 380 * 1024) {
  if (!isBrowserReadable(file)) return file;
  const dataUrl = await new Promise((res, rej) => {
    const r = new FileReader();
    r.onload = e => res(e.target.result); r.onerror = rej; r.readAsDataURL(file);
  });
  const img = await new Promise((res, rej) => {
    const i = new Image(); i.onload = () => res(i); i.onerror = rej; i.src = dataUrl;
  });
  if (file.size <= targetBytes && img.width <= maxWidth) return file;
  let w = img.width, h = img.height;
  if (w > maxWidth) { h = Math.round(h * (maxWidth / w)); w = maxWidth; }
  const canvas = document.createElement('canvas');
  canvas.width = w; canvas.height = h;
  const ctx = canvas.getContext('2d');
  ctx.fillStyle = '#ffffff'; ctx.fillRect(0, 0, w, h);
  ctx.drawImage(img, 0, 0, w, h);
  let q = 0.85, blob = null;
  for (let i = 0; i < 6; i++) {
    blob = await new Promise(r => canvas.toBlob(r, 'image/jpeg', q));
    if (!blob) break;
    if (blob.size <= targetBytes) break;
    q -= 0.12;
    if (q < 0.35) break;
  }
  if (!blob) return file;
  return new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg', lastModified: Date.now() });
}

function fmtSize(bytes) {
  if (bytes >= 1024 * 1024) return (bytes / 1048576).toFixed(1) + ' MB';
  return Math.round(bytes / 1024) + ' KB';
}

function getImageDims(url) {
  return new Promise(resolve => {
    const img = new Image();
    img.onload  = () => resolve({ w: img.naturalWidth, h: img.naturalHeight });
    img.onerror = () => resolve(null);
    img.src = url;
  });
}

let _cropperLoadingPromise = null;
function ensureCropperLoaded() {
  if (window.Cropper) return Promise.resolve();
  if (_cropperLoadingPromise) return _cropperLoadingPromise;
  _cropperLoadingPromise = new Promise((res, rej) => {
    const css = document.createElement('link');
    css.rel = 'stylesheet';
    css.href = 'https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css';
    document.head.appendChild(css);
    const js = document.createElement('script');
    js.src = 'https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js';
    js.onload = res; js.onerror = rej;
    document.head.appendChild(js);
  });
  return _cropperLoadingPromise;
}

function cropImage(file, aspectRatio = NaN, modalTitle = 'Crop Image') {
  return new Promise((resolve, reject) => {
    if (!isBrowserReadable(file)) { resolve(file); return; }
    ensureCropperLoaded().then(() => {
      const url = URL.createObjectURL(file);
      const overlay = document.createElement('div');
      overlay.className = 'mok-crop-overlay';
      overlay.innerHTML = `
        <div class="mok-crop-modal">
          <div class="mok-crop-header">
            <h3>${modalTitle}</h3>
            <button type="button" class="mok-crop-close" aria-label="Close">&times;</button>
          </div>
          <div class="mok-crop-body"><img class="mok-crop-img" src="${url}" alt=""></div>
          <div class="mok-crop-footer">
            <div class="mok-crop-aspect">
              <button type="button" data-ratio="NaN">Free</button>
              <button type="button" data-ratio="${16/9}">16:9</button>
              <button type="button" data-ratio="${4/3}">4:3</button>
              <button type="button" data-ratio="1">1:1</button>
              <button type="button" data-ratio="${3/2}">3:2</button>
            </div>
            <div class="mok-crop-actions">
              <button type="button" class="mok-crop-cancel">Skip Crop</button>
              <button type="button" class="mok-crop-apply">Apply Crop</button>
            </div>
          </div>
        </div>`;
      document.body.appendChild(overlay);
      const imgEl = overlay.querySelector('.mok-crop-img');
      let cropper;
      imgEl.onload = () => {
        cropper = new Cropper(imgEl, {
          aspectRatio: aspectRatio, viewMode: 1, background: false,
          responsive: true, autoCropArea: 0.95,
        });
        overlay.querySelectorAll('[data-ratio]').forEach(btn => {
          const r = parseFloat(btn.dataset.ratio);
          if ((isNaN(aspectRatio) && btn.dataset.ratio === 'NaN') || r === aspectRatio) btn.classList.add('active');
          btn.addEventListener('click', () => {
            const newR = btn.dataset.ratio === 'NaN' ? NaN : parseFloat(btn.dataset.ratio);
            cropper.setAspectRatio(newR);
            overlay.querySelectorAll('[data-ratio]').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
          });
        });
      };
      const cleanup = () => { if (cropper) cropper.destroy(); URL.revokeObjectURL(url); overlay.remove(); };
      overlay.querySelector('.mok-crop-close').onclick  = () => { cleanup(); reject(new Error('cancelled')); };
      overlay.querySelector('.mok-crop-cancel').onclick = () => { cleanup(); resolve(file); };
      overlay.querySelector('.mok-crop-apply').onclick  = () => {
        if (!cropper) { cleanup(); resolve(file); return; }
        const canvas = cropper.getCroppedCanvas({ maxWidth: 4000, maxHeight: 4000 });
        canvas.toBlob(blob => {
          if (!blob) { cleanup(); resolve(file); return; }
          const newFile = new File([blob], file.name.replace(/\.[^.]+$/, '') + '-cropped.jpg', { type: 'image/jpeg', lastModified: Date.now() });
          cleanup();
          resolve(newFile);
        }, 'image/jpeg', 0.92);
      };
    }).catch(err => { console.error('Cropper failed to load', err); resolve(file); });
  });
}

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
    previewUrl:   null,
    filename:     '',
    dragOver:     false,
    optimizing:   false,
    optimizedMsg: '',

    async handleFile(file) {
      if (!file) return;
      this.optimizing   = true;
      this.optimizedMsg = '';
      this.filename     = file.name;

      try {
        const originalSize = file.size;

        let working = file;
        try {
          working = await cropImage(file, 16/9, 'Crop Featured Image');
        } catch (e) {
          this.optimizing = false; this.filename = ''; return;
        }

        const optimized = await optimizeImage(working);

        const dt = new DataTransfer();
        dt.items.add(optimized);
        this.$refs.featuredInput.files = dt.files;

        if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
        this.previewUrl   = URL.createObjectURL(optimized);
        this.optimizedMsg = optimized === file ? `${fmtSize(originalSize)}` : `${fmtSize(originalSize)} → ${fmtSize(optimized.size)}`;
      } catch (err) {
        console.error(err);
        alert('Could not process image — please try a different file.');
      } finally {
        this.optimizing = false;
      }
    },

    handleDrop(event) {
      this.dragOver = false;
      const file = event.dataTransfer.files[0];
      if (!file) return;
      this.handleFile(file);
    },

    clearPreview() {
      if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
      this.previewUrl   = null;
      this.filename     = '';
      this.optimizedMsg = '';
      this.$refs.featuredInput.value = '';
    },
  };
}

function galleryPreviewer() {
  return {
    previews:         [],
    allFiles:         [],
    dragOver:         false,
    optimizing:       false,
    optimizing_count: 0,

    async handleFiles(fileList) {
      const files = Array.from(fileList);
      if (files.length === 0) return;

      this.optimizing       = true;
      this.optimizing_count = files.length;

      for (const file of files) {
        const originalSize = file.size;
        try {
          let working = file;
          try {
            working = await cropImage(file, NaN, 'Crop Gallery Image (' + file.name + ')');
          } catch (e) {
            this.optimizing_count--;
            continue;
          }

          const optimized = await optimizeImage(working);
          const previewUrl = URL.createObjectURL(optimized);
          const wasOptimized = optimized !== file;
          const dims = await getImageDims(previewUrl);

          this.previews.push({
            url:       previewUrl,
            name:      optimized.name,
            sizeMsg:   wasOptimized ? fmtSize(originalSize) + ' → ' + fmtSize(optimized.size) : fmtSize(originalSize),
            dims:      dims ? dims.w + ' × ' + dims.h + 'px' : '',
            optimized: wasOptimized,
          });
          this.allFiles.push(optimized);
        } catch (err) {
          console.error('Optimize failed for', file.name, err);
        }
        this.optimizing_count--;
      }

      this.optimizing = false;
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
  };
}
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
