<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Maintenance mode check — bypass for admin pages and preview cookie
$isAdmin = str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin');
$hasPreview = isset($_COOKIE['moksha_preview']) && $_COOKIE['moksha_preview'] === '1';

if (!$isAdmin && !$hasPreview) {
    $mStmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode' LIMIT 1");
    $mMode = $mStmt->fetchColumn();
    if ($mMode === '1') {
        http_response_code(503);
        header('Retry-After: 3600');
        readfile(__DIR__ . '/../maintenance.html');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="grain">
<head>
<?php require __DIR__ . '/meta.php'; ?>
<?php require __DIR__ . '/schema.php'; ?>
</head>
<body class="antialiased">

<!-- Header -->
<header class="site-header" id="siteHeader" x-data="mobileMenu()">
  <div class="header-inner">
    <!-- Logo -->
    <a href="/" class="shrink-0" aria-label="Moksha Construction Home">
      <img src="/assets/images/branding/logo-full-color.svg" alt="Moksha Construction" class="h-14 w-auto" width="280" height="40">
    </a>

    <!-- Desktop Nav -->
    <nav class="hidden lg:flex items-center gap-8 ml-auto mr-8" aria-label="Primary navigation">
      <a href="/" class="text-sm font-medium text-text-2 hover:text-text transition-colors <?= ($current_page ?? '') === 'home' ? '!text-accent-400' : '' ?>">Home</a>

      <!-- Services Dropdown -->
      <div class="relative" x-data="dropdown()" @mouseenter="open = true" @mouseleave="open = false">
        <button class="text-sm font-medium text-text-2 hover:text-text transition-colors flex items-center gap-1 <?= str_starts_with($current_page ?? '', 'service') ? '!text-accent-400' : '' ?>" @click="toggle()" aria-expanded="false" aria-haspopup="true">
          Services
          <svg class="w-3.5 h-3.5 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div class="absolute top-full left-1/2 -translate-x-1/2 pt-3 w-72 z-50" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" x-cloak>
          <div class="bg-raised border border-[oklch(100%_0_0/0.08)] rounded-xl p-2 shadow-lg backdrop-blur-xl">
            <a href="/services/general-contracting" class="block px-4 py-2.5 rounded-lg text-sm text-text-2 hover:text-text hover:bg-[oklch(100%_0_0/0.04)] transition-colors">General Contracting</a>
            <a href="/services/construction-management" class="block px-4 py-2.5 rounded-lg text-sm text-text-2 hover:text-text hover:bg-[oklch(100%_0_0/0.04)] transition-colors">Construction Management</a>
            <a href="/services/design-build" class="block px-4 py-2.5 rounded-lg text-sm text-text-2 hover:text-text hover:bg-[oklch(100%_0_0/0.04)] transition-colors">Design & Build</a>
            <a href="/services/residential-commercial-industrial" class="block px-4 py-2.5 rounded-lg text-sm text-text-2 hover:text-text hover:bg-[oklch(100%_0_0/0.04)] transition-colors">Residential · Commercial · Industrial</a>
          </div>
        </div>
      </div>

      <a href="/projects" class="text-sm font-medium text-text-2 hover:text-text transition-colors <?= ($current_page ?? '') === 'projects' ? '!text-accent-400' : '' ?>">Projects</a>
      <a href="/about" class="text-sm font-medium text-text-2 hover:text-text transition-colors <?= ($current_page ?? '') === 'about' ? '!text-accent-400' : '' ?>">About</a>
      <a href="/contact" class="text-sm font-medium text-text-2 hover:text-text transition-colors <?= ($current_page ?? '') === 'contact' ? '!text-accent-400' : '' ?>">Contact</a>
    </nav>

    <!-- Phone + CTA (Desktop) -->
    <div class="hidden lg:flex items-center gap-4">
      <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-sm font-medium text-text-2 hover:text-accent-400 transition-colors" aria-label="Call us">
        <svg class="w-4 h-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
        <?= SITE_PHONE ?>
      </a>
      <a href="/contact#quote" class="btn-primary text-sm">Get a Free Quote <span aria-hidden="true">→</span></a>
    </div>

    <!-- Mobile Menu Toggle -->
    <button class="lg:hidden ml-auto p-2 text-text-2" @click="toggle()" :aria-expanded="open" aria-label="Toggle menu">
      <svg x-show="!open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
      <svg x-show="open" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" x-cloak><path d="M6 18L18 6M6 6l12 12"/></svg>
    </button>
  </div>

  <!-- Mobile Menu Overlay -->
  <div class="lg:hidden fixed inset-0 z-40 bg-void/95 backdrop-blur-xl flex flex-col items-center justify-center gap-6" x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" x-cloak @click.self="close()">
    <a href="/" class="text-2xl font-bold text-text hover:text-accent-400 transition-colors" @click="close()">Home</a>
    <a href="/services/general-contracting" class="text-xl text-text-2 hover:text-accent-400 transition-colors" @click="close()">General Contracting</a>
    <a href="/services/construction-management" class="text-xl text-text-2 hover:text-accent-400 transition-colors" @click="close()">Construction Management</a>
    <a href="/services/design-build" class="text-xl text-text-2 hover:text-accent-400 transition-colors" @click="close()">Design & Build</a>
    <a href="/services/residential-commercial-industrial" class="text-xl text-text-2 hover:text-accent-400 transition-colors" @click="close()">Residential · Commercial · Industrial</a>
    <a href="/projects" class="text-2xl font-bold text-text hover:text-accent-400 transition-colors" @click="close()">Projects</a>
    <a href="/about" class="text-2xl font-bold text-text hover:text-accent-400 transition-colors" @click="close()">About</a>
    <a href="/contact" class="text-2xl font-bold text-text hover:text-accent-400 transition-colors" @click="close()">Contact</a>
    <div class="mt-4 flex flex-col items-center gap-3">
      <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-lg text-accent-400"><?= SITE_PHONE ?></a>
      <a href="/contact#quote" class="btn-primary" @click="close()">Get a Free Quote →</a>
    </div>
  </div>
</header>

<!-- Main Content -->
<main>
