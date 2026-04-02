<?php
$page_title       = 'Construction Management Services | Moksha Construction | Nashville & Clarksville, TN';
$page_description = 'Expert construction management in Nashville and Clarksville, TN. Moksha Construction delivers projects on time and on budget with data-driven scheduling, cost control, and transparent reporting. Free consultation.';
$page_url         = '/services/construction-management';
$current_page     = 'services-construction-management';

$breadcrumbs = [
    ['name' => 'Home',                    'url' => '/'],
    ['name' => 'Services',                'url' => '/services/construction-management'],
    ['name' => 'Construction Management'],
];

$faqs = [
    [
        'q' => 'What\'s the difference between a general contractor and a construction manager?',
        'a' => 'A general contractor performs the construction work directly, while a construction manager acts as the owner\'s representative — overseeing the project on your behalf, managing contractors, and ensuring quality, schedule, and budget compliance. Moksha Construction offers both services.',
    ],
    [
        'q' => 'How does construction management save money?',
        'a' => 'Professional construction management typically saves 5–15% on total project costs through value engineering, competitive bid management, schedule optimization, and early identification of potential issues that could cause costly changes later.',
    ],
    [
        'q' => 'What size projects benefit from construction management?',
        'a' => 'Any project over $500,000 typically benefits from dedicated construction management. For projects above $1 million, the cost savings from professional oversight almost always exceed the management fee.',
    ],
    [
        'q' => 'Do you provide construction management for both commercial and residential projects?',
        'a' => 'Yes. Moksha Construction provides construction management services for residential developments, commercial office buildings, industrial facilities, hospitality properties, and religious structures across Tennessee, Texas, and North Carolina.',
    ],
    [
        'q' => 'What reporting do clients receive during construction management?',
        'a' => 'Our clients receive weekly progress reports with site photos, schedule updates, and budget-to-actual comparisons. We also provide monthly cost reports with variance analysis and change order tracking — so you always know exactly where your project stands.',
    ],
];

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================================
     HERO — Construction Management
============================================================ -->
<section class="relative min-h-[70vh] flex items-end pb-20 pt-40 overflow-hidden">
  <!-- Background photo -->
  <div class="absolute inset-0">
    <img
      src="/assets/images/services/trimble-gps.webp"
      alt="Construction manager reviewing project data on site with Trimble GPS equipment"
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
        <li><a href="/services/construction-management" class="hover:text-accent-400 transition-colors">Services</a></li>
        <li aria-hidden="true"><span class="text-text-4">/</span></li>
        <li class="text-accent-400 font-medium" aria-current="page">Construction Management</li>
      </ol>
    </nav>

    <!-- Eyebrow -->
    <p class="section-label reveal">OUR SERVICES</p>

    <!-- H1 -->
    <h1 class="text-(--text-hero) font-bold tracking-tight text-text max-w-3xl reveal reveal-delay-1">
      Construction Management That Eliminates the Guesswork
    </h1>

    <!-- Subtext -->
    <p class="text-(--text-body-lg) text-text-2 mt-6 max-w-xl reveal reveal-delay-2">
      Data-driven project management. Transparent reporting. Zero surprises.
    </p>

    <!-- CTA buttons -->
    <div class="flex flex-col sm:flex-row items-start gap-4 mt-10 reveal reveal-delay-3">
      <a href="/contact#quote" class="btn-primary">Get a Free Consultation <span aria-hidden="true">→</span></a>
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
        <p class="section-label">THE MOKSHA APPROACH</p>
        <h2 class="text-(--text-h2) font-bold tracking-tight text-text mb-6">
          Proactive Management. Predictable Results.
        </h2>
        <div class="space-y-5 text-text-2 text-(--text-body-lg) leading-relaxed">
          <p>
            Effective construction management is the difference between a project that delivers on its promises and one that spirals into delays and overruns. At Moksha Construction, our construction management team takes a proactive approach — identifying problems before they surface, optimizing schedules for efficiency, and maintaining transparent communication at every milestone.
          </p>
          <p>
            We serve as your owner's representative on the job site, ensuring that every subcontractor, material delivery, and inspection aligns with your project goals. Our clients across Tennessee, Texas, and North Carolina trust us because we treat their budgets like our own.
          </p>
        </div>
      </div>

      <!-- Right: quick facts card (40%) -->
      <aside class="lg:col-span-2 reveal reveal-delay-2">
        <div class="card p-8 border-t-[3px] border-t-accent-400">
          <h3 class="text-(--text-h3) font-bold text-text mb-6">Why It Works</h3>
          <ul class="space-y-4">
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Saves 5–15% on total project costs</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Weekly progress reports with photos</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Budget-to-actual tracking every milestone</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Daily OSHA-compliant site inspections</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Free initial consultation</span>
            </li>
          </ul>
          <a href="/contact#quote" class="btn-primary w-full justify-center mt-8">Put Us to Work →</a>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============================================================
     WHAT WE MANAGE — 4 feature blocks
