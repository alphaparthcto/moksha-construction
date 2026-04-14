<?php
require_once __DIR__ . '/includes/db.php';

$page_title       = 'Our Projects | Moksha Construction Portfolio | Clarksville & Nashville, TN';
$page_description = 'View Moksha Construction\'s project portfolio — from 280,000 sq ft exhibition centers to luxury hotels and apartment complexes. See our work across Tennessee and the Southeast.';
$page_url         = '/projects';
$current_page     = 'projects';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Projects'],
];

$stmt     = $db->query('SELECT * FROM projects WHERE status = "published" ORDER BY sort_order ASC, created_at DESC');
$projects = $stmt->fetchAll();
$types    = array_unique(array_column($projects, 'type'));

require_once __DIR__ . '/includes/header.php';
?>

  <!-- ============================================================
       HERO
  ============================================================ -->
  <section class="relative min-h-[60vh] flex items-end pb-20 pt-40 overflow-hidden">
    <!-- Background image -->
    <div class="absolute inset-0">
      <img
        src="/assets/images/projects/exhibition/main.png"
        alt="Moksha Construction project portfolio"
        class="w-full h-full object-cover"
        fetchpriority="high"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-base via-base/70 to-base/20"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-base/60 to-transparent"></div>
    </div>

    <!-- Breadcrumb -->
    <div class="absolute top-28 left-0 right-0">
      <div class="max-w-[var(--container)] mx-auto px-6">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-text-3">
          <a href="/" class="hover:text-accent-400 transition-colors">Home</a>
          <span aria-hidden="true">/</span>
          <span class="text-text-2">Projects</span>
        </nav>
      </div>
    </div>

    <div class="relative max-w-[var(--container)] mx-auto px-6">
      <p class="section-label reveal">OUR PORTFOLIO</p>
      <h1 class="text-[length:var(--text-hero)] font-bold tracking-tight mb-6 reveal reveal-delay-1">
        Our Work Speaks<br>
        <em class="font-accent not-italic text-accent-400">for Itself</em>
      </h1>
      <p class="text-[length:var(--text-body-lg)] text-text-2 max-w-2xl reveal reveal-delay-2">
        Hotels, exhibition centers, office buildings, retail spaces, and residential complexes — built by Moksha Construction across Tennessee and the Southeast.
      </p>
    </div>
  </section>

  <!-- ============================================================
       FILTER BAR + PROJECT GRID
  ============================================================ -->
  <section class="py-(--section-y)" x-data="projectFilter()">
    <div class="max-w-[var(--container)] mx-auto px-6">

      <!-- Filter Bar -->
      <div class="flex flex-wrap items-center justify-center gap-3 mb-14 reveal">
        <template x-for="filter in filters" :key="filter.value">
          <button
            @click="activeFilter = filter.value"
            :class="activeFilter === filter.value
              ? 'bg-accent-400 text-[oklch(15%_0.06_97)] border-accent-400'
              : 'bg-transparent text-text-2 border-[oklch(100%_0_0/0.12)] hover:border-accent-400 hover:text-accent-400'"
            class="px-5 py-2 text-sm font-semibold border rounded-full transition-all duration-200 tracking-wide"
            :aria-pressed="activeFilter === filter.value"
            x-text="filter.label"
          ></button>
        </template>
      </div>

      <?php if (empty($projects)): ?>

      <!-- Empty state: no projects in DB yet -->
      <div class="text-center py-20">
        <p class="text-text-3 text-[length:var(--text-body-lg)]">Projects coming soon.</p>
      </div>

      <?php else: ?>

      <!-- Project Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <?php foreach ($projects as $i => $project):
          $isLast     = $i === count($projects) - 1;
          $isOddTotal = count($projects) % 2 !== 0;
          $isWide     = $isLast && $isOddTotal;
        ?>
        <div
          class="project-card group <?= $isWide ? 'md:col-span-2' : '' ?>"
          data-type="<?= htmlspecialchars($project['type']) ?>"
          x-show="activeFilter === 'all' || activeFilter === '<?= htmlspecialchars($project['type']) ?>'"
          x-transition:enter="transition ease-out duration-400"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          style="aspect-ratio: <?= $isWide ? '21/9' : '4/3' ?>;"
        >
          <a href="/projects/<?= htmlspecialchars($project['slug']) ?>" class="block w-full h-full">
            <img
              src="<?= htmlspecialchars($project['featured_image'] ?? '/assets/images/projects/placeholder.jpg') ?>"
              alt="<?= htmlspecialchars($project['title']) ?> — <?= htmlspecialchars($project['size'] ?? '') ?> <?= htmlspecialchars($project['type']) ?> project<?= $project['location'] ? ' in ' . htmlspecialchars($project['location']) : '' ?>"
              class="w-full h-full object-cover"
              loading="lazy"
            >
            <span class="project-badge"><?= htmlspecialchars(ucfirst($project['type'])) ?></span>
            <div class="project-card-overlay">
              <h2 class="text-[length:var(--text-h3)] font-bold text-text mb-1"><?= htmlspecialchars($project['title']) ?></h2>
              <p class="text-sm text-text-3 mb-3">
                <?= $project['size'] ? htmlspecialchars($project['size']) . ' &middot; ' : '' ?><?= htmlspecialchars($project['location'] ?? '') ?>
              </p>
              <p class="text-sm text-text-2 leading-relaxed max-h-0 overflow-hidden opacity-0 translate-y-2 transition-all duration-300 group-hover:max-h-40 group-hover:opacity-100 group-hover:translate-y-0 max-w-lg">
                <?= htmlspecialchars(mb_substr(strip_tags($project['description'] ?? ''), 0, 200)) ?>
              </p>
            </div>
          </a>
        </div>
        <?php endforeach; ?>

      </div><!-- /grid -->

      <?php endif; ?>

    </div><!-- /container -->
  </section>

  <!-- ============================================================
       CTA BANNER
  ============================================================ -->
  <?php require __DIR__ . '/includes/cta-banner.php'; ?>

<script>
function projectFilter() {
  return {
    activeFilter: 'all',
    filters: [
      { label: 'All Projects', value: 'all' },
      <?php foreach ($types as $type): ?>
      { label: <?= json_encode(ucfirst($type)) ?>, value: <?= json_encode($type) ?> },
      <?php endforeach; ?>
    ],
  };
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
