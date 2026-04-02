<?php
$page_title       = 'General Contracting Services | Moksha Construction | Clarksville & Nashville, TN';
$page_description = 'Moksha Construction provides full-service general contracting in Clarksville, Nashville, and across the Southeast. From scheduling and budgeting to quality control — we manage every detail of your build. Get a free quote.';
$page_url         = '/services/general-contracting';
$current_page     = 'services-general-contracting';

$breadcrumbs = [
    ['name' => 'Home',             'url' => '/'],
    ['name' => 'Services',         'url' => '/services/general-contracting'],
    ['name' => 'General Contracting'],
];

$faqs = [
    [
        'q' => 'What does a general contractor do?',
        'a' => 'A general contractor manages all aspects of a construction project, including hiring subcontractors, scheduling work phases, managing the budget, obtaining permits, and ensuring quality standards are met. At Moksha Construction, we serve as the single point of accountability for your entire build.',
    ],
    [
        'q' => 'How much does it cost to hire a general contractor in Tennessee?',
        'a' => 'General contractor fees in Tennessee typically range from 10% to 20% of total project cost, depending on project complexity, size, and scope. Moksha Construction provides free, detailed estimates with line-item cost breakdowns. Call us at (615) 234-0272 for a no-obligation quote.',
    ],
    [
        'q' => 'Do you work on both residential and commercial projects?',
        'a' => 'Yes. Moksha Construction handles residential projects (custom homes, renovations, apartments), commercial builds (offices, retail, hotels, exhibition centers), industrial facilities (warehouses, manufacturing), and religious structures (temples, churches). We\'re licensed in Tennessee, Texas, and North Carolina.',
    ],
    [
        'q' => 'How long does a typical construction project take?',
        'a' => 'Timelines vary by project size and complexity. A residential home typically takes 6–12 months, while a large commercial build like our 280,000 sq ft exhibition center may take 18–24 months. We provide a detailed schedule during the planning phase and keep you updated with weekly progress reports.',
    ],
    [
        'q' => 'Are you licensed and insured?',
        'a' => 'Yes. Moksha Construction is fully licensed, bonded, and insured. We hold active contractor licenses in Tennessee, Texas, and North Carolina, with expansion into Georgia, South Carolina, and Florida in progress.',
    ],
];

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================================
     HERO — General Contracting
============================================================ -->
<section class="relative min-h-[70vh] flex items-end pb-20 pt-40 overflow-hidden">
  <!-- Background photo -->
  <div class="absolute inset-0">
    <img
      src="/assets/images/services/drone-bim.webp"
      alt="Aerial view of a construction site managed by Moksha Construction"
      class="w-full h-full object-cover"
      loading="eager"
      fetchpriority="high"
    >
    <!-- Dark overlay gradient -->
    <div class="absolute inset-0 bg-gradient-to-t from-base via-base/70 to-base/20"></div>
    <!-- Purple radial glow -->
    <div class="absolute bottom-0 left-0 w-[600px] h-[600px] rounded-full bg-brand-600/10 blur-[120px] pointer-events-none"></div>
  </div>

  <div class="relative max-w-[var(--container)] mx-auto px-6 w-full">
    <!-- Breadcrumb nav -->
    <nav class="mb-8" aria-label="Breadcrumb">
      <ol class="flex items-center gap-2 text-sm text-text-3" role="list">
        <li><a href="/" class="hover:text-accent-400 transition-colors">Home</a></li>
        <li aria-hidden="true"><span class="text-text-4">/</span></li>
        <li><a href="/services/general-contracting" class="hover:text-accent-400 transition-colors">Services</a></li>
        <li aria-hidden="true"><span class="text-text-4">/</span></li>
        <li class="text-accent-400 font-medium" aria-current="page">General Contracting</li>
      </ol>
    </nav>

    <!-- Eyebrow -->
    <p class="section-label reveal">OUR SERVICES</p>

    <!-- H1 -->
    <h1 class="text-[length:var(--text-hero)] font-bold tracking-tight text-text max-w-3xl reveal reveal-delay-1">
      General Contracting That Delivers
    </h1>

    <!-- Subtext -->
    <p class="text-[length:var(--text-body-lg)] text-text-2 mt-6 max-w-xl reveal reveal-delay-2">
      Complete project oversight from foundation to finish. Licensed in Tennessee, Texas, and North Carolina.
    </p>

    <!-- CTA buttons -->
    <div class="flex flex-col sm:flex-row items-start gap-4 mt-10 reveal reveal-delay-3">
      <a href="/contact#quote" class="btn-primary">Get a Free Quote <span aria-hidden="true">→</span></a>
      <a href="tel:<?= SITE_PHONE_RAW ?>" class="btn-secondary">Call <?= SITE_PHONE ?></a>
    </div>
  </div>
