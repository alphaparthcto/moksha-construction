<?php
http_response_code(404);

$page_title       = 'Page Not Found | Moksha Construction';
$page_description = "The page you're looking for doesn't exist.";
$current_page     = '';

require_once __DIR__ . '/includes/header.php';
?>

<section class="min-h-screen bg-base flex items-center justify-center px-6 py-32">
  <div class="max-w-xl w-full text-center">

    <!-- Large 404 -->
    <p class="text-accent-400 font-display font-black leading-none mb-6 select-none"
       style="font-size: clamp(6rem, 20vw, 14rem); letter-spacing: -0.04em; text-shadow: 0 0 60px oklch(88% 0.24 97 / 0.25);">
      404
    </p>

    <!-- Divider -->
    <div class="w-16 h-px bg-accent-400 mx-auto mb-8 opacity-60"></div>

    <!-- Headline -->
    <h1 class="text-[var(--text-h2)] font-display font-bold text-text mb-4">
      This Page Doesn't Exist
    </h1>

    <!-- Subtext -->
    <p class="text-text-2 text-[var(--text-body-lg)] leading-relaxed mb-10 max-w-md mx-auto">
      The page you're looking for may have been moved, deleted, or never existed.
    </p>

    <!-- CTAs -->
    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="/" class="btn-primary">Go Home &rarr;</a>
      <a href="/projects" class="btn-secondary">View Our Projects</a>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
