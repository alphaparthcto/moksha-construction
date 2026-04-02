<?php
$page_title       = 'Our Projects | Moksha Construction Portfolio | Clarksville & Nashville, TN';
$page_description = 'View Moksha Construction\'s project portfolio — from 280,000 sq ft exhibition centers to luxury hotels and apartment complexes. See our work across Tennessee and the Southeast.';
$page_url         = '/projects';
$current_page     = 'projects';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Projects'],
];

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
      <h1 class="text-(--text-hero) font-bold tracking-tight mb-6 reveal reveal-delay-1">
        Our Work Speaks<br>
        <em class="font-accent not-italic text-accent-400">for Itself</em>
      </h1>
      <p class="text-(--text-body-lg) text-text-2 max-w-2xl reveal reveal-delay-2">
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

      <!-- Project Grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Project 1: Exhibition Center -->
        <div
          class="project-card"
          data-type="commercial"
          x-show="activeFilter === 'all' || activeFilter === 'commercial'"
          x-transition:enter="transition ease-out duration-400"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          style="aspect-ratio: 4/3;"
        >
          <img
            src="/assets/images/projects/exhibition/main.png"
            alt="Expansive Exhibition Center — 280,000 sq ft multi-purpose venue in Clarksville, TN"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <span class="project-badge">Commercial</span>
          <div class="project-card-overlay">
            <h2 class="text-(--text-h3) font-bold text-text mb-1">Expansive Exhibition Center</h2>
            <p class="text-sm text-text-3 mb-3">280,000 sq ft &middot; Clarksville, TN</p>
            <p class="text-sm text-text-2 leading-relaxed opacity-0 translate-y-2 transition-all duration-300 group-hover:opacity-100 group-hover:translate-y-0 max-w-lg">
              A sprawling multi-purpose hub for trade shows, conventions, cultural gatherings, and large-scale events. State-of-the-art amenities with flexible layouts designed for maximum versatility.
            </p>
          </div>
        </div>

        <!-- Project 2: Office Building -->
        <div
          class="project-card"
          data-type="commercial"
          x-show="activeFilter === 'all' || activeFilter === 'commercial'"
          x-transition:enter="transition ease-out duration-400"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          style="aspect-ratio: 4/3;"
        >
          <img
            src="/assets/images/projects/office/main.png"
            alt="Versatile Office Building — 200,000 sq ft landmark office with live sound studio, theaters, and cafe"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <span class="project-badge">Commercial</span>
          <div class="project-card-overlay">
            <h2 class="text-(--text-h3) font-bold text-text mb-1">Versatile Office Building</h2>
            <p class="text-sm text-text-3 mb-3">200,000 sq ft &middot; Tennessee</p>
            <p class="text-sm text-text-2 leading-relaxed max-w-lg">
              A landmark office building featuring an integrated live sound studio, multiple presentation theaters, and a two-story cafe — redefining the modern workplace with seamless multi-use functionality.
            </p>
          </div>
        </div>

        <!-- Project 3: Hotel -->
        <div
          class="project-card"
          data-type="hospitality"
          x-show="activeFilter === 'all' || activeFilter === 'hospitality'"
          x-transition:enter="transition ease-out duration-400"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          style="aspect-ratio: 4/3;"
        >
          <img
            src="/assets/images/projects/hotel/main.png"
            alt="Luxurious Hotel of Distinction — 90 room suites in Clarksville, TN"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <span class="project-badge">Hospitality</span>
          <div class="project-card-overlay">
            <h2 class="text-(--text-h3) font-bold text-text mb-1">Luxurious Hotel of Distinction</h2>
            <p class="text-sm text-text-3 mb-3">90 Room Suites &middot; Clarksville, TN</p>
            <p class="text-sm text-text-2 leading-relaxed max-w-lg">
              An upscale hospitality venue featuring 90 luxurious room suites with world-class amenities and personalized services. Every detail crafted to deliver the ultimate guest experience.
            </p>
          </div>
        </div>

        <!-- Project 4: Lotus Villa Apartments -->
        <div
          class="project-card"
          data-type="residential"
          x-show="activeFilter === 'all' || activeFilter === 'residential'"
          x-transition:enter="transition ease-out duration-400"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          style="aspect-ratio: 4/3;"
        >
          <img
            src="/assets/images/projects/apartments/drone-1.jpg"
            alt="Lotus Villa Apartments — 64-unit ground-up apartment complex in Tennessee"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <span class="project-badge">Residential</span>
          <div class="project-card-overlay">
            <h2 class="text-(--text-h3) font-bold text-text mb-1">Lotus Villa Apartments</h2>
            <p class="text-sm text-text-3 mb-3">64 Units &middot; Tennessee</p>
            <p class="text-sm text-text-2 leading-relaxed max-w-lg">
              A ground-up apartment complex featuring contemporary architectural design, spacious units, a fitness center, communal gathering spaces, and landscaped green areas — built for modern community living.
            </p>
          </div>
        </div>

        <!-- Project 5: Retail Center — spans full width -->
        <div
          class="project-card md:col-span-2"
          data-type="commercial"
          x-show="activeFilter === 'all' || activeFilter === 'commercial'"
          x-transition:enter="transition ease-out duration-400"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-200"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          style="aspect-ratio: 21/9;"
        >
          <img
            src="/assets/images/projects/retail/photo-1.jpg"
            alt="Commercial Retail Center — 10,000 sq ft ground-up retail space in Tennessee"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <span class="project-badge">Commercial</span>
          <div class="project-card-overlay">
            <h2 class="text-(--text-h3) font-bold text-text mb-1">Commercial Retail Center</h2>
            <p class="text-sm text-text-3 mb-3">10,000 sq ft &middot; Tennessee</p>
            <p class="text-sm text-text-2 leading-relaxed max-w-2xl">
              A ground-up retail center optimizing every square foot for functionality and visual appeal. Innovative design blended with practical commercial considerations — built to attract and serve customers from day one.
            </p>
          </div>
        </div>

      </div><!-- /grid -->

      <!-- Empty state (when filter yields no matches) -->
      <div
        x-show="activeFilter === 'industrial'"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        class="text-center py-20"
      >
        <p class="text-text-3 text-(--text-body-lg)">Industrial projects coming soon.</p>
        <a href="/contact#quote" class="btn-primary mt-6 inline-flex">Discuss Your Project</a>
      </div>

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
      { label: 'Commercial',   value: 'commercial' },
      { label: 'Residential',  value: 'residential' },
      { label: 'Hospitality',  value: 'hospitality' },
      { label: 'Industrial',   value: 'industrial' },
    ],
  };
}
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
