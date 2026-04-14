<?php
/**
 * Admin — Contact Form Submissions
 * Lists all contact form submissions with status management.
 */

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../includes/db.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// ---- POST: Mark as resolved ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'resolve') {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/submissions.php');
        exit;
    }
    $id = (int)($_POST['submission_id'] ?? 0);
    if ($id > 0) {
        $sub_row = $db->prepare("SELECT first_name, last_name FROM contact_submissions WHERE id = ? LIMIT 1");
        $sub_row->execute([$id]);
        $sub = $sub_row->fetch();
        $stmt = $db->prepare("UPDATE contact_submissions SET status = 'resolved' WHERE id = ?");
        $stmt->execute([$id]);
        require_once __DIR__ . '/../includes/activity.php';
        logActivity($db, 'submission_resolve', "Resolved submission #{$id}" . ($sub ? " from {$sub['first_name']} {$sub['last_name']}" : ''));
        $_SESSION['flash_success'] = 'Submission marked as <strong>resolved</strong>.';
    }
    header('Location: /admin/submissions.php');
    exit;
}

// ---- POST: Mark as new (reopen) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reopen') {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/submissions.php');
        exit;
    }
    $id = (int)($_POST['submission_id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE contact_submissions SET status = 'new' WHERE id = ?");
        $stmt->execute([$id]);
        require_once __DIR__ . '/../includes/activity.php';
        logActivity($db, 'submission_reopen', "Reopened submission #{$id}");
        $_SESSION['flash_success'] = 'Submission reopened and marked as <strong>new</strong>.';
    }
    header('Location: /admin/submissions.php');
    exit;
}

// ---- POST: Delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/submissions.php');
        exit;
    }
    $id = (int)($_POST['submission_id'] ?? 0);
    if ($id > 0) {
        $stmt = $db->prepare("UPDATE contact_submissions SET status = 'deleted' WHERE id = ?");
        $stmt->execute([$id]);
        require_once __DIR__ . '/../includes/activity.php';
        logActivity($db, 'submission_delete', "Deleted submission #{$id}");
        $_SESSION['flash_success'] = 'Submission deleted.';
    }
    header('Location: /admin/submissions.php');
    exit;
}

// ---- Fetch all submissions (exclude soft-deleted) ----
$submissions = $db->query("
    SELECT * FROM contact_submissions
    WHERE status != 'deleted'
    ORDER BY created_at DESC
")->fetchAll();

// ---- Stats ----
$total    = count($submissions);
$new      = count(array_filter($submissions, fn($s) => $s['status'] === 'new'));
$resolved = count(array_filter($submissions, fn($s) => $s['status'] === 'resolved'));

// ---- Flash messages ----
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ---- Page meta ----
$admin_page = 'submissions';
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
    <h1 class="page-title">Contact Submissions</h1>
    <p class="page-subtitle">Incoming leads and project enquiries from the contact form.</p>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">

  <div class="stat-card">
    <div class="stat-card-icon" style="background:var(--green-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--green)" stroke-width="1.75">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22 6 12 13 2 6"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--green)"><?= $new ?></div>
    <div class="stat-card-label">New</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background:rgba(255,255,255,0.04)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--text-3)" stroke-width="1.75">
        <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--text-3)"><?= $resolved ?></div>
    <div class="stat-card-label">Resolved</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
        <path d="M16 3.13a4 4 0 010 7.75"/>
      </svg>
    </div>
    <div class="stat-card-number"><?= $total ?></div>
    <div class="stat-card-label">Total</div>
  </div>

</div>

