<?php
/**
 * Admin — Subcontractor Applications
 * Lists all subcontractor applications with status management.
 */

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../includes/db.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// ---- Helper: status update ----
function updateSubStatus(PDO $db, int $id, string $status, string $action, string $verb): void {
    $row = $db->prepare("SELECT first_name, last_name, company_name FROM subcontractor_submissions WHERE id = ? LIMIT 1");
    $row->execute([$id]);
    $sub = $row->fetch();

    $stmt = $db->prepare("UPDATE subcontractor_submissions SET status = ?, reviewed_at = NOW() WHERE id = ?");
    $stmt->execute([$status, $id]);

    require_once __DIR__ . '/../includes/activity.php';
    $details = "{$verb} subcontractor #{$id}";
    if ($sub) {
        $name = trim(($sub['first_name'] ?? '') . ' ' . ($sub['last_name'] ?? ''));
        $details .= " — {$sub['company_name']}" . ($name ? " ({$name})" : '');
    }
    logActivity($db, $action, $details);
}

// ---- POST handlers ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['submission_id'] ?? 0);

    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/subcontractors.php');
        exit;
    }

    if ($id > 0) {
        switch ($action) {
            case 'review':
                updateSubStatus($db, $id, 'reviewing', 'subcontractor_review', 'Marked reviewing');
                $_SESSION['flash_success'] = 'Application moved to <strong>reviewing</strong>.';
                break;
            case 'approve':
                updateSubStatus($db, $id, 'approved', 'subcontractor_approve', 'Approved');
                $_SESSION['flash_success'] = 'Application <strong>approved</strong>.';
                break;
            case 'reject':
                updateSubStatus($db, $id, 'rejected', 'subcontractor_reject', 'Rejected');
                $_SESSION['flash_success'] = 'Application <strong>rejected</strong>.';
                break;
            case 'reopen':
                updateSubStatus($db, $id, 'new', 'subcontractor_reopen', 'Reopened');
                $_SESSION['flash_success'] = 'Application reopened as <strong>new</strong>.';
                break;
            case 'delete':
                updateSubStatus($db, $id, 'deleted', 'subcontractor_delete', 'Deleted');
                $_SESSION['flash_success'] = 'Application deleted.';
                break;
        }
    }
    header('Location: /admin/subcontractors.php');
    exit;
}