</section>

<!-- ============================================================
     INTRO — 2-column: copy left 60%, quick facts card right 40%
============================================================ -->
<section class="py-(--section-y) bg-subtle">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 items-start">

      <!-- Left: copy (60%) -->
      <div class="lg:col-span-3 reveal">
        <p class="section-label">WHAT WE DO</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text mb-6">
          Your Single Point of Accountability
        </h2>
        <div class="space-y-5 text-text-2 text-[length:var(--text-body-lg)] leading-relaxed">
          <p>
            As your general contractor, Moksha Construction takes full responsibility for your build — from the first permit application to the final punch list walkthrough. We coordinate architects, engineers, and subcontractors into a single, efficient operation so your project stays on schedule and within budget.
          </p>
          <p>
            Based in Clarksville, Tennessee, with a second office in Nashville, we serve clients across the Southeast with residential, commercial, industrial, and religious construction projects ranging from 10,000 to 280,000+ square feet.
          </p>
        </div>
      </div>

      <!-- Right: quick facts card (40%) -->
      <aside class="lg:col-span-2 reveal reveal-delay-2">
        <div class="card p-8 border-t-[3px] border-t-accent-400">
          <h3 class="text-[length:var(--text-h3)] font-bold text-text mb-6">Quick Facts</h3>
          <ul class="space-y-4">
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Licensed in 3 states — TN, TX, NC</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">15+ years collective experience</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Projects up to 280,000 sq ft</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Offices in Nashville &amp; Atlanta</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Free detailed estimates</span>
            </li>
          </ul>
          <a href="/contact#quote" class="btn-primary w-full justify-center mt-8">Start Your Project →</a>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============================================================
     WHAT WE HANDLE — 3 feature blocks
============================================================ -->
<section class="py-(--section-y) bg-base">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center max-w-2xl mx-auto mb-16">
      <p class="section-label reveal">CAPABILITIES</p>
      <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text reveal reveal-delay-1">
        What We Handle
      </h2>
      <p class="text-text-2 mt-4 reveal reveal-delay-2">
        From the first permit to the final walkthrough, our team manages every moving part of your build.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

      <!-- Feature 1 -->
      <article class="card p-8 reveal">
        <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center mb-6">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
        </div>
        <h3 class="text-[length:var(--text-h3)] font-bold text-text mb-3">Project Coordination &amp; Scheduling</h3>
        <p class="text-text-2 leading-relaxed">
          We build the master schedule and manage every phase — excavation, framing, MEP, finishes. Our project managers maintain daily coordination with every trade on site, flagging conflicts before they become delays.
        </p>
      </article>

      <!-- Feature 2 -->
      <article class="card p-8 reveal reveal-delay-1">
        <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center mb-6">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </div>
        <h3 class="text-[length:var(--text-h3)] font-bold text-text mb-3">Budget Management &amp; Cost Control</h3>
        <p class="text-text-2 leading-relaxed">
          Transparent budgets with line-item breakdowns. We track costs against estimates in real time, giving you full visibility into where your money goes — and catching variances before they compound.
        </p>
      </article>

      <!-- Feature 3 -->
      <article class="card p-8 reveal reveal-delay-2">
        <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center mb-6">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="text-[length:var(--text-h3)] font-bold text-text mb-3">Quality Assurance &amp; Inspections</h3>
        <p class="text-text-2 leading-relaxed">
          Every material and installation meets or exceeds code requirements. We conduct internal quality audits at every milestone and coordinate all municipal inspections to keep your Certificate of Occupancy on track.
        </p>
      </article>

    </div>
  </div>
</section>

<!-- ============================================================
     PROCESS — 4 numbered steps
============================================================ -->
<section class="py-(--section-y) bg-subtle">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center max-w-2xl mx-auto mb-16">
      <p class="section-label reveal">HOW IT WORKS</p>
      <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text reveal reveal-delay-1">
        Our Process
      </h2>
    </div>

    <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" role="list">

      <!-- Step 1 -->
      <li class="relative reveal">
        <div class="card p-8 h-full">
          <div class="text-[length:var(--text-display)] font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">01</div>
          <h3 class="text-lg font-bold text-text mb-3">Discover</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            We learn your vision, site, and budget. This phase covers an initial site visit, scope discussion, and preliminary assessment — so we understand every constraint before we plan.
          </p>
        </div>
      </li>

      <!-- Step 2 -->
      <li class="relative reveal reveal-delay-1">
        <div class="card p-8 h-full">
          <div class="text-[length:var(--text-display)] font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">02</div>
          <h3 class="text-lg font-bold text-text mb-3">Plan</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Detailed scope, schedule, and trade plan. We build the master project schedule, procure subcontractors, lock the budget, and submit all permit applications before a single shovel breaks ground.
          </p>
        </div>
      </li>

      <!-- Step 3 -->
      <li class="relative reveal reveal-delay-2">
        <div class="card p-8 h-full">
          <div class="text-[length:var(--text-display)] font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">03</div>
          <h3 class="text-lg font-bold text-text mb-3">Build</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Active site management with weekly reporting. Our superintendent is on site daily, coordinating trades, tracking progress, and communicating updates to you every step of the way.
          </p>
        </div>
      </li>

      <!-- Step 4 -->
      <li class="relative reveal reveal-delay-3">
        <div class="card p-8 h-full">
          <div class="text-[length:var(--text-display)] font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">04</div>
          <h3 class="text-lg font-bold text-text mb-3">Deliver</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Final inspections, punch list, and handover. We walk the completed structure with you, resolve every open item, and hand you the keys — along with all warranty documents and as-built drawings.
          </p>
        </div>
      </li>

    </ol>

  </div>
