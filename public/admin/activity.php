<?php
/**
 * Admin — Activity Log
 * Shows all admin actions, newest first.
 */

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../includes/db.php';

// ---- Helpers ----

function timeAgo(string $datetime): string {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return (int)($diff / 60) . 'm ago';
    if ($diff < 86400)  return (int)($diff / 3600) . 'h ago';
    if ($diff < 604800) return (int)($diff / 86400) . 'd ago';
    return date('M j', strtotime($datetime));
}

// ---- Fetch activity log ----

try {
    $activities = $db->query("
        SELECT al.*, u.name as user_name, u.email as user_email
        FROM activity_log al
        LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC
        LIMIT 200
    ")->fetchAll();
} catch (PDOException $e) {
    $activities = [];
}

// ---- Page meta ----

$admin_page = 'activity';
require_once __DIR__ . '/includes/admin-header.php';

// ---- Action badge helper ----

function actionBadge(string $action): string {
    $label = ucfirst(str_replace('_', ' ', $action));

    if (in_array($action, ['login', 'logout'], true)) {
        $color = 'var(--purple, #a78bfa)';
        $bg    = 'rgba(167,139,250,0.12)';
        $icon  = '<circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>';
    } elseif (str_starts_with($action, 'project_')) {
        $color = 'var(--gold)';
        $bg    = 'rgba(212,175,100,0.12)';
        $icon  = '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/>';
    } elseif (str_starts_with($action, 'submission_')) {
        $color = 'var(--green)';
        $bg    = 'rgba(52,211,153,0.12)';
        $icon  = '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/>';
    } elseif (str_starts_with($action, 'maintenance_')) {
        $color = 'var(--red)';
        $bg    = 'rgba(248,113,113,0.12)';
        $icon  = '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>';
    } else {
        // settings_* and anything else
        $color = 'var(--text-3)';
        $bg    = 'rgba(255,255,255,0.06)';
        $icon  = '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/>';
    }

    return sprintf(
        '<span style="display:inline-flex;align-items:center;gap:.35rem;padding:.2rem .55rem;border-radius:5px;font-size:.75rem;font-weight:600;letter-spacing:.03em;color:%s;background:%s;white-space:nowrap">'
        . '<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:12px;height:12px;flex-shrink:0">%s</svg>'
        . '%s'
        . '</span>',
        $color, $bg, $icon, htmlspecialchars($label)
    );
}
?>

<!-- Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Activity Log</h1>
    <p class="page-subtitle">Track all admin actions</p>
  </div>
</div>

<!-- Activity Table -->
<div class="card">

  <div class="card-header">
    <h2 class="card-title">
      Recent Actions
      <span style="color:var(--text-3);font-weight:400;font-size:.8125rem;margin-left:.5rem">(last 200)</span>
    </h2>
  </div>

  <?php if (empty($activities)): ?>
    <div class="empty-state">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25">
        <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <p>No activity recorded yet.</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Action</th>
            <th>Details</th>
            <th>User</th>
            <th>IP Address</th>
            <th class="col-date">When</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($activities as $row):
            $action  = $row['action']    ?? '';
            $details = $row['details']   ?? '—';
            $name    = $row['user_name'] ?? ($row['user_email'] ?? 'System');
            $ip      = $row['ip_address'] ?? '—';
            $when    = $row['created_at'] ?? '';
            $full    = $when ? date('D, M j Y \a\t g:i A', strtotime($when)) : '';
          ?>
          <tr>
            <td><?= actionBadge($action) ?></td>
            <td style="font-size:.8125rem;color:var(--text-2);max-width:360px"><?= htmlspecialchars($details) ?></td>
            <td style="font-size:.8125rem;color:var(--text-2);white-space:nowrap"><?= htmlspecialchars($name) ?></td>
            <td style="font-size:.8125rem;color:var(--text-3);font-variant-numeric:tabular-nums;white-space:nowrap"><?= htmlspecialchars($ip) ?></td>
            <td class="col-date">
              <?php if ($when): ?>
                <span title="<?= htmlspecialchars($full) ?>" style="cursor:default">
                  <?= htmlspecialchars(timeAgo($when)) ?>
                </span>
              <?php else: ?>
                —
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
