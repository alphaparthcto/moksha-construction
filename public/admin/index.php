<?php
/**
 * Admin Dashboard — /admin/
 * High-level site overview: stats, maintenance toggle, recent submissions, activity log.
 */

require_once __DIR__ . '/../includes/auth.php';
requireAuth();

require_once __DIR__ . '/../includes/db.php';

// ---- CSRF token ----
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];

// ---- POST: Maintenance mode toggle ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'maintenance_mode') {
    if (!hash_equals($csrf, $_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid security token.';
        header('Location: /admin/');
        exit;
    }
    $value = isset($_POST['maintenance_mode']) ? '1' : '0';
    $stmt = $db->prepare("
        INSERT INTO settings (setting_key, setting_value)
        VALUES ('maintenance_mode', :val)
        ON DUPLICATE KEY UPDATE setting_value = :val2
    ");
    $stmt->execute([':val' => $value, ':val2' => $value]);
    $label = $value === '1' ? 'enabled' : 'disabled';
    $_SESSION['flash_success'] = "Maintenance mode {$label}.";
    header('Location: /admin/');
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

// ---- Data: submission counts ----
try {
    $subCounts = $db->query("
        SELECT COUNT(*) AS total,
            SUM(status = 'new')      AS unread,
            SUM(status = 'resolved') AS resolved
        FROM contact_submissions WHERE status != 'deleted'
    ")->fetch();
} catch (PDOException $e) {
    $subCounts = ['total' => 0, 'unread' => 0, 'resolved' => 0];
}

// ---- Data: subcontractor application counts ----
try {
    $scCounts = $db->query("
        SELECT COUNT(*) AS total,
            SUM(status = 'new')       AS new,
            SUM(status = 'reviewing') AS reviewing,
            SUM(status = 'approved')  AS approved
        FROM subcontractor_submissions WHERE status != 'deleted'
    ")->fetch();
} catch (PDOException $e) {
    $scCounts = ['total' => 0, 'new' => 0, 'reviewing' => 0, 'approved' => 0];
}

// ---- Data: maintenance mode ----
$stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode' LIMIT 1");
$stmt->execute();
$maintenance_on = (($stmt->fetch())['setting_value'] ?? '0') === '1';

// ---- Data: recent activity (latest 15) ----
try {
    $recentActivity = $db->query("
        SELECT al.*, u.name as user_name
        FROM activity_log al LEFT JOIN users u ON al.user_id = u.id
        ORDER BY al.created_at DESC LIMIT 15
    ")->fetchAll();
} catch (PDOException $e) {
    $recentActivity = [];
}

// ---- Data: recent unread submissions (latest 5) ----
try {
    $recentSubs = $db->query("
        SELECT * FROM contact_submissions
        WHERE status = 'new' ORDER BY created_at DESC LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    $recentSubs = [];
}

// ---- Data: recent subcontractor applications (latest 5) ----
try {
    $recentSubcontractors = $db->query("
        SELECT * FROM subcontractor_submissions
        WHERE status = 'new' ORDER BY created_at DESC LIMIT 5
    ")->fetchAll();
} catch (PDOException $e) {
    $recentSubcontractors = [];
}

// ---- Data: total activity count ----
try {
    $activityTotal = (int) $db->query("SELECT COUNT(*) FROM activity_log")->fetchColumn();
} catch (PDOException $e) {
    $activityTotal = 0;
}

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
$admin_page = 'dashboard';
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

<!-- Section 1: Page Header -->
<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Site overview and analytics</p>
  </div>
</div>

<!-- Section 2: Stats Grid (7 tiles) -->
<div class="stats-grid" style="grid-template-columns:repeat(7,1fr)">

  <!-- 1. Total Projects -->
  <a href="/admin/projects.php" class="stat-card" style="text-decoration:none;display:flex;flex-direction:column">
    <div class="stat-card-icon">
      <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
        <rect x="2" y="3" width="20" height="14" rx="2"/>
        <line x1="8" y1="21" x2="16" y2="21"/>
        <line x1="12" y1="17" x2="12" y2="21"/>
      </svg>
    </div>
    <div class="stat-card-number"><?= (int)($counts['total'] ?? 0) ?></div>
    <div class="stat-card-label">Total Projects</div>
  </a>

  <!-- 2. Published -->
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

  <!-- 3. New Submissions -->
  <a href="/admin/submissions.php" class="stat-card" style="text-decoration:none;display:flex;flex-direction:column">
    <div class="stat-card-icon" style="background:var(--green-dim)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--green)" stroke-width="1.75">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
        <polyline points="22 6 12 13 2 6"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--green)">
      <span style="display:inline-flex;align-items:center;gap:6px">
        <?= (int)($subCounts['unread'] ?? 0) ?>
        <?php if (($subCounts['unread'] ?? 0) > 0): ?>
          <span style="width:8px;height:8px;border-radius:50%;background:var(--green);animation:pulse 2s infinite"></span>
        <?php endif; ?>
      </span>
    </div>
    <div class="stat-card-label">New Submissions</div>
  </a>

  <!-- 4. Resolved -->
  <div class="stat-card">
    <div class="stat-card-icon" style="background:rgba(255,255,255,.04)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--text-3)" stroke-width="1.75">
        <path d="M9 11l3 3L22 4"/>
        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--text-3)"><?= (int)($subCounts['resolved'] ?? 0) ?></div>
    <div class="stat-card-label">Resolved</div>
  </div>

  <!-- 4b. Subcontractor Applications -->
  <a href="/admin/subcontractors.php" class="stat-card" style="text-decoration:none;display:flex;flex-direction:column">
    <div class="stat-card-icon" style="background:rgba(212,175,100,.12)">
      <svg fill="none" viewBox="0 0 24 24" stroke="var(--gold)" stroke-width="1.75">
        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
        <circle cx="9" cy="7" r="4"/>
        <path d="M23 21v-2a4 4 0 00-3-3.87"/>
        <path d="M16 3.13a4 4 0 010 7.75"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:var(--gold)">
      <span style="display:inline-flex;align-items:center;gap:6px">
        <?= (int)($scCounts['new'] ?? 0) ?>
        <?php if (($scCounts['new'] ?? 0) > 0): ?>
          <span style="width:8px;height:8px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite"></span>
        <?php endif; ?>
      </span>
    </div>
    <div class="stat-card-label">Subcontractor Apps</div>
  </a>

  <!-- 5. Activity Events -->
  <a href="/admin/activity.php" class="stat-card" style="text-decoration:none;display:flex;flex-direction:column">
    <div class="stat-card-icon" style="background:rgba(139,92,246,.12)">
      <svg fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" stroke-width="1.75">
        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
      </svg>
    </div>
    <div class="stat-card-number" style="color:#8b5cf6"><?= $activityTotal ?></div>
    <div class="stat-card-label">Activity Events</div>
  </a>

  <!-- 6. Maintenance Mode -->
  <div class="stat-card">
    <div class="stat-card-icon" style="background:<?= $maintenance_on ? 'rgba(239,68,68,.12)' : 'rgba(255,255,255,.04)' ?>">
      <?php if ($maintenance_on): ?>
        <svg fill="none" viewBox="0 0 24 24" stroke="#ef4444" stroke-width="1.75">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/>
          <line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      <?php else: ?>
        <svg fill="none" viewBox="0 0 24 24" stroke="var(--text-3)" stroke-width="1.75">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
      <?php endif; ?>
    </div>
    <div class="stat-card-number" style="color:<?= $maintenance_on ? '#ef4444' : 'var(--text-3)' ?>;font-size:1.25rem;letter-spacing:.05em">
      <?= $maintenance_on ? 'ON' : 'OFF' ?>
    </div>
    <div class="stat-card-label">Maintenance</div>
  </div>

</div>

<!-- Section 3: Maintenance Mode Toggle Card -->
<form method="post" action="/admin/" id="maintenanceForm">
  <input type="hidden" name="action"     value="maintenance_mode">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">

  <div class="maintenance-card<?= $maintenance_on ? ' active' : '' ?>" id="maintenanceCard">
    <div class="maintenance-icon" id="maintenanceIcon">
      <?php if ($maintenance_on): ?>
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
          <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
        </svg>
      <?php else: ?>
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
          <circle cx="12" cy="12" r="10"/>
          <polyline points="12 6 12 12 16 14"/>
        </svg>
      <?php endif; ?>
    </div>

    <div class="maintenance-info">
      <div class="maintenance-title">Maintenance Mode</div>
      <div class="maintenance-desc">
        <?php if ($maintenance_on): ?>
          Site is currently in maintenance mode — visitors see the maintenance page.
        <?php else: ?>
          Site is live and accessible to all visitors.
        <?php endif; ?>
      </div>
    </div>

    <div class="toggle-wrap" style="flex-shrink:0">
      <span class="toggle-label"><?= $maintenance_on ? 'On' : 'Off' ?></span>
      <label style="cursor:pointer;display:flex;align-items:center;gap:.5rem">
        <input
          type="checkbox"
          name="maintenance_mode"
          class="toggle-input"
          id="maintenanceToggle"
          <?= $maintenance_on ? 'checked' : '' ?>
          onchange="this.form.submit()">
        <span class="toggle-track" aria-hidden="true"></span>
      </label>
      <span class="toggle-status" id="maintenanceStatus"><?= $maintenance_on ? 'ACTIVE' : 'INACTIVE' ?></span>
    </div>
  </div>
</form>

<!-- Section 4: Two-column layout — Recent Submissions + Activity Log -->
<div class="dash-two-col" style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">

  <!-- LEFT: Recent Submissions -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">
        New Submissions
        <span style="color:var(--text-3);font-weight:400;font-size:.8125rem;margin-left:.5rem">(<?= (int)($subCounts['unread'] ?? 0) ?>)</span>
      </h2>
      <a href="/admin/submissions.php" class="btn btn-ghost" style="font-size:.8125rem;white-space:nowrap">
        View All →
      </a>
    </div>

    <?php if (empty($recentSubs)): ?>
      <div class="card-body">
        <div class="empty-state" style="padding:1.5rem 0">
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25" style="width:32px;height:32px">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22 6 12 13 2 6"/>
          </svg>
          <p style="font-size:.875rem">No new submissions.</p>
        </div>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column">
        <?php foreach ($recentSubs as $idx => $sub):
          $sName  = htmlspecialchars(trim(($sub['first_name'] ?? '') . ' ' . ($sub['last_name'] ?? '')));
          $sEmail = htmlspecialchars($sub['email'] ?? '');
          $sType  = htmlspecialchars($sub['project_type'] ?? '');
          $sTime  = timeAgo($sub['created_at'] ?? '');
        ?>
          <?php if ($idx > 0): ?><div class="divider"></div><?php endif; ?>
          <a href="/admin/submissions.php"
             style="display:flex;flex-direction:column;gap:.25rem;padding:1rem 1.25rem;text-decoration:none;transition:background .15s"
             onmouseover="this.style.background='rgba(255,255,255,.03)'"
             onmouseout="this.style.background='transparent'">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem">
              <span style="font-size:.875rem;font-weight:500;color:var(--text-1)"><?= $sName ?: 'Anonymous' ?></span>
              <span style="font-size:.75rem;color:var(--text-3);white-space:nowrap;flex-shrink:0"><?= $sTime ?></span>
            </div>
            <?php if ($sEmail): ?>
              <span style="font-size:.75rem;color:var(--text-3)"><?= $sEmail ?></span>
            <?php endif; ?>
            <?php if ($sType): ?>
              <span style="font-size:.75rem;color:var(--text-3)"><?= $sType ?></span>
            <?php endif; ?>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- RIGHT: Recent Activity -->
  <div class="card">
    <div class="card-header">
      <h2 class="card-title">Activity Log</h2>
      <a href="/admin/activity.php" class="btn btn-ghost" style="font-size:.8125rem;white-space:nowrap">
        View All →
      </a>
    </div>

    <?php if (empty($recentActivity)): ?>
      <div class="card-body">
        <p style="font-size:.875rem;color:var(--text-3);text-align:center;padding:.5rem 0">No activity recorded yet.</p>
      </div>
    <?php else: ?>
      <div style="display:flex;flex-direction:column;padding:1rem 1.25rem;gap:0">
        <?php foreach ($recentActivity as $idx => $entry):
          $aDesc  = htmlspecialchars($entry['details'] ?? $entry['action'] ?? 'Action recorded');
          $aUser  = htmlspecialchars($entry['user_name'] ?? 'System');
          $aTime  = timeAgo($entry['created_at'] ?? '');
          $isLast = $idx === count($recentActivity) - 1;
        ?>
          <div style="display:flex;gap:.875rem;<?= $isLast ? '' : 'padding-bottom:1rem;' ?>">
            <!-- Timeline dot + line -->
            <div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0">
              <div style="width:8px;height:8px;border-radius:50%;background:var(--gold);margin-top:.3rem;flex-shrink:0"></div>
              <?php if (!$isLast): ?>
                <div style="width:1px;background:var(--border);flex:1;margin-top:.25rem"></div>
              <?php endif; ?>
            </div>
            <!-- Content -->
            <div style="display:flex;flex-direction:column;gap:.15rem;padding-bottom:<?= $isLast ? '0' : '.25rem' ?>">
              <span style="font-size:.8125rem;color:var(--text-1);line-height:1.4"><?= $aDesc ?></span>
              <span style="font-size:.75rem;color:var(--text-3)">
                <?= $aUser ?> &middot; <?= $aTime ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</div>

<!-- Section 4b: Recent Subcontractor Applications -->
<div class="card" style="margin-top:1.5rem">
  <div class="card-header">
    <h2 class="card-title">
      New Subcontractor Applications
      <span style="color:var(--text-3);font-weight:400;font-size:.8125rem;margin-left:.5rem">(<?= (int)($scCounts['new'] ?? 0) ?>)</span>
    </h2>
    <a href="/admin/subcontractors.php" class="btn btn-ghost" style="font-size:.8125rem;white-space:nowrap">View All →</a>
  </div>

  <?php if (empty($recentSubcontractors)): ?>
    <div class="card-body">
      <div class="empty-state" style="padding:1.5rem 0">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.25" style="width:32px;height:32px">
          <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
          <circle cx="9" cy="7" r="4"/>
          <path d="M23 21v-2a4 4 0 00-3-3.87"/>
          <path d="M16 3.13a4 4 0 010 7.75"/>
        </svg>
        <p style="font-size:.875rem">No new subcontractor applications.</p>
      </div>
    </div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column">
      <?php foreach ($recentSubcontractors as $idx => $sc):
        $cName  = htmlspecialchars($sc['company_name'] ?? '—');
        $pName  = htmlspecialchars(trim(($sc['first_name'] ?? '') . ' ' . ($sc['last_name'] ?? '')));
        $cTrade = htmlspecialchars($sc['trade'] ?? '');
        $cTime  = timeAgo($sc['created_at'] ?? '');
      ?>
        <?php if ($idx > 0): ?><div class="divider"></div><?php endif; ?>
        <a href="/admin/subcontractors.php"
           style="display:flex;flex-direction:column;gap:.25rem;padding:1rem 1.25rem;text-decoration:none;transition:background .15s"
           onmouseover="this.style.background='rgba(255,255,255,.03)'"
           onmouseout="this.style.background='transparent'">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem">
            <span style="font-size:.875rem;font-weight:500;color:var(--text-1)"><?= $cName ?></span>
            <span style="font-size:.75rem;color:var(--text-3);white-space:nowrap;flex-shrink:0"><?= $cTime ?></span>
          </div>
          <div style="display:flex;gap:.75rem;font-size:.75rem;color:var(--text-3)">
            <?php if ($pName): ?><span><?= $pName ?></span><?php endif; ?>
            <?php if ($cTrade): ?><span style="color:var(--gold)"><?= $cTrade ?></span><?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Section 5: Quick Links -->
<div class="dash-quick-links" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-top:1.5rem">

  <a href="/admin/project-create.php" class="card"
     style="padding:1.25rem;text-align:center;text-decoration:none;transition:background .15s,border-color .15s"
     onmouseover="this.style.borderColor='var(--gold)'"
     onmouseout="this.style.borderColor=''">
    <div style="display:flex;justify-content:center;margin-bottom:.625rem">
      <div style="width:36px;height:36px;border-radius:8px;background:rgba(201,162,95,.12);display:flex;align-items:center;justify-content:center">
        <svg fill="none" viewBox="0 0 24 24" stroke="var(--gold)" stroke-width="2" style="width:18px;height:18px">
          <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
      </div>
    </div>
    <div style="font-size:.875rem;font-weight:500;color:var(--text-1)">Add Project</div>
  </a>

  <a href="/admin/projects.php" class="card"
     style="padding:1.25rem;text-align:center;text-decoration:none;transition:background .15s,border-color .15s"
     onmouseover="this.style.borderColor='#8b5cf6'"
     onmouseout="this.style.borderColor=''">
    <div style="display:flex;justify-content:center;margin-bottom:.625rem">
      <div style="width:36px;height:36px;border-radius:8px;background:rgba(139,92,246,.12);display:flex;align-items:center;justify-content:center">
        <svg fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" stroke-width="2" style="width:18px;height:18px">
          <rect x="2" y="3" width="20" height="14" rx="2"/>
          <line x1="8" y1="21" x2="16" y2="21"/>
          <line x1="12" y1="17" x2="12" y2="21"/>
        </svg>
      </div>
    </div>
    <div style="font-size:.875rem;font-weight:500;color:var(--text-1)">View Projects</div>
  </a>

  <a href="/admin/submissions.php" class="card"
     style="padding:1.25rem;text-align:center;text-decoration:none;transition:background .15s,border-color .15s"
     onmouseover="this.style.borderColor='var(--green)'"
     onmouseout="this.style.borderColor=''">
    <div style="display:flex;justify-content:center;margin-bottom:.625rem">
      <div style="width:36px;height:36px;border-radius:8px;background:var(--green-dim);display:flex;align-items:center;justify-content:center">
        <svg fill="none" viewBox="0 0 24 24" stroke="var(--green)" stroke-width="2" style="width:18px;height:18px">
          <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
          <polyline points="22 6 12 13 2 6"/>
        </svg>
      </div>
    </div>
    <div style="font-size:.875rem;font-weight:500;color:var(--text-1)">View Submissions</div>
  </a>

  <a href="/" target="_blank" rel="noopener" class="card"
     style="padding:1.25rem;text-align:center;text-decoration:none;transition:background .15s,border-color .15s"
     onmouseover="this.style.borderColor='var(--border-2)'"
     onmouseout="this.style.borderColor=''">
    <div style="display:flex;justify-content:center;margin-bottom:.625rem">
      <div style="width:36px;height:36px;border-radius:8px;background:rgba(255,255,255,.05);display:flex;align-items:center;justify-content:center">
        <svg fill="none" viewBox="0 0 24 24" stroke="var(--text-3)" stroke-width="2" style="width:18px;height:18px">
          <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
          <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
      </div>
    </div>
    <div style="font-size:.875rem;font-weight:500;color:var(--text-1)">View Site</div>
  </a>

</div>

<style>
@keyframes pulse { 0%,100% { opacity:1 } 50% { opacity:.3 } }

@media (max-width: 1200px) {
  .stats-grid { grid-template-columns: repeat(4, 1fr) !important; }
}

@media (max-width: 900px) {
  .stats-grid { grid-template-columns: repeat(3, 1fr) !important; }
  .dash-two-col { grid-template-columns: 1fr !important; }
  .dash-quick-links { grid-template-columns: repeat(2, 1fr) !important; }
}

@media (max-width: 480px) {
  .stats-grid { grid-template-columns: repeat(2, 1fr) !important; }
  .dash-quick-links { grid-template-columns: repeat(2, 1fr) !important; }
}
</style>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
