<?php
/**
 * Admin Projects — /admin/projects.php
 * Full project management: list, toggle status, delete.
 */

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/activity.php';

// ---- CSRF token ----
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// ---- POST: Toggle project published/hidden ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_status'])) {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/projects.php');
        exit;
    }
    $pid    = (int) ($_POST['project_id'] ?? 0);
    $status = ($_POST['current_status'] === 'published') ? 'hidden' : 'published';
    if ($pid > 0) {
        // Fetch title for logging
        $titleStmt = $db->prepare("SELECT title FROM projects WHERE id = ?");
        $titleStmt->execute([$pid]);
        $title = $titleStmt->fetchColumn() ?: 'Unknown';

        $stmt = $db->prepare("UPDATE projects SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $pid]);

        logActivity(
            $db,
            $status === 'published' ? 'project_publish' : 'project_hide',
            "Set \"{$title}\" to {$status}"
        );

        $_SESSION['flash_success'] = 'Project set to <strong>' . ucfirst($status) . '</strong>.';
    }
    header('Location: /admin/projects.php');
    exit;
}

// ---- POST: Delete project ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_project'])) {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/projects.php');
        exit;
    }
    $pid = (int) ($_POST['project_id'] ?? 0);
    if ($pid > 0) {
        // Fetch title BEFORE deleting so we can log it
        $titleStmt = $db->prepare("SELECT title FROM projects WHERE id = ?");
        $titleStmt->execute([$pid]);
        $title = $titleStmt->fetchColumn() ?: 'Unknown';

        $stmt = $db->prepare("DELETE FROM projects WHERE id = :id");
        $stmt->execute([':id' => $pid]);

        logActivity($db, 'project_delete', "Deleted project \"{$title}\"");

        $_SESSION['flash_success'] = 'Project <strong>' . htmlspecialchars($title) . '</strong> deleted.';
    }
    header('Location: /admin/projects.php');
    exit;
}

// ---- Data: project counts ----
$counts = $db->query("
    SELECT COUNT(*) AS total,
        SUM(status = 'published') AS published,
        SUM(status = 'draft')     AS draft,
        SUM(status = 'hidden')    AS hidden
    FROM projects
")->fetch();

// ---- Data: all projects ----
$projects = $db->query("
    SELECT id, title, slug, type, status, sort_order, created_at
    FROM projects ORDER BY sort_order ASC, created_at DESC
")->fetchAll();

// ---- Pull flash messages ----
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ---- Helper: relative time ----
function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return (int)($diff / 60) . 'm ago';
    if ($diff < 86400)  return (int)($diff / 3600) . 'h ago';
    if ($diff < 604800) return (int)($diff / 86400) . 'd ago';
    return date('M j', strtotime($datetime));
}

// ---- Page meta ----
$admin_page = 'projects';
require_once __DIR__ . '/includes/admin-header.php';
?>

<!-- Flash Messages -->
<?php if ($flash_success): ?>
  <div class="flash flash-success" data-auto-dismiss>
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0">
      <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>
    <?= $flash_success ?>
  </div>
<?php endif; ?>
<?php if ($flash_error): ?>
  <div class="flash flash-error" data-auto-dismiss>
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;flex-shrink:0">
      <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
    </svg>
    <?= $flash_error ?>
  </div>
<?php endif; ?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Projects</h1>
    <p class="page-subtitle">Manage your project portfolio</p>
  </div>
  <a href="/admin/project-create.php" class="btn btn-primary">
    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:14px;height:14px">
      <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    Add New Project
  </a>
</div>

<!-- Stats Row (4 columns) -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">

  <!-- Total Projects -->
  <div class="stat-card">
    <div class="stat-card-icon">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <rect x="2" y="3" width="20" height="14" rx="2"/>
        <line x1="8" y1="21" x2="16" y2="21"/>
        <line x1="12" y1="17" x2="12" y2="21"/>
      </svg>
    </div>
    <div class="stat-card-number"><?= (int)($counts['total'] ?? 0) ?></div>
    <div class="stat-card-label">Total Projects</div>
  </div>

  <!-- Published -->
  <div class="stat-card">
    <div class="stat-card-icon" style="background:var(--green-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--green)" stroke-width="1.75">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/>
        <polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--green)"><?= (int)($counts['published'] ?? 0) ?></div>
    <div class="stat-card-label">Published</div>
  </div>

  <!-- Drafts -->
  <div class="stat-card">
    <div class="stat-card-icon" style="background:var(--yellow-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--yellow)" stroke-width="1.75">
        <path d="M12 20h9"/>
        <path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--yellow)"><?= (int)($counts['draft'] ?? 0) ?></div>
    <div class="stat-card-label">Drafts</div>
  </div>

  <!-- Hidden -->
  <div class="stat-card">
    <div class="stat-card-icon" style="background:var(--gray-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--text-3)" stroke-width="1.75">
        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
        <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
        <line x1="1" y1="1" x2="23" y2="23"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--text-3)"><?= (int)($counts['hidden'] ?? 0) ?></div>
    <div class="stat-card-label">Hidden</div>
  </div>

</div>