</section>

<!-- ============================================================
     SMART HOMES & TECHNOLOGY
============================================================ -->
<section class="py-(--section-y) bg-base">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

      <!-- Copy -->
      <div class="reveal">
        <p class="section-label">TECHNOLOGY</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text mb-6">
          Building Smart Homes for the Connected Generation
        </h2>
        <p class="text-text-2 leading-relaxed mb-6">
          We don't just build structures — we build intelligent spaces. Our in-house IT specialists work alongside our construction team to integrate home automation, structured wiring, smart HVAC controls, and security systems into every residential project.
        </p>
        <p class="text-text-2 leading-relaxed">
          Whether you're building a tech-forward family home or a connected commercial space, Moksha delivers the infrastructure for tomorrow — without compromising the craftsmanship of today.
        </p>
      </div>

      <!-- Icon grid -->
      <div class="grid grid-cols-2 gap-6 reveal reveal-delay-1">
        <div class="card p-6 text-center">
          <div class="w-10 h-10 rounded-lg bg-accent-400/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
          </div>
          <p class="text-sm font-semibold text-text">Home Automation</p>
          <p class="text-xs text-text-3 mt-1">Smart lighting, climate, security</p>
        </div>
        <div class="card p-6 text-center">
          <div class="w-10 h-10 rounded-lg bg-accent-400/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/></svg>
          </div>
          <p class="text-sm font-semibold text-text">Structured Wiring</p>
          <p class="text-xs text-text-3 mt-1">Cat6, fiber, whole-home audio</p>
        </div>
        <div class="card p-6 text-center">
          <div class="w-10 h-10 rounded-lg bg-accent-400/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
          <p class="text-sm font-semibold text-text">Energy Efficiency</p>
          <p class="text-xs text-text-3 mt-1">Smart HVAC, solar-ready, EV prep</p>
        </div>
        <div class="card p-6 text-center">
          <div class="w-10 h-10 rounded-lg bg-accent-400/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.069A1 1 0 0121 8.847v6.306a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
          </div>
          <p class="text-sm font-semibold text-text">Security Systems</p>
          <p class="text-xs text-text-3 mt-1">Cameras, access control, monitoring</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     FAQ
============================================================ -->
<section class="py-(--section-y) bg-subtle" id="faq">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center max-w-2xl mx-auto mb-16">
      <p class="section-label reveal">FAQ</p>
      <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text reveal reveal-delay-1">
        Common Questions
      </h2>
    </div>

    <div class="max-w-3xl mx-auto space-y-4" x-data="{ open: null }">

      <?php foreach ($faqs as $index => $faq): ?>
      <div class="card overflow-hidden reveal" style="transition-delay: <?= $index * 0.05 ?>s">
        <button
          class="w-full flex items-center justify-between gap-6 p-6 text-left"
          @click="open === <?= $index ?> ? open = null : open = <?= $index ?>"
          :aria-expanded="open === <?= $index ?>"
        >
          <span class="font-semibold text-text"><?= htmlspecialchars($faq['q']) ?></span>
          <svg
            class="w-5 h-5 text-accent-400 shrink-0 transition-transform duration-300"
            :class="open === <?= $index ?> && 'rotate-180'"
            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
          >
            <path d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div
          x-show="open === <?= $index ?>"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 -translate-y-2"
          x-transition:enter-end="opacity-100 translate-y-0"
          x-transition:leave="transition ease-in duration-150"
          x-cloak
        >
          <div class="px-6 pb-6 text-text-2 leading-relaxed border-t border-[oklch(100%_0_0/0.06)] pt-4">
            <?= htmlspecialchars($faq['a']) ?>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

    </div>

  </div>
</section>

<!-- ============================================================
     CTA BANNER
============================================================ -->
<?php require __DIR__ . '/../includes/cta-banner.php'; ?>

<?php require __DIR__ . '/../includes/footer.php'; ?>
