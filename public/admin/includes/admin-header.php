<?php
/**
 * Admin Panel — Header / Shell Open
 * Requires: $admin_page (string) — current page slug for active nav state
 * Requires: session started and $_SESSION['admin_user'] available
 */
require_once __DIR__ . '/../../includes/config.php';

$admin_page = $admin_page ?? '';
$admin_username = $_SESSION['user_email'] ?? 'Admin';

$nav_items = [
    'dashboard'   => ['label' => 'Dashboard',   'href' => '/admin/',                'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    'submissions' => ['label' => 'Submissions',  'href' => '/admin/submissions.php', 'icon' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22 6 12 13 2 6"/>'],
    'activity'    => ['label' => 'Activity Log', 'href' => '/admin/activity.php',    'icon' => '<path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($admin_page ? ucfirst($admin_page) . ' — ' : '') ?>Admin Panel — Moksha Construction</title>
  <meta name="robots" content="noindex, nofollow">

  <!-- Favicon -->
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <meta name="theme-color" content="#0d0510">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">

  <!-- Admin Stylesheet -->
  <link rel="stylesheet" href="/admin/assets/admin.css?v=<?= ASSET_VERSION ?>">
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="admin-shell">

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand">
      <a href="/" target="_blank" rel="noopener" title="View main site">
        <img src="/assets/images/branding/logo-full-color.svg"
             alt="Moksha Construction"
             width="160" height="30">
      </a>
    </div>

    <nav class="sidebar-nav" aria-label="Admin navigation">
      <span class="sidebar-nav-label">Main</span>
      <?php foreach ($nav_items as $key => $item): ?>
        <a href="<?= htmlspecialchars($item['href']) ?>"
           class="sidebar-nav-item<?= $admin_page === $key ? ' active' : '' ?>"
           <?= $admin_page === $key ? 'aria-current="page"' : '' ?>>
          <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><?= $item['icon'] ?></svg>
          <?= htmlspecialchars($item['label']) ?>
        </a>
      <?php endforeach; ?>

      <div class="sidebar-nav-divider"></div>
      <span class="sidebar-nav-label">Site</span>

      <a href="/" target="_blank" rel="noopener" class="sidebar-nav-item">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
          <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
          <polyline points="15 3 21 3 21 9"/>
          <line x1="10" y1="14" x2="21" y2="3"/>
        </svg>
        View Site
      </a>
    </nav>

    <div class="sidebar-footer">
      v1.0
    </div>
  </aside>

  <!-- Main Column -->
  <div class="admin-main">

    <!-- Top Bar -->
    <header class="admin-topbar">
      <!-- Mobile sidebar toggle -->
      <button class="sidebar-toggle" onclick="openSidebar()" aria-label="Open navigation">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"/>
          <line x1="3" y1="6"  x2="21" y2="6"/>
          <line x1="3" y1="18" x2="21" y2="18"/>
        </svg>
      </button>

      <div class="admin-topbar-brand">
        <span class="admin-topbar-label" style="border-left:none;padding-left:0">Admin Panel</span>
      </div>

      <div class="admin-topbar-user">
        <span>
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:4px;opacity:.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
          </svg>
          <?= htmlspecialchars($admin_username) ?>
        </span>
        <a href="/admin/logout.php" style="color:var(--text-3);font-size:.8125rem;transition:color .15s;" onmouseover="this.style.color='var(--red)'" onmouseout="this.style.color='var(--text-3)'">
          <svg style="width:14px;height:14px;vertical-align:-2px;margin-right:3px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
            <polyline points="16 17 21 12 16 7"/>
            <line x1="21" y1="12" x2="9" y2="12"/>
          </svg>
          Logout
        </a>
      </div>
    </header>

    <!-- Page Content (admin pages output here) -->
    <div class="admin-content">
