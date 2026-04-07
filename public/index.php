<?php
/**
 * Homepage — Moksha Construction
 * /public/index.php
 */

require_once __DIR__ . '/includes/db.php';

$current_page    = 'home';
$page_title      = 'Moksha Construction | General Contractor in Clarksville & Nashville, TN';
$page_description = 'Moksha Construction is a licensed general contractor in Clarksville, TN serving Nashville, Atlanta, and the Southeast. Residential, commercial, industrial & religious construction. Get a free quote today.';
$page_url        = '/';
$page_image      = 'og-home.jpg';

$extra_schemas = [
    [
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        '@id'      => 'https://moksha.construction/#website',
        'url'      => 'https://moksha.construction/',
        'name'     => 'Moksha Construction',
        'description' => 'Licensed general contractor in Clarksville & Nashville, TN serving the Southeast.',
        'publisher' => [
            '@id' => 'https://moksha.construction/#organization',
        ],
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => [
                '@type'       => 'EntryPoint',
                'urlTemplate' => 'https://moksha.construction/?s={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ],
];

// Featured projects for homepage
$featuredStmt = $db->query('SELECT * FROM projects WHERE status = "published" ORDER BY sort_order ASC, created_at DESC LIMIT 3');
$featuredProjects = $featuredStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     SECTION 1: HERO — Full Viewport Image Background
============================================================ -->
<section class="relative min-h-screen flex flex-col justify-center overflow-hidden" aria-label="Hero">

  <!-- Video Background -->
  <div class="absolute inset-0 z-0">
    <video
      autoplay muted loop playsinline
      preload="metadata"
      poster="/assets/images/hero-poster.jpg"
      class="w-full h-full object-cover object-center"
      aria-hidden="true"
    >
      <source src="/assets/video/hero.mp4" type="video/mp4">
    </video>

    <!-- Dark gradient overlay -->
    <div class="absolute inset-0 bg-gradient-to-t from-[oklch(6%_0.008_310/0.85)] via-[oklch(6%_0.008_310/0.55)] to-transparent"></div>
    <!-- Purple radial glow — bottom-left corner -->
    <div class="absolute bottom-0 left-0 w-[60vw] h-[60vh] bg-[radial-gradient(ellipse_at_bottom_left,_oklch(46%_0.22_310/0.30)_0%,_transparent_70%)] pointer-events-none"></div>
  </div>

  <!-- Hero Content -->
  <div class="relative z-10 max-w-[var(--container)] mx-auto px-6 pt-32 pb-48 lg:pt-40 lg:pb-56">

    <!-- Eyebrow -->
    <p class="section-label tracking-[0.14em] mb-5 reveal">
      GENERAL CONTRACTOR · CLARKSVILLE, TN
    </p>

    <!-- H1 -->
    <h1 class="font-[var(--font-display)] text-[length:var(--text-hero)] font-extrabold tracking-tight leading-[1.05] max-w-4xl mb-6 reveal reveal-delay-1">
      We Don't Just Build Structures.<br>
      <em class="font-accent not-italic text-accent-400">We Build Legacies.</em>
    </h1>

    <!-- Subhead -->
    <p class="text-[length:var(--text-body-lg)] text-text-2 max-w-2xl mb-10 reveal reveal-delay-2">
      Licensed across Tennessee, Texas, and North Carolina — with offices in Nashville and Atlanta. From ground-up commercial builds to custom residential projects, Moksha Construction delivers on time, on budget, and built to last.
    </p>

    <!-- CTA Buttons -->
    <div class="flex flex-col sm:flex-row items-start gap-4 reveal reveal-delay-3">
      <a href="/contact#quote" class="btn-primary text-base">
        Get a Free Quote <span aria-hidden="true">→</span>
      </a>
      <a href="/projects" class="btn-secondary text-base">
        View Our Work
      </a>
    </div>
  </div>

  <!-- Scroll Indicator -->
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-2 text-text-3 animate-bounce" aria-hidden="true">
    <span class="text-xs font-medium tracking-widest uppercase">Scroll</span>
    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
    </svg>
  </div>
</section>


<!-- ============================================================
     SECTION 2: STATS BAR — Floating card overlapping hero
============================================================ -->
<div class="relative z-20 max-w-[var(--container)] mx-auto px-6 -mt-20 mb-0 reveal">
  <div class="stats-bar shadow-[var(--shadow-lg)]">

    <!-- Stat 1 -->
    <div>
      <div class="stat-number" data-counter data-target="15" data-suffix="+">15+</div>
      <div class="stat-label">Years of<br>Experience</div>
    </div>

    <!-- Stat 2 -->
    <div>
      <div class="stat-number" data-counter data-target="5">5</div>
      <div class="stat-label">States &amp;<br>Growing</div>
    </div>

    <!-- Stat 3 -->
    <div>
      <div class="stat-number" data-counter data-target="280" data-suffix="K+">280K+</div>
      <div class="stat-label">Square Feet<br>Delivered</div>
    </div>

    <!-- Stat 4 -->
    <div>
      <div class="stat-number text-[clamp(1.5rem,3vw,2.25rem)]">Multi-<br>Sector</div>
      <div class="stat-label">Expertise<br>Across All Types</div>
    </div>

  </div>
</div>


<!-- ============================================================
     SECTION 3: ABOUT INTRO — Two columns
============================================================ -->
<section class="py-[var(--section-y)] bg-subtle" aria-labelledby="about-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 lg:gap-20 items-center">

      <!-- Left: Label column with gold accent bar -->
      <div class="lg:col-span-2 reveal">
        <div class="flex items-start gap-5">
          <!-- Gold accent vertical bar -->
          <div class="w-1 self-stretch bg-accent-400 rounded-full shrink-0 min-h-[120px]" aria-hidden="true"></div>

          <div>
            <p class="section-label mb-3">WHO WE ARE</p>
            <h2 id="about-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight">
              Precision-Built for the Southeast
            </h2>
          </div>
        </div>
      </div>

      <!-- Right: Copy -->
      <div class="lg:col-span-3 reveal reveal-delay-2">
        <p class="text-[length:var(--text-body-lg)] text-text-2 mb-5">
          Moksha Construction is a full-service general contractor headquartered in Clarksville, Tennessee, with offices in Nashville and Atlanta. We specialize in general contracting, construction management, and design-build services across residential, commercial, industrial, and religious construction.
        </p>
        <p class="text-[length:var(--text-body-lg)] text-text-2 mb-8">
          Our team combines 15 years of collective experience with a global perspective — delivering projects that meet the highest standards of craftsmanship while staying on schedule and within budget. We're licensed across multiple states and growing, because our clients keep coming back.
        </p>
        <a href="/about" class="btn-ghost text-base">
          About Our Company
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>

    </div>
  </div>
</section>


<!-- ============================================================
     SECTION 4: SERVICES GRID — 2×2 cards with image backgrounds
============================================================ -->
<section class="py-[var(--section-y)]" aria-labelledby="services-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <!-- Section Header -->
    <div class="text-center mb-12">
      <p class="section-label reveal">WHAT WE DO</p>
      <h2 id="services-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight reveal reveal-delay-1">
        Services Built Around Your Vision
      </h2>
    </div>

    <!-- 2×2 Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

      <!-- Card 1: General Contracting -->
      <a href="/services/general-contracting" class="service-card reveal reveal-delay-1" aria-label="General Contracting services">
        <img
          src="/assets/images/services/drone-bim.webp"
          alt="Aerial view of a construction site managed by Moksha Construction"
          loading="lazy"
          width="800"
          height="600"
        >
        <div>
          <!-- Service icon -->
          <div class="w-10 h-10 rounded-md bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mb-3" aria-hidden="true">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-2">General Contracting</h3>
          <p class="text-text-2 text-sm leading-relaxed mb-4">Complete project oversight from permits to punch list. We coordinate every trade so you don't have to.</p>
          <span class="btn-ghost text-sm">
            Learn More
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <!-- Card 2: Construction Management -->
      <a href="/services/construction-management" class="service-card reveal reveal-delay-2" aria-label="Construction Management services">
        <img
          src="/assets/images/services/trimble-gps.webp"
          alt="Construction manager using GPS and digital tools on site"
          loading="lazy"
          width="800"
          height="600"
        >
        <div>
          <div class="w-10 h-10 rounded-md bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mb-3" aria-hidden="true">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9 3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Construction Management</h3>
          <p class="text-text-2 text-sm leading-relaxed mb-4">Data-driven project management that eliminates overruns and keeps your build on track.</p>
          <span class="btn-ghost text-sm">
            Learn More
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <!-- Card 3: Design & Build -->
      <a href="/services/design-build" class="service-card reveal reveal-delay-3" aria-label="Design and Build services">
        <img
          src="/assets/images/services/design-build-hero.webp"
          alt="Architectural plans and building design collaboration"
          loading="lazy"
          width="800"
          height="600"
        >
        <div>
          <div class="w-10 h-10 rounded-md bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mb-3" aria-hidden="true">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Design &amp; Build</h3>
          <p class="text-text-2 text-sm leading-relaxed mb-4">One team, one vision, one point of accountability — from concept sketch to final walkthrough.</p>
          <span class="btn-ghost text-sm">
            Learn More
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

      <!-- Card 4: Residential · Commercial · Industrial -->
      <a href="/services/residential-commercial-industrial" class="service-card reveal reveal-delay-4" aria-label="Residential, Commercial, and Industrial construction services">
        <img
          src="/assets/images/services/commercial.webp"
          alt="Large-scale commercial construction project by Moksha Construction"
          loading="lazy"
          width="800"
          height="600"
        >
        <div>
          <div class="w-10 h-10 rounded-md bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mb-3" aria-hidden="true">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Residential · Commercial · Industrial</h3>
          <p class="text-text-2 text-sm leading-relaxed mb-4">Custom homes, retail centers, warehouses, and everything in between. We build across sectors.</p>
          <span class="btn-ghost text-sm">
            Learn More
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
          </span>
        </div>
      </a>

    </div>
  </div>
</section>


<!-- ============================================================
     SECTION 5: FEATURED PROJECTS — 3-column horizontal row
============================================================ -->
<section class="py-[var(--section-y)] bg-subtle" aria-labelledby="projects-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <!-- Section Header -->
    <div class="text-center mb-4 reveal">
      <p class="section-label">OUR WORK</p>
    </div>
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-12">
      <h2 id="projects-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight reveal reveal-delay-1">
        Projects That Speak<br>for Themselves
      </h2>
      <p class="text-text-2 text-[length:var(--text-body-lg)] max-w-sm reveal reveal-delay-2">
        From 90-suite hotels to 280,000 sq ft exhibition centers — see what Moksha Construction delivers.
      </p>
    </div>

    <!-- Project Cards Row — scrollable on mobile -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 overflow-x-auto md:overflow-visible">

      <?php foreach ($featuredProjects as $i => $fp): ?>
      <article class="project-card reveal reveal-delay-<?= $i + 1 ?>">
        <img
          src="<?= htmlspecialchars($fp['featured_image'] ?? '/assets/images/projects/placeholder.jpg') ?>"
          alt="<?= htmlspecialchars($fp['title']) ?> — <?= htmlspecialchars($fp['size'] ?? '') ?> <?= htmlspecialchars($fp['type']) ?> project<?= $fp['location'] ? ' in ' . htmlspecialchars($fp['location']) : '' ?>"
          loading="lazy"
          width="800"
          height="533"
        >
        <span class="project-badge"><?= strtoupper(htmlspecialchars($fp['type'])) ?></span>
        <div class="project-card-overlay">
          <h3 class="text-[length:var(--text-h3)] font-bold mb-1"><?= htmlspecialchars($fp['title']) ?></h3>
          <p class="text-text-2 text-sm">
            <?= $fp['size'] ? htmlspecialchars($fp['size']) . ' · ' : '' ?>
            <?= htmlspecialchars($fp['location'] ?? '') ?>
          </p>
          <a href="/projects/<?= htmlspecialchars($fp['slug']) ?>" class="btn-ghost text-sm mt-3 opacity-0 group-hover:opacity-100 transition-opacity">
            View Project
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      </article>
      <?php endforeach; ?>

      <?php if (empty($featuredProjects)): ?>
      <div class="col-span-3 text-center py-12">
        <p class="text-text-3">Projects coming soon.</p>
      </div>
      <?php endif; ?>

    </div>

    <!-- View All CTA -->
    <div class="text-center mt-10 reveal">
      <a href="/projects" class="btn-ghost text-base">
        View All Projects
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>

  </div>
</section>


<!-- ============================================================
     SECTION 6: WHY MOKSHA — Image left, differentiators right
============================================================ -->
<section class="py-[var(--section-y)]" aria-labelledby="why-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

      <!-- Left: Atmospheric construction photo -->
      <div class="relative reveal">
        <div class="rounded-[var(--radius-xl)] overflow-hidden aspect-[4/5]">
          <img
            src="/assets/images/services/drone-bim.webp"
            alt="Moksha Construction team on a large-scale commercial project site"
            loading="lazy"
            width="700"
            height="875"
            class="w-full h-full object-cover"
          >
          <!-- Subtle purple gradient overlay on the photo -->
          <div class="absolute inset-0 bg-gradient-to-br from-brand-900/40 via-transparent to-transparent rounded-[var(--radius-xl)]"></div>
        </div>

        <!-- Floating accent badge -->
        <div class="absolute -bottom-6 -right-6 bg-surface border border-[oklch(100%_0_0/0.08)] rounded-[var(--radius-md)] p-5 shadow-[var(--shadow-gold)] hidden sm:block">
          <div class="text-accent-400 font-extrabold text-3xl leading-none">15+</div>
          <div class="text-text-2 text-sm mt-1">Years of<br>Experience</div>
        </div>
      </div>

      <!-- Right: Differentiators -->
      <div>
        <div class="reveal mb-10">
          <p class="section-label mb-3">WHY MOKSHA</p>
          <h2 id="why-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight">
            What Sets Us Apart
          </h2>
        </div>

        <ol class="space-y-8" role="list">

          <!-- Differentiator 1 -->
          <li class="flex gap-5 reveal reveal-delay-1">
            <div class="shrink-0 w-10 h-10 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center" aria-hidden="true">
              <span class="text-accent-400 font-extrabold text-sm leading-none">01</span>
            </div>
            <div>
              <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Licensed Across Five States</h3>
              <p class="text-text-2 leading-relaxed">We hold active licenses in Tennessee, Texas, and North Carolina — with Georgia, South Carolina, and Florida in progress. One contractor, no borders.</p>
            </div>
          </li>

          <!-- Differentiator 2 -->
          <li class="flex gap-5 reveal reveal-delay-2">
            <div class="shrink-0 w-10 h-10 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center" aria-hidden="true">
              <span class="text-accent-400 font-extrabold text-sm leading-none">02</span>
            </div>
            <div>
              <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Smart Homes &amp; Modern Construction</h3>
              <p class="text-text-2 leading-relaxed">Our in-house IT specialists integrate cutting-edge technology into every build. From connected home systems to BIM-driven project management, we build for the future.</p>
            </div>
          </li>

          <!-- Differentiator 3 -->
          <li class="flex gap-5 reveal reveal-delay-3">
            <div class="shrink-0 w-10 h-10 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center" aria-hidden="true">
              <span class="text-accent-400 font-extrabold text-sm leading-none">03</span>
            </div>
            <div>
              <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Religious &amp; Cultural Construction</h3>
              <p class="text-text-2 leading-relaxed">We're one of the only contractors in the Southeast with deep experience building temples, churches, and culturally significant structures. We understand the unique requirements, sensitivities, and craftsmanship these projects demand.</p>
            </div>
          </li>

          <!-- Differentiator 4 -->
          <li class="flex gap-5 reveal reveal-delay-4">
            <div class="shrink-0 w-10 h-10 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center" aria-hidden="true">
              <span class="text-accent-400 font-extrabold text-sm leading-none">04</span>
            </div>
            <div>
              <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Diverse Expertise, Deep Local Knowledge</h3>
              <p class="text-text-2 leading-relaxed">Our team brings perspectives from across the country and the globe — backed by roots in Clarksville and Nashville. We understand this market because we live here.</p>
            </div>
          </li>

        </ol>
      </div>

    </div>
  </div>
</section>


<!-- ============================================================
     SECTION 7: PARTNERS — Grayscale logo row
============================================================ -->
<section class="py-[var(--section-y)] bg-subtle overflow-hidden" aria-labelledby="partners-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <!-- Section Header -->
    <div class="text-center mb-12">
      <p class="section-label reveal">TRUSTED PARTNERS</p>
      <h2 id="partners-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight reveal reveal-delay-1">
        Built With the Best
      </h2>
    </div>

    <!-- Partner Logo Row -->
    <div class="flex flex-wrap items-center justify-center gap-10 lg:gap-16 reveal reveal-delay-2">

      <!-- Lowe's -->
      <a href="https://lowes.com" target="_blank" rel="noopener noreferrer" aria-label="Lowe's — Moksha Construction partner" class="group">
        <img
          src="/assets/images/partners/lowes.png"
          alt="Lowe's"
          loading="lazy"
          width="120"
          height="48"
          class="h-10 w-auto object-contain brightness-0 invert opacity-80 group-hover:brightness-100 group-hover:invert-0 group-hover:opacity-100 transition-all duration-300"
        >
      </a>

      <!-- Sherwin-Williams -->
      <a href="https://sherwin-williams.com" target="_blank" rel="noopener noreferrer" aria-label="Sherwin-Williams — Moksha Construction partner" class="group">
        <img
          src="/assets/images/partners/sherwin-williams.png"
          alt="Sherwin-Williams"
          loading="lazy"
          width="180"
          height="48"
          class="h-10 w-auto object-contain brightness-0 invert opacity-80 group-hover:brightness-100 group-hover:invert-0 group-hover:opacity-100 transition-all duration-300"
        >
      </a>

      <!-- United Rentals -->
      <a href="https://unitedrentals.com" target="_blank" rel="noopener noreferrer" aria-label="United Rentals — Moksha Construction partner" class="group">
        <img
          src="/assets/images/partners/united-rentals.svg"
          alt="United Rentals"
          loading="lazy"
          width="180"
          height="48"
          class="h-10 w-auto object-contain brightness-0 invert opacity-80 group-hover:brightness-100 group-hover:invert-0 group-hover:opacity-100 transition-all duration-300"
        >
      </a>

      <!-- Partner placeholder 4 -->
      <div class="h-10 w-32 rounded bg-surface border border-[oklch(100%_0_0/0.06)] flex items-center justify-center opacity-30" aria-hidden="true">
        <span class="text-text-4 text-xs tracking-wider">PARTNER</span>
      </div>

      <!-- Partner placeholder 5 -->
      <div class="h-10 w-32 rounded bg-surface border border-[oklch(100%_0_0/0.06)] flex items-center justify-center opacity-30" aria-hidden="true">
        <span class="text-text-4 text-xs tracking-wider">PARTNER</span>
      </div>

      <!-- Partner placeholder 6 -->
      <div class="h-10 w-32 rounded bg-surface border border-[oklch(100%_0_0/0.06)] flex items-center justify-center opacity-30" aria-hidden="true">
        <span class="text-text-4 text-xs tracking-wider">PARTNER</span>
      </div>

    </div>
  </div>
</section>


<!-- ============================================================
     SECTION 8: OUR TEAM
============================================================ -->
<section class="py-[var(--section-y)]" aria-labelledby="team-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center mb-14 reveal">
      <p class="section-label">OUR TEAM</p>
      <h2 id="team-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight reveal reveal-delay-1">
        The People Behind<br>
        <em class="font-accent not-italic text-accent-400">Every Project</em>
      </h2>
    </div>

    <?php
    $team = [
        ['name' => 'Rakesh Patel',  'role' => 'CEO',                'initials' => 'RP', 'image' => '/assets/images/team/rakesh-patel.jpg',     'bio' => 'Visionary leader with decades of experience in construction and real estate development across the Southeast.'],
        ['name' => 'Parth Patel',   'role' => 'Managing Director',  'initials' => 'PP', 'image' => '/assets/images/team/parth-patel.jpg',      'bio' => 'Manages Moksha\'s most complex builds with meticulous attention to timeline, budget, and quality.'],
        ['name' => 'Hari Patel',    'role' => 'CFO',                'initials' => 'HP', 'image' => '/assets/images/team/hari-patel.jpg',       'bio' => 'Coordinates day-to-day site operations, subcontractor scheduling, and quality control.'],
        ['name' => 'Parth Patel',   'role' => 'CTO',                'initials' => 'PP', 'image' => '/assets/images/team/parth-patel-tech.jpg', 'bio' => 'Bridges construction and technology — BIM workflows, project management systems, and digital tools.'],
    ];
    ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($team as $i => $member): ?>
      <div class="card p-0 overflow-hidden group reveal <?= $i > 0 ? 'reveal-delay-' . min($i, 3) : '' ?>">
        <div class="relative aspect-[3/4] bg-gradient-to-br from-brand-900 to-brand-700 overflow-hidden">
          <img
            src="<?= htmlspecialchars($member['image']) ?>"
            alt="<?= htmlspecialchars($member['name']) ?>, <?= htmlspecialchars($member['role']) ?> at Moksha Construction"
            class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
            onerror="this.style.display='none';this.nextElementSibling.style.display=''"
            onload="this.nextElementSibling.style.display='none'"
          >
          <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <span class="text-6xl font-bold text-accent-400 opacity-30 select-none"><?= $member['initials'] ?></span>
          </div>
          <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-void/80 to-transparent"></div>
        </div>
        <div class="p-6">
          <p class="text-xs font-semibold text-accent-400 uppercase tracking-widest mb-1.5"><?= htmlspecialchars($member['role']) ?></p>
          <h3 class="text-[length:var(--text-h3)] font-bold tracking-tight mb-3"><?= htmlspecialchars($member['name']) ?></h3>
          <p class="text-sm text-text-2 leading-relaxed"><?= htmlspecialchars($member['bio']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  </div>
</section>


<!-- ============================================================
     SECTION 9: VALUES STRIP — 5 horizontal value cards
============================================================ -->
<section class="py-[var(--section-y)] bg-subtle" aria-labelledby="values-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <!-- Section Header (visually hidden — values are self-explanatory) -->
    <h2 id="values-heading" class="sr-only">Our Core Values</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

      <!-- Value 1: Integrity -->
      <div class="card p-6 text-center reveal reveal-delay-1">
        <div class="w-12 h-12 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mx-auto mb-4" aria-hidden="true">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
          </svg>
        </div>
        <h3 class="font-bold text-text mb-2">Integrity</h3>
        <p class="text-text-3 text-sm leading-relaxed">Honest work. Honest pricing. Every time.</p>
      </div>

      <!-- Value 2: Transparency -->
      <div class="card p-6 text-center reveal reveal-delay-2">
        <div class="w-12 h-12 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mx-auto mb-4" aria-hidden="true">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <h3 class="font-bold text-text mb-2">Transparency</h3>
        <p class="text-text-3 text-sm leading-relaxed">You see every dollar, every timeline, every decision.</p>
      </div>

      <!-- Value 3: Accountability -->
      <div class="card p-6 text-center reveal reveal-delay-3">
        <div class="w-12 h-12 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mx-auto mb-4" aria-hidden="true">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
          </svg>
        </div>
        <h3 class="font-bold text-text mb-2">Accountability</h3>
        <p class="text-text-3 text-sm leading-relaxed">We own our outcomes — the good and the challenges.</p>
      </div>

      <!-- Value 4: Respect -->
      <div class="card p-6 text-center reveal reveal-delay-4">
        <div class="w-12 h-12 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mx-auto mb-4" aria-hidden="true">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/>
          </svg>
        </div>
        <h3 class="font-bold text-text mb-2">Respect</h3>
        <p class="text-text-3 text-sm leading-relaxed">Your vision first. Cultural sensitivity always.</p>
      </div>

      <!-- Value 5: Compliance -->
      <div class="card p-6 text-center reveal reveal-delay-4">
        <div class="w-12 h-12 rounded-full bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mx-auto mb-4" aria-hidden="true">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375m-8.25-3l1.5 1.5 3-3.75"/>
          </svg>
        </div>
        <h3 class="font-bold text-text mb-2">Compliance</h3>
        <p class="text-text-3 text-sm leading-relaxed">Licensed, insured, and code-compliant — no shortcuts.</p>
      </div>

    </div>
  </div>
</section>


<!-- ============================================================
     SECTION 10: SERVICE AREAS — Map placeholder + Office cards
============================================================ -->
<section class="py-[var(--section-y)]" aria-labelledby="areas-heading">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <!-- Section Header -->
    <div class="text-center mb-12">
      <p class="section-label reveal">WHERE WE BUILD</p>
      <h2 id="areas-heading" class="text-[length:var(--text-h2)] font-bold tracking-tight reveal reveal-delay-1">
        Serving the Southeast — and Growing
      </h2>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

      <!-- Map Placeholder (left — spans 2 columns) -->
      <div class="lg:col-span-2 reveal">
        <div
          class="w-full aspect-[16/9] rounded-[var(--radius-xl)] bg-surface border border-[oklch(100%_0_0/0.08)] overflow-hidden relative"
          id="service-area-map"
          aria-label="Map of Moksha Construction service areas across the Southeast United States"
          role="img"
        >
          <!-- Placeholder map background -->
          <div class="absolute inset-0 bg-gradient-to-br from-brand-950 to-base flex items-center justify-center">
            <div class="text-center">
              <svg class="w-12 h-12 text-accent-400/30 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
              </svg>
              <p class="text-text-4 text-sm">Interactive map loading</p>
            </div>
          </div>

          <!-- State pin indicators -->
          <div class="absolute inset-0 pointer-events-none">
            <!-- Nashville pin -->
            <div class="absolute" style="top: 35%; left: 52%;" aria-hidden="true">
              <div class="relative">
                <div class="w-3 h-3 rounded-full bg-accent-400 shadow-[0_0_8px_oklch(88%_0.24_97/0.6)]"></div>
                <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-surface border border-[oklch(100%_0_0/0.12)] rounded px-2 py-1 text-xs text-text whitespace-nowrap">Nashville</div>
              </div>
            </div>
            <!-- Atlanta pin -->
            <div class="absolute" style="top: 52%; left: 54%;" aria-hidden="true">
              <div class="relative">
                <div class="w-3 h-3 rounded-full bg-accent-400 shadow-[0_0_8px_oklch(88%_0.24_97/0.6)]"></div>
                <div class="absolute top-4 left-1/2 -translate-x-1/2 bg-surface border border-[oklch(100%_0_0/0.12)] rounded px-2 py-1 text-xs text-text whitespace-nowrap">Atlanta</div>
              </div>
            </div>
            <!-- Texas indicator -->
            <div class="absolute" style="top: 65%; left: 28%;" aria-hidden="true">
              <div class="w-2.5 h-2.5 rounded-full bg-brand-500 opacity-70 shadow-[0_0_6px_oklch(46%_0.22_310/0.5)]"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Office Cards + Badge (right column) -->
      <div class="space-y-5 reveal reveal-delay-2">

        <!-- Nashville Office Card -->
        <address class="card p-6 not-italic">
          <div class="flex items-start gap-3">
            <div class="shrink-0 w-8 h-8 rounded bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mt-0.5" aria-hidden="true">
              <svg class="w-4 h-4 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-text text-sm mb-1">Nashville Office</h3>
              <p class="text-text-2 text-sm leading-relaxed">
                315 Deaderick Street, Suite 1550<br>
                Nashville, TN 37238
              </p>
            </div>
          </div>
        </address>

        <!-- Atlanta Office Card -->
        <address class="card p-6 not-italic">
          <div class="flex items-start gap-3">
            <div class="shrink-0 w-8 h-8 rounded bg-accent-400/10 border border-accent-400/30 flex items-center justify-center mt-0.5" aria-hidden="true">
              <svg class="w-4 h-4 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="font-semibold text-text text-sm mb-1">Atlanta Office</h3>
              <p class="text-text-2 text-sm leading-relaxed">
                1 W Court Square<br>
                Decatur, GA 30030
              </p>
            </div>
          </div>
        </address>

        <!-- Multi-State License Badge -->
        <div class="card p-6 border-accent-400/20">
          <h3 class="text-xs font-semibold text-accent-400 uppercase tracking-widest mb-3">Active Licenses</h3>
          <div class="flex flex-wrap gap-2 mb-4">
            <span class="px-3 py-1 bg-accent-400/10 border border-accent-400/30 rounded-full text-xs font-semibold text-accent-400">Tennessee</span>
            <span class="px-3 py-1 bg-accent-400/10 border border-accent-400/30 rounded-full text-xs font-semibold text-accent-400">Texas</span>
            <span class="px-3 py-1 bg-accent-400/10 border border-accent-400/30 rounded-full text-xs font-semibold text-accent-400">North Carolina</span>
          </div>
          <h3 class="text-xs font-semibold text-text-3 uppercase tracking-widest mb-3">Expanding To</h3>
          <div class="flex flex-wrap gap-2">
            <span class="px-3 py-1 bg-surface border border-[oklch(100%_0_0/0.08)] rounded-full text-xs text-text-3">Georgia</span>
            <span class="px-3 py-1 bg-surface border border-[oklch(100%_0_0/0.08)] rounded-full text-xs text-text-3">South Carolina</span>
            <span class="px-3 py-1 bg-surface border border-[oklch(100%_0_0/0.08)] rounded-full text-xs text-text-3">Florida</span>
          </div>
        </div>

      </div>
    </div>
  </div>
</section>


<!-- ============================================================
     SECTION 11: CTA BANNER — Include
============================================================ -->
<?php require __DIR__ . '/includes/cta-banner.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