<!-- Submissions Table -->
<div class="card" x-data>

  <div class="card-header">
    <h2 class="card-title">
      All Submissions
      <span style="color:var(--text-3);font-weight:400;font-size:.8125rem;margin-left:.5rem">(<?= $total ?>)</span>
    </h2>
    <?php if ($new > 0): ?>
      <span class="badge badge-published"><?= $new ?> new</span>
    <?php endif; ?>
  </div>

  <?php if (empty($submissions)): ?>
    <div class="empty-state">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22 6 12 13 2 6"/>
      </svg>
      <p>No submissions yet. They'll appear here once visitors fill out the contact form.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="col-num">#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Project Type</th>
            <th>Status</th>
            <th class="col-date">Date</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
          <?php foreach ($submissions as $i => $sub):
            $sid        = (int)$sub['id'];
            $first      = htmlspecialchars($sub['first_name'] ?? '');
            $last       = htmlspecialchars($sub['last_name']  ?? '');
            $email      = htmlspecialchars($sub['email']      ?? '');
            $phone      = htmlspecialchars($sub['phone']      ?? '');
            $ptype      = htmlspecialchars($sub['project_type'] ?? '—');
            $company    = htmlspecialchars($sub['company']    ?? '');
            $location   = htmlspecialchars($sub['location']   ?? '');
            $budget     = htmlspecialchars($sub['budget']     ?? '');
            $timeline   = htmlspecialchars($sub['timeline']   ?? '');
            $message    = htmlspecialchars($sub['message']    ?? '');
            $status     = $sub['status'] ?? 'new';
            $date       = $sub['created_at']
                            ? date('M j, Y', strtotime($sub['created_at']))
                            : '—';
            $detail_id  = 'detail-' . $sid;

            $status_badge = match($status) {
              'new'      => '<span class="badge badge-published">New</span>',
              'resolved' => '<span class="badge badge-hidden">Resolved</span>',
              default    => '<span class="badge badge-hidden">' . htmlspecialchars($status) . '</span>',
            };
          ?>

            <tbody x-data="{ open: false }">
            <!-- Main row -->
            <tr :class="open ? 'row-open' : ''">

              <!-- # -->
              <td class="col-num"><?= $i + 1 ?></td>

              <!-- Name -->
              <td class="col-title">
                <?= $first . ' ' . $last ?>
                <?php if ($company): ?>
                  <small><?= $company ?></small>
                <?php endif; ?>
              </td>

              <!-- Email -->
              <td>
                <a href="mailto:<?= $email ?>"
                   style="color:var(--gold);font-size:.8125rem;word-break:break-all"
                   title="Email <?= $first ?>">
                  <?= $email ?>
                </a>
              </td>

              <!-- Phone -->
              <td>
                <a href="tel:<?= $phone ?>"
                   style="color:var(--text-2);font-size:.8125rem;white-space:nowrap">
                  <?= $phone ?>
                </a>
              </td>

              <!-- Project Type -->
              <td>
                <?php if ($ptype && $ptype !== '—'): ?>
                  <span class="badge badge-type"><?= $ptype ?></span>
                <?php else: ?>
                  <span style="color:var(--text-3);font-size:.8125rem">—</span>
                <?php endif; ?>
              </td>

              <!-- Status -->
              <td><?= $status_badge ?></td>

              <!-- Date -->
              <td class="col-date"><?= $date ?></td>

              <!-- Actions -->
              <td class="col-actions">
                <div class="btn-group">

                  <!-- Expand / collapse details -->
                  <button type="button"
                          class="btn btn-ghost"
                          @click="open = !open"
                          :title="open ? 'Collapse details' : 'View details'"
                          style="font-size:.8125rem">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="transition:transform .2s" :style="open ? 'transform:rotate(180deg)' : ''">
                      <polyline points="6 9 12 15 18 9"/>
                    </svg>
                    <span x-text="open ? 'Collapse' : 'Details'">Details</span>
                  </button>

                  <!-- Mark resolved / reopen -->
                  <?php if ($status === 'new'): ?>
                    <form method="post" action="/admin/submissions.php" style="display:inline">
                      <input type="hidden" name="action"        value="resolve">
                      <input type="hidden" name="submission_id" value="<?= $sid ?>">
                      <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                      <button type="submit" class="btn btn-success" title="Mark as resolved">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Resolve
                      </button>
                    </form>
                  <?php else: ?>
                    <form method="post" action="/admin/submissions.php" style="display:inline">
                      <input type="hidden" name="action"        value="reopen">
                      <input type="hidden" name="submission_id" value="<?= $sid ?>">
                      <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                      <button type="submit" class="btn btn-ghost" title="Reopen as new">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                          <polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/>
                        </svg>
                        Reopen
                      </button>
                    </form>
                  <?php endif; ?>

                  <!-- Delete -->
                  <form method="post" action="/admin/submissions.php" style="display:inline"
                        onsubmit="return confirm('Delete this submission from <?= addslashes($first . ' ' . $last) ?>? This cannot be undone.')">
                    <input type="hidden" name="action"        value="delete">
                    <input type="hidden" name="submission_id" value="<?= $sid ?>">
                    <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                    <button type="submit" class="btn btn-danger" title="Delete submission">
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

            <!-- Detail expansion row -->
            <tr x-show="open" x-cloak style="background:rgba(255,255,255,0.018)">
              <td colspan="8" style="padding:0">
                <div style="
                  padding:1.25rem 1.5rem 1.5rem;
                  border-top:1px solid rgba(255,255,255,0.06);
                  border-bottom:1px solid rgba(255,255,255,0.06);
                  display:grid;
                  grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
                  gap:1.25rem 2rem;
                ">

                  <?php if ($company): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Company</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $company ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($location): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Location</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $location ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($budget): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Budget Range</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $budget ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($timeline): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Timeline</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $timeline ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($message): ?>
                    <div style="grid-column:1 / -1">
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.5rem">Message</div>
                      <div style="
                        font-size:.875rem;
                        color:var(--text-2);
                        line-height:1.65;
                        padding:1rem 1.125rem;
                        background:rgba(0,0,0,0.25);
                        border:1px solid rgba(255,255,255,0.06);
                        border-radius:8px;
                        white-space:pre-wrap;
                      "><?= $message ?></div>
                    </div>
                  <?php endif; ?>

                </div>
              </td>
            </tr>
            </tbody>

          <?php endforeach; ?>
      </table>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
