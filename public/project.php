<?php
// ============================================================
// PROJECT DETAIL PAGE
// Route: /projects/{slug} → /project.php?slug={slug}
// ============================================================

require_once __DIR__ . '/includes/db.php'; // also loads config.php

$slug = trim($_GET['slug'] ?? '');

// ── Fetch project ─────────────────────────────────────────
$stmt = $db->prepare('SELECT * FROM projects WHERE slug = ? AND status = "published" LIMIT 1');
$stmt->execute([$slug]);
$project = $stmt->fetch();

// ── 404 branch ────────────────────────────────────────────
if (!$project) {
    http_response_code(404);
    $page_title       = 'Project Not Found | Moksha Construction';
    $page_description = 'The project you\'re looking for doesn\'t exist or has been removed.';
    $page_url         = '/projects';
    $current_page     = 'projects';
    require_once __DIR__ . '/includes/header.php';
    ?>

    <section class="min-h-screen bg-base flex items-center justify-center px-6 py-32">
      <div class="max-w-xl w-full text-center">

        <p class="text-accent-400 font-display font-black leading-none mb-6 select-none"
           style="font-size: clamp(5rem, 16vw, 10rem); letter-spacing: -0.04em; text-shadow: 0 0 60px oklch(88% 0.24 97 / 0.25);">
          404
        </p>

        <div class="w-16 h-px bg-accent-400 mx-auto mb-8 opacity-60"></div>

        <h1 class="text-[length:var(--text-h2)] font-display font-bold text-text mb-4">
          Project Not Found
        </h1>
        <p class="text-text-2 text-[length:var(--text-body-lg)] leading-relaxed mb-10 max-w-md mx-auto">
          The project you're looking for may have been moved, removed, or never existed.
        </p>

        <div class="flex flex-wrap items-center justify-center gap-4">
          <a href="/projects" class="btn-primary">View All Projects &rarr;</a>
          <a href="/" class="btn-secondary">Go Home</a>
        </div>

      </div>
    </section>

    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

// ── Fetch gallery images ───────────────────────────────────
$img_stmt = $db->prepare(
    'SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, id ASC'
);
$img_stmt->execute([$project['id']]);
$gallery_images = $img_stmt->fetchAll();

// ── Fetch related projects (same type, excluding this one) ─
$rel_stmt = $db->prepare(
    'SELECT * FROM projects
      WHERE type = ? AND status = "published" AND id != ?
      ORDER BY sort_order ASC, year DESC
      LIMIT 3'
);
$rel_stmt->execute([$project['type'], $project['id']]);
$related = $rel_stmt->fetchAll();

// ── Type label map ─────────────────────────────────────────
$type_labels = [
    'residential'  => 'Residential',
    'commercial'   => 'Commercial',
    'industrial'   => 'Industrial',
    'hospitality'  => 'Hospitality',
    'religious'    => 'Religious',
];
$type_label = $type_labels[$project['type']] ?? ucfirst($project['type']);

// ── Page meta ─────────────────────────────────────────────
$page_title       = htmlspecialchars($project['title']) . ' | Moksha Construction';
$page_description = mb_substr(strip_tags($project['description'] ?? ''), 0, 160);
$page_url         = '/projects/' . $project['slug'];
$current_page     = 'projects';

// Use featured image as OG image if it's a relative path we can reference
$page_image = !empty($project['featured_image'])
    ? preg_replace('#^/?assets/images/#', '', $project['featured_image'])
    : 'og-home.jpg';

$breadcrumbs = [
    ['name' => 'Home',     'url' => '/'],
    ['name' => 'Projects', 'url' => '/projects'],
    ['name' => $project['title']],
];

// ── Build gallery JSON for Alpine ─────────────────────────
$gallery_json = json_encode(array_map(function ($img) {
    return [
        'src' => $img['image_path'],
        'alt' => $img['alt_text'] ?? '',
    ];
}, $gallery_images), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);

require_once __DIR__ . '/includes/header.php';
?>

  <!-- ============================================================
       HERO — full-width project featured image
  ============================================================ -->
  <section class="relative min-h-[70vh] flex items-end pb-20 pt-40 overflow-hidden">

    <!-- Background: featured image -->
    <div class="absolute inset-0">
      <?php if (!empty($project['featured_image'])): ?>
        <img
          src="<?= htmlspecialchars($project['featured_image'], ENT_QUOTES, 'UTF-8') ?>"
          alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') ?>"
          class="w-full h-full object-cover"
          fetchpriority="high"
        >
      <?php else: ?>
        <!-- Fallback gradient when no featured image is set -->
        <div class="w-full h-full bg-gradient-to-br from-brand-900 via-base to-accent-950"></div>
      <?php endif; ?>

      <!-- Gradient overlays for text legibility -->
      <div class="absolute inset-0 bg-gradient-to-t from-base via-base/65 to-base/10"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-base/70 to-transparent"></div>
      <!-- Ambient glow -->
      <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full bg-brand-600/20 blur-3xl pointer-events-none"></div>
    </div>

    <!-- Breadcrumb -->
    <div class="absolute top-28 left-0 right-0">
      <div class="max-w-[var(--container)] mx-auto px-6">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-text-3">
          <a href="/" class="hover:text-accent-400 transition-colors">Home</a>
          <span aria-hidden="true">/</span>
          <a href="/projects" class="hover:text-accent-400 transition-colors">Projects</a>
          <span aria-hidden="true">/</span>
          <span class="text-text-2"><?= htmlspecialchars($project['title']) ?></span>
        </nav>
      </div>
    </div>

    <!-- Hero content -->
    <div class="relative max-w-[var(--container)] mx-auto px-6">
      <p class="section-label reveal">
        <?= htmlspecialchars(strtoupper($type_label)) ?> PROJECT
      </p>

      <h1 class="text-[length:var(--text-hero)] font-bold tracking-tight mb-5 reveal reveal-delay-1">
        <?= htmlspecialchars($project['title']) ?>
      </h1>

      <!-- Project quick-facts row -->
      <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-text-2 reveal reveal-delay-2">
        <!-- Type badge -->
        <span class="project-badge static-badge relative"><?= htmlspecialchars($type_label) ?></span>

        <?php if (!empty($project['size'])): ?>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-accent-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
            <?= htmlspecialchars($project['size']) ?>
          </span>
        <?php endif; ?>

        <?php if (!empty($project['location'])): ?>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-accent-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
            <?= htmlspecialchars($project['location']) ?>
          </span>
        <?php endif; ?>

        <?php if (!empty($project['year'])): ?>
          <span class="flex items-center gap-1.5">
            <svg class="w-4 h-4 text-accent-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            <?= htmlspecialchars((string)$project['year']) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- ============================================================
       PROJECT INFO — description + details sidebar
  ============================================================ -->
  <section class="py-(--section-y) bg-subtle">
    <div class="max-w-[var(--container)] mx-auto px-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-16 items-start">

        <!-- Left: Full description (2/3 width) -->
        <div class="lg:col-span-2 reveal">
          <p class="section-label">ABOUT THIS PROJECT</p>
          <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight mb-6">
            <?= htmlspecialchars($project['title']) ?>
          </h2>

          <?php if (!empty($project['description'])): ?>
            <div class="prose-project text-text-2 leading-relaxed space-y-4">
              <?php
              // Render as HTML (stored as HTML in DB) — already trusted CMS content.
              // Strip any actual script tags as a safety measure.
              $desc = preg_replace('#<script[^>]*>.*?</script>#is', '', $project['description']);
              echo $desc;
              ?>
            </div>
          <?php endif; ?>

          <!-- Back link -->
          <div class="mt-10">
            <a href="/projects" class="btn-ghost inline-flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
              </svg>
              Back to All Projects
            </a>
          </div>
        </div>

        <!-- Right: Details sidebar (1/3 width) -->
        <div class="reveal reveal-delay-1">
          <div class="bg-raised border border-[oklch(100%_0_0/0.07)] rounded-2xl p-6 lg:p-8 sticky top-28">
            <h3 class="text-sm font-semibold text-text uppercase tracking-widest mb-6">
              Project Details
            </h3>

            <dl class="space-y-5">

              <!-- Type -->
              <div>
                <dt class="text-xs text-text-3 uppercase tracking-widest mb-1.5">Type</dt>
                <dd>
                  <span class="project-badge static-badge"><?= htmlspecialchars($type_label) ?></span>
                </dd>
              </div>

              <?php if (!empty($project['size'])): ?>
              <!-- Size -->
              <div>
                <dt class="text-xs text-text-3 uppercase tracking-widest mb-1.5">Size</dt>
                <dd class="text-text font-medium"><?= htmlspecialchars($project['size']) ?></dd>
              </div>
              <?php endif; ?>

              <?php if (!empty($project['location'])): ?>
              <!-- Location -->
              <div>
                <dt class="text-xs text-text-3 uppercase tracking-widest mb-1.5">Location</dt>
                <dd class="text-text font-medium"><?= htmlspecialchars($project['location']) ?></dd>
              </div>
              <?php endif; ?>

              <?php if (!empty($project['year'])): ?>
              <!-- Year -->
              <div>
                <dt class="text-xs text-text-3 uppercase tracking-widest mb-1.5">Year Completed</dt>
                <dd class="text-text font-medium"><?= htmlspecialchars((string)$project['year']) ?></dd>
              </div>
              <?php endif; ?>

            </dl>

            <!-- Divider -->
            <div class="my-6 h-px bg-[oklch(100%_0_0/0.07)]"></div>

            <!-- CTA -->
            <p class="text-xs text-text-3 leading-relaxed mb-4">
              Interested in a similar project? Let's talk.
            </p>
            <a href="/contact#quote" class="btn-primary w-full text-center block">
              Get a Free Quote <span aria-hidden="true">&rarr;</span>
            </a>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
       IMAGE GALLERY — Alpine.js lightbox
  ============================================================ -->
  <?php if (!empty($gallery_images)): ?>
  <section
    class="py-(--section-y)"
    x-data="projectGallery(<?= htmlspecialchars($gallery_json, ENT_QUOTES, 'UTF-8') ?>)"
    @keydown.escape.window="close()"
    @keydown.arrow-right.window="open && next()"
    @keydown.arrow-left.window="open && prev()"
  >
    <div class="max-w-[var(--container)] mx-auto px-6">

      <p class="section-label reveal">GALLERY</p>
      <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight mb-10 reveal reveal-delay-1">
        Project <em class="font-accent not-italic text-accent-400">Photography</em>
      </h2>

      <!-- Gallery grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($gallery_images as $i => $img): ?>
          <button
            type="button"
            class="group relative overflow-hidden rounded-xl aspect-[4/3] bg-raised focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400 focus-visible:ring-offset-2 focus-visible:ring-offset-base reveal"
            @click="show(<?= (int)$i ?>)"
            aria-label="Open image <?= (int)($i + 1) ?> in lightbox"
          >
            <img
              src="<?= htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8') ?>"
              alt="<?= htmlspecialchars($img['alt_text'] ?? $project['title'] . ' — image ' . ($i + 1), ENT_QUOTES, 'UTF-8') ?>"
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
            >
            <!-- Hover overlay -->
            <div class="absolute inset-0 bg-base/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
              <div class="w-12 h-12 rounded-full bg-accent-400/90 flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-[oklch(15%_0.06_97)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607zM10.5 7.5v6m3-3h-6"/>
                </svg>
              </div>
            </div>
          </button>
        <?php endforeach; ?>
      </div>

    </div>

    <!-- ── Lightbox modal ── -->
    <div
      x-show="open"
      x-cloak
      role="dialog"
      aria-modal="true"
      aria-label="Image lightbox"
      class="fixed inset-0 z-50 flex items-center justify-center"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-void/95 backdrop-blur-sm"
        @click="close()"
        aria-hidden="true"
      ></div>

      <!-- Modal panel -->
      <div class="relative w-full max-w-5xl mx-4 lg:mx-8 flex flex-col items-center">

        <!-- Close button -->
        <button
          type="button"
          @click="close()"
          class="absolute -top-12 right-0 text-text-2 hover:text-text transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400 rounded"
          aria-label="Close lightbox"
        >
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>

        <!-- Image container -->
        <div class="relative w-full rounded-xl overflow-hidden bg-raised shadow-2xl">
          <template x-for="(img, i) in images" :key="i">
            <img
              x-show="current === i"
              :src="img.src"
              :alt="img.alt"
              class="w-full max-h-[80vh] object-contain"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 scale-98"
              x-transition:enter-end="opacity-100 scale-100"
            >
          </template>
        </div>

        <!-- Prev / Next + counter -->
        <div class="flex items-center gap-6 mt-5">
          <button
            type="button"
            @click="prev()"
            class="w-10 h-10 rounded-full border border-[oklch(100%_0_0/0.12)] flex items-center justify-center text-text-2 hover:border-accent-400 hover:text-accent-400 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400"
            aria-label="Previous image"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
            </svg>
          </button>

          <span class="text-sm text-text-3 tabular-nums min-w-[4ch] text-center" x-text="(current + 1) + ' / ' + images.length"></span>

          <button
            type="button"
            @click="next()"
            class="w-10 h-10 rounded-full border border-[oklch(100%_0_0/0.12)] flex items-center justify-center text-text-2 hover:border-accent-400 hover:text-accent-400 transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-accent-400"
            aria-label="Next image"
          >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
            </svg>
          </button>
        </div>

        <!-- Alt text caption -->
        <p
          class="mt-3 text-xs text-text-3 text-center max-w-lg"
          x-text="images[current]?.alt"
          x-show="images[current]?.alt"
        ></p>

      </div>
    </div><!-- /lightbox -->

  </section>
  <?php endif; ?>

  <!-- ============================================================
       RELATED PROJECTS
  ============================================================ -->
  <?php if (!empty($related)): ?>
  <section class="py-(--section-y) bg-subtle">
    <div class="max-w-[var(--container)] mx-auto px-6">

      <p class="section-label reveal">MORE LIKE THIS</p>
      <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight mb-10 reveal reveal-delay-1">
        Related <em class="font-accent not-italic text-accent-400">Projects</em>
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-<?= count($related) === 1 ? '1 max-w-lg' : (count($related) === 2 ? '2' : '3') ?> gap-6">
        <?php foreach ($related as $i => $rel):
          $rel_label = $type_labels[$rel['type']] ?? ucfirst($rel['type']);
          $delay_class = $i === 0 ? '' : ($i === 1 ? 'reveal-delay-1' : 'reveal-delay-2');
        ?>
          <a
            href="/projects/<?= htmlspecialchars($rel['slug'], ENT_QUOTES, 'UTF-8') ?>"
            class="project-card group"
            style="aspect-ratio: 4/3;"
            aria-label="View project: <?= htmlspecialchars($rel['title'], ENT_QUOTES, 'UTF-8') ?>"
          >
            <?php if (!empty($rel['featured_image'])): ?>
              <img
                src="<?= htmlspecialchars($rel['featured_image'], ENT_QUOTES, 'UTF-8') ?>"
                alt="<?= htmlspecialchars($rel['title'], ENT_QUOTES, 'UTF-8') ?>"
                class="w-full h-full object-cover"
                loading="lazy"
              >
            <?php else: ?>
              <div class="w-full h-full bg-gradient-to-br from-brand-900 to-base"></div>
            <?php endif; ?>

            <span class="project-badge"><?= htmlspecialchars($rel_label) ?></span>

            <div class="project-card-overlay">
              <h3 class="text-[length:var(--text-h3)] font-bold text-text mb-1">
                <?= htmlspecialchars($rel['title']) ?>
              </h3>
              <p class="text-sm text-text-3">
                <?php
                  $meta_parts = [];
                  if (!empty($rel['size']))     $meta_parts[] = htmlspecialchars($rel['size']);
                  if (!empty($rel['location'])) $meta_parts[] = htmlspecialchars($rel['location']);
                  echo implode(' &middot; ', $meta_parts);
                ?>
              </p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="mt-10 text-center reveal">
        <a href="/projects" class="btn-ghost inline-flex items-center gap-2">
          View All Projects
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
          </svg>
        </a>
      </div>

    </div>
  </section>
  <?php endif; ?>

  <!-- ============================================================
       CTA BANNER
  ============================================================ -->
  <?php require __DIR__ . '/includes/cta-banner.php'; ?>

<script>
/**
 * projectGallery — Alpine.js component for the image lightbox.
 * @param {Array} images — array of {src, alt} objects from PHP.
 */
function projectGallery(images) {
  return {
    images: images || [],
    current: 0,
    open: false,

    show(index) {
      this.current = index;
      this.open = true;
      // Prevent body scroll while lightbox is open
      document.body.style.overflow = 'hidden';
      // Move focus to the lightbox on next tick
      this.$nextTick(() => {
        const dialog = this.$el.querySelector('[role="dialog"]');
        if (dialog) dialog.focus();
      });
    },

    close() {
      this.open = false;
      document.body.style.overflow = '';
    },

    next() {
      this.current = (this.current + 1) % this.images.length;
    },

    prev() {
      this.current = (this.current - 1 + this.images.length) % this.images.length;
    },
  };
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