// ---- Fetch all submissions (exclude soft-deleted) ----
try {
    $submissions = $db->query("
        SELECT * FROM subcontractor_submissions
        WHERE status != 'deleted'
        ORDER BY created_at DESC
    ")->fetchAll();
} catch (PDOException $e) {
    $submissions = [];
}

// ---- Stats ----
$total     = count($submissions);
$new       = count(array_filter($submissions, fn($s) => $s['status'] === 'new'));
$reviewing = count(array_filter($submissions, fn($s) => $s['status'] === 'reviewing'));
$approved  = count(array_filter($submissions, fn($s) => $s['status'] === 'approved'));
$rejected  = count(array_filter($submissions, fn($s) => $s['status'] === 'rejected'));

// ---- Flash messages ----
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// ---- Page meta ----
$admin_page = 'subcontractors';
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
    <h1 class="page-title">Subcontractor Applications</h1>
    <p class="page-subtitle">Trade partner applications submitted through the website.</p>
  </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid" style="grid-template-columns:repeat(5,1fr)">

  <div class="stat-card">
    <div class="stat-card-icon" style="background:var(--green-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--green)" stroke-width="1.75">
        <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--green)"><?= $new ?></div>
    <div class="stat-card-label">New</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background:rgba(139,92,246,.12)">
      <svg fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" stroke-width="1.75">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:#8b5cf6"><?= $reviewing ?></div>
    <div class="stat-card-label">Reviewing</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background:var(--green-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--green)" stroke-width="1.75">
        <polyline points="20 6 9 17 4 12"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--green)"><?= $approved ?></div>
    <div class="stat-card-label">Approved</div>
  </div>

  <div class="stat-card">
    <div class="stat-card-icon" style="background:rgba(248,113,113,.12)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--red)" stroke-width="1.75">
        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--red)"><?= $rejected ?></div>
    <div class="stat-card-label">Rejected</div>
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
      All Applications
      <span style="color:var(--text-3);font-weight:400;font-size:.8125rem;margin-left:.5rem">(<?= $total ?>)</span>
    </h2>
    <?php if ($new > 0): ?>
      <span class="badge badge-published"><?= $new ?> new</span>
    <?php endif; ?>
  </div>

  <?php if (empty($submissions)): ?>
    <div class="empty-state">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
        <path d="M16 3.13a4 4 0 010 7.75"/>
      </svg>
      <p>No subcontractor applications yet. They'll appear here once trade partners apply.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="col-num">#</th>
            <th>Company</th>
            <th>Contact</th>
            <th>Trade</th>
            <th>Status</th>
            <th class="col-date">Date</th>
            <th class="col-actions">Actions</th>
          </tr>
        </thead>
          <?php foreach ($submissions as $i => $sub):
            $sid          = (int)$sub['id'];
            $first        = htmlspecialchars($sub['first_name']   ?? '');
            $last         = htmlspecialchars($sub['last_name']    ?? '');
            $email        = htmlspecialchars($sub['email']        ?? '');
            $phone        = htmlspecialchars($sub['phone']        ?? '');
            $company      = htmlspecialchars($sub['company_name'] ?? '');
            $website      = htmlspecialchars($sub['website']      ?? '');
            $years        = htmlspecialchars($sub['years_in_business'] ?? '');
            $size         = htmlspecialchars($sub['company_size']  ?? '');
            $trade        = htmlspecialchars($sub['trade']         ?? '—');
            $tradeOther   = htmlspecialchars($sub['trades_other']  ?? '');
            $states       = htmlspecialchars($sub['states_licensed'] ?? '');
            $area         = htmlspecialchars($sub['service_area']  ?? '');
            $license      = htmlspecialchars($sub['license_number'] ?? '');
            $insured      = htmlspecialchars($sub['insured']       ?? '');
            $bonded       = htmlspecialchars($sub['bonded']        ?? '');
            $emr          = htmlspecialchars($sub['emr_rating']    ?? '');
            $union        = htmlspecialchars($sub['union_status']  ?? '');
            $interest     = htmlspecialchars($sub['project_types_interest'] ?? '');
            $largest      = htmlspecialchars($sub['largest_project'] ?? '');
            $references   = htmlspecialchars($sub['references_text'] ?? '');
            $message      = htmlspecialchars($sub['message']       ?? '');
            $status       = $sub['status'] ?? 'new';
            $date         = $sub['created_at']
                              ? date('M j, Y', strtotime($sub['created_at']))
                              : '—';

            $status_badge = match($status) {
              'new'       => '<span class="badge badge-published">New</span>',
              'reviewing' => '<span class="badge" style="background:rgba(139,92,246,.15);color:#a78bfa;border:1px solid rgba(139,92,246,.3)">Reviewing</span>',
              'approved'  => '<span class="badge" style="background:rgba(52,211,153,.15);color:var(--green);border:1px solid rgba(52,211,153,.3)">Approved</span>',
              'rejected'  => '<span class="badge" style="background:rgba(248,113,113,.15);color:var(--red);border:1px solid rgba(248,113,113,.3)">Rejected</span>',
              default     => '<span class="badge badge-hidden">' . htmlspecialchars($status) . '</span>',
            };
          ?>

            <tbody x-data="{ open: false }">
            <!-- Main row -->
            <tr :class="open ? 'row-open' : ''">

              <td class="col-num"><?= $i + 1 ?></td>

              <td class="col-title">
                <?= $company ?: '—' ?>
                <?php if ($website): ?>
                  <small><a href="<?= $website ?>" target="_blank" rel="noopener" style="color:var(--text-3)"><?= $website ?></a></small>
                <?php endif; ?>
              </td>

              <td>
                <div style="font-size:.8125rem;color:var(--text)"><?= $first . ' ' . $last ?></div>
                <a href="mailto:<?= $email ?>" style="color:var(--gold);font-size:.75rem;word-break:break-all"><?= $email ?></a>
              </td>

              <td>
                <span class="badge badge-type"><?= $trade ?></span>
              </td>

              <td><?= $status_badge ?></td>

              <td class="col-date"><?= $date ?></td>

              <td class="col-actions">
                <div class="btn-group">

                  <!-- Expand / collapse -->
                  <button type="button" class="btn btn-ghost" @click="open = !open" :title="open ? 'Collapse' : 'View details'" style="font-size:.8125rem">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="transition:transform .2s" :style="open ? 'transform:rotate(180deg)' : ''">
                      <polyline points="6 9 12 15 18 9"/>
                    </svg>
                    <span x-text="open ? 'Collapse' : 'Details'">Details</span>
                  </button>

                  <!-- Status actions -->
                  <?php if ($status === 'new'): ?>
                    <form method="post" action="/admin/subcontractors.php" style="display:inline">
                      <input type="hidden" name="action"        value="review">
                      <input type="hidden" name="submission_id" value="<?= $sid ?>">
                      <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                      <button type="submit" class="btn btn-ghost" title="Mark as reviewing" style="color:#a78bfa">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        Review
                      </button>
                    </form>
                  <?php endif; ?>

                  <?php if (in_array($status, ['new', 'reviewing'], true)): ?>
                    <form method="post" action="/admin/subcontractors.php" style="display:inline">
                      <input type="hidden" name="action"        value="approve">
                      <input type="hidden" name="submission_id" value="<?= $sid ?>">
                      <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                      <button type="submit" class="btn btn-success" title="Approve">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        Approve
                      </button>
                    </form>

                    <form method="post" action="/admin/subcontractors.php" style="display:inline">
                      <input type="hidden" name="action"        value="reject">
                      <input type="hidden" name="submission_id" value="<?= $sid ?>">
                      <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                      <button type="submit" class="btn btn-danger" title="Reject">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Reject
                      </button>
                    </form>
                  <?php else: ?>
                    <form method="post" action="/admin/subcontractors.php" style="display:inline">
                      <input type="hidden" name="action"        value="reopen">
                      <input type="hidden" name="submission_id" value="<?= $sid ?>">
                      <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                      <button type="submit" class="btn btn-ghost" title="Reopen">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 102.13-9.36L1 10"/></svg>
                        Reopen
                      </button>
                    </form>
                  <?php endif; ?>

                  <!-- Delete -->
                  <form method="post" action="/admin/subcontractors.php" style="display:inline"
                        onsubmit="return confirm('Delete application from <?= addslashes($company) ?>? This cannot be undone.')">
                    <input type="hidden" name="action"        value="delete">
                    <input type="hidden" name="submission_id" value="<?= $sid ?>">
                    <input type="hidden" name="csrf_token"    value="<?= htmlspecialchars($csrf) ?>">
                    <button type="submit" class="btn btn-danger" title="Delete">
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

            <!-- Detail row -->
            <tr x-show="open" x-cloak style="background:rgba(255,255,255,0.018)">
              <td colspan="7" style="padding:0">
                <div style="
                  padding:1.25rem 1.5rem 1.5rem;
                  border-top:1px solid rgba(255,255,255,0.06);
                  border-bottom:1px solid rgba(255,255,255,0.06);
                  display:grid;
                  grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
                  gap:1.25rem 2rem;
                ">

                  <div>
                    <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Phone</div>
                    <div style="font-size:.875rem;color:var(--text)"><?= $phone ?: '—' ?></div>
                  </div>

                  <?php if ($years): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Years in Business</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $years ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($size): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Company Size</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $size ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($tradeOther): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Trade (specified)</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $tradeOther ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($states): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">States Licensed</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $states ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($area): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Service Area</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $area ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($license): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">License #</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $license ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($insured): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Insured</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= ucfirst(str_replace('_', ' ', $insured)) ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($bonded): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Bonded</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= ucfirst($bonded) ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($emr): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">EMR Rating</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $emr ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($union): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Union Status</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= ucfirst(str_replace('_', '-', $union)) ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($interest): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Project Types of Interest</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $interest ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($largest): ?>
                    <div>
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.3rem">Largest Project</div>
                      <div style="font-size:.875rem;color:var(--text)"><?= $largest ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($references): ?>
                    <div style="grid-column:1 / -1">
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.5rem">References</div>
                      <div style="font-size:.875rem;color:var(--text-2);line-height:1.65;padding:.875rem 1rem;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.06);border-radius:8px;white-space:pre-wrap"><?= $references ?></div>
                    </div>
                  <?php endif; ?>

                  <?php if ($message): ?>
                    <div style="grid-column:1 / -1">
                      <div style="font-size:.6875rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:var(--text-3);margin-bottom:.5rem">Message</div>
                      <div style="font-size:.875rem;color:var(--text-2);line-height:1.65;padding:1rem 1.125rem;background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.06);border-radius:8px;white-space:pre-wrap"><?= $message ?></div>
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