<!-- Projects Table Card -->
<div class="card">
  <div class="card-header">
    <h2 class="card-title">
      All Projects
      <span style="color:var(--text-3);font-weight:400;font-size:.8125rem;margin-left:.5rem">(<?= count($projects) ?>)</span>
    </h2>
    <div style="display:flex;gap:.5rem;align-items:center">
      <a href="/projects" target="_blank" rel="noopener" class="btn btn-ghost" style="font-size:.8125rem">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px">
          <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
          <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
        View Live
      </a>
      <a href="/admin/project-create.php" class="btn btn-primary" style="padding:.4rem .85rem;font-size:.8125rem">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" style="width:13px;height:13px">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Add Project
      </a>
    </div>
  </div>

  <?php if (empty($projects)): ?>
    <div class="empty-state">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
        <rect x="2" y="3" width="20" height="14" rx="2"/>
        <line x1="8" y1="21" x2="16" y2="21"/>
        <line x1="12" y1="17" x2="12" y2="21"/>
      </svg>
      <p>No projects yet. <a href="/admin/project-create.php" style="color:var(--gold)">Add your first project</a>.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="col-num">#</th>
            <th>Title</th>
            <th>Type</th>
            <th>Status</th>
            <th class="col-date">Date</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projects as $i => $project):
            $slug   = htmlspecialchars($project['slug'] ?? '');
            $title  = htmlspecialchars($project['title'] ?? 'Untitled');
            $type   = htmlspecialchars($project['type'] ?? '');
            $status = $project['status'] ?? 'draft';
            $pid    = (int) $project['id'];
            $sort   = (int) ($project['sort_order'] ?? 0);

            // Date: relative for recent items (< 7 days), absolute otherwise
            $rawDate = $project['created_at'] ?? '';
            if ($rawDate) {
                $diff = time() - strtotime($rawDate);
                $date = ($diff < 604800)
                    ? timeAgo($rawDate)
                    : date('M j, Y', strtotime($rawDate));
            } else {
                $date = '—';
            }

            $status_badge = match($status) {
                'published' => '<span class="badge badge-published">Published</span>',
                'draft'     => '<span class="badge badge-draft">Draft</span>',
                'hidden'    => '<span class="badge badge-hidden">Hidden</span>',
                default     => '<span class="badge badge-hidden">' . htmlspecialchars($status) . '</span>',
            };

            $toggle_label = ($status === 'published') ? 'Hide' : 'Publish';
            $toggle_class = ($status === 'published') ? 'btn btn-ghost' : 'btn btn-success';
          ?>
          <tr>
            <td class="col-num"><?= $sort ?: ($i + 1) ?></td>

            <td class="col-title">
              <?= $title ?>
              <?php if ($slug): ?>
                <small class="font-mono">/projects/<?= $slug ?></small>
              <?php endif; ?>
            </td>

            <td>
              <?php if ($type): ?>
                <span class="badge badge-type"><?= $type ?></span>
              <?php else: ?>
                <span style="color:var(--text-3);font-size:.8125rem">—</span>
              <?php endif; ?>
            </td>

            <td><?= $status_badge ?></td>

            <td class="col-date"><?= $date ?></td>

            <td class="col-actions">
              <div class="btn-group">

                <!-- Edit -->
                <a href="/admin/project-edit.php?id=<?= $pid ?>" class="btn btn-ghost" title="Edit project">
                  <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4L16.5 3.5z"/>
                  </svg>
                  Edit
                </a>

                <!-- View on site (published only) -->
                <?php if ($slug && $status === 'published'): ?>
                  <a href="/projects/<?= $slug ?>" target="_blank" rel="noopener" class="btn-icon" title="View on site">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                      <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                  </a>
                <?php endif; ?>

                <!-- Toggle published/hidden -->
                <form method="post" action="/admin/projects.php" style="display:inline">
                  <input type="hidden" name="csrf_token"     value="<?= htmlspecialchars($csrf) ?>">
                  <input type="hidden" name="toggle_status"  value="1">
                  <input type="hidden" name="project_id"     value="<?= $pid ?>">
                  <input type="hidden" name="current_status" value="<?= htmlspecialchars($status) ?>">
                  <button type="submit" class="<?= $toggle_class ?>" title="<?= $toggle_label ?>">
                    <?php if ($status === 'published'): ?>
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94"/>
                        <path d="M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19"/>
                        <line x1="1" y1="1" x2="23" y2="23"/>
                      </svg>
                      Hide
                    <?php else: ?>
                      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                      </svg>
                      Publish
                    <?php endif; ?>
                  </button>
                </form>

                <!-- Delete -->
                <form method="post" action="/admin/projects.php" style="display:inline"
                      onsubmit="return confirm('Delete \'<?= addslashes($title) ?>\'? This cannot be undone.')">
                  <input type="hidden" name="csrf_token"     value="<?= htmlspecialchars($csrf) ?>">
                  <input type="hidden" name="delete_project" value="1">
                  <input type="hidden" name="project_id"     value="<?= $pid ?>">
                  <button type="submit" class="btn btn-danger" title="Delete project">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <polyline points="3 6 5 6 21 6"/>
                      <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                      <path d="M10 11v6"/><path d="M14 11v6"/>
                      <path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
                    </svg>
                    Delete
                  </button>
                </form>

              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