============================================================ -->
<section class="py-(--section-y) bg-base">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center max-w-2xl mx-auto mb-16">
      <p class="section-label reveal">SCOPE OF SERVICES</p>
      <h2 class="text-(--text-h2) font-bold tracking-tight text-text reveal reveal-delay-1">
        What We Manage
      </h2>
      <p class="text-text-2 mt-4 reveal reveal-delay-2">
        From the first feasibility study to the final punch list, our construction management team owns every phase of your project.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

      <!-- Feature 1 -->
      <article class="card p-8 reveal">
        <div class="flex items-start gap-5">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
          </div>
          <div>
            <h3 class="text-(--text-h3) font-bold text-text mb-3">Pre-Construction Planning</h3>
            <p class="text-text-2 leading-relaxed">
              Before a shovel hits dirt, we build a comprehensive project plan — scope definition, trade procurement, value engineering, and risk assessment. Our pre-construction process identifies cost savings and scheduling efficiencies that compound throughout the build.
            </p>
          </div>
        </div>
      </article>

      <!-- Feature 2 -->
      <article class="card p-8 reveal reveal-delay-1">
        <div class="flex items-start gap-5">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-(--text-h3) font-bold text-text mb-3">Schedule Optimization</h3>
            <p class="text-text-2 leading-relaxed">
              We use modern scheduling tools to map critical path activities, manage float time, and coordinate trade sequences. Our project managers monitor progress against the baseline daily, adjusting in real time to prevent cascading delays.
            </p>
          </div>
        </div>
      </article>

      <!-- Feature 3 -->
      <article class="card p-8 reveal reveal-delay-1">
        <div class="flex items-start gap-5">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-(--text-h3) font-bold text-text mb-3">Cost Tracking &amp; Reporting</h3>
            <p class="text-text-2 leading-relaxed">
              Monthly cost reports with variance analysis. Change order tracking with impact projections. Budget-to-actual comparisons at every milestone. You always know exactly where your project stands financially.
            </p>
          </div>
        </div>
      </article>

      <!-- Feature 4 -->
      <article class="card p-8 reveal reveal-delay-2">
        <div class="flex items-start gap-5">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-(--text-h3) font-bold text-text mb-3">Quality &amp; Safety Compliance</h3>
            <p class="text-text-2 leading-relaxed">
              Our safety-first approach includes daily job site inspections, OSHA compliance monitoring, subcontractor safety orientations, and incident-free project goals. Quality audits happen at every phase gate — not just at the end.
            </p>
          </div>
        </div>
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
      <h2 class="text-(--text-h2) font-bold tracking-tight text-text reveal reveal-delay-1">
        Our Process
      </h2>
    </div>

    <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" role="list">

      <!-- Step 1 -->
      <li class="reveal">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">01</div>
          <h3 class="text-lg font-bold text-text mb-3">Assess</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Site analysis, feasibility study, and risk review. We evaluate existing conditions, understand project objectives, and identify potential challenges before committing to a plan.
          </p>
        </div>
      </li>

      <!-- Step 2 -->
      <li class="reveal reveal-delay-1">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">02</div>
          <h3 class="text-lg font-bold text-text mb-3">Strategize</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Master schedule, budget lock, and trade awards. We finalize procurement, negotiate subcontractor agreements, and establish the baseline plan against which all progress will be measured.
          </p>
        </div>
      </li>

      <!-- Step 3 -->
      <li class="reveal reveal-delay-2">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">03</div>
          <h3 class="text-lg font-bold text-text mb-3">Execute</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Daily oversight, weekly reports, and issue resolution. We are your eyes on the ground — managing trades, tracking milestones, and escalating issues before they become delays.
          </p>
        </div>
      </li>

      <!-- Step 4 -->
      <li class="reveal reveal-delay-3">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">04</div>
          <h3 class="text-lg font-bold text-text mb-3">Close Out</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Final inspections, documentation, and handover. We compile all close-out packages — warranties, as-builts, O&amp;M manuals — and ensure every outstanding item is resolved before we sign off.
          </p>
        </div>
      </li>

    </ol>

  </div>
</section>

<!-- ============================================================
     FAQ
============================================================ -->
<section class="py-(--section-y) bg-base" id="faq">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center max-w-2xl mx-auto mb-16">
      <p class="section-label reveal">FAQ</p>
      <h2 class="text-(--text-h2) font-bold tracking-tight text-text reveal reveal-delay-1">
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
