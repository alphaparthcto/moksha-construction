<?php
$page_title       = 'Design & Build Services | Moksha Construction | Clarksville & Nashville, TN';
$page_description = 'Design-build construction services in Clarksville and Nashville, TN. Moksha Construction delivers seamless design-to-construction execution with one team, one budget, one timeline. Get a free consultation.';
$page_url         = '/services/design-build';
$current_page     = 'services-design-build';

$breadcrumbs = [
    ['name' => 'Home',          'url' => '/'],
    ['name' => 'Services',      'url' => '/services/design-build'],
    ['name' => 'Design & Build'],
];

$faqs = [
    [
        'q' => 'What is design-build construction?',
        'a' => 'Design-build is a project delivery method where one company handles both the design and construction of a building. This streamlines communication, reduces costs, and accelerates timelines by eliminating the gap between design and construction teams. Moksha Construction offers design-build services for residential, commercial, and industrial projects.',
    ],
    [
        'q' => 'Is design-build more expensive than traditional construction?',
        'a' => 'Design-build is typically 5–10% less expensive than traditional design-bid-build because it eliminates redundant processes, reduces change orders, and allows for continuous value engineering. You also benefit from a guaranteed maximum price earlier in the process.',
    ],
    [
        'q' => 'Can I bring my own architect to a design-build project?',
        'a' => 'Yes. We work with client-selected architects as well as our in-house design team. In either case, we integrate the design process with construction planning from day one to maintain the benefits of design-build delivery.',
    ],
    [
        'q' => 'How much faster is design-build compared to traditional delivery?',
        'a' => 'Design-build projects typically deliver 20–30% faster than traditional design-bid-build. This is because design and construction phases overlap — design development, permitting, and early-stage construction can run concurrently rather than sequentially.',
    ],
    [
        'q' => 'What types of projects is design-build best suited for?',
        'a' => 'Design-build works exceptionally well for commercial offices, retail centers, industrial facilities, custom homes, and hospitality properties. It\'s particularly valuable when the owner prioritizes speed-to-market, budget certainty, and a single point of accountability throughout the project.',
    ],
];

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================================
     HERO — Design & Build
============================================================ -->
<section class="relative min-h-[70vh] flex items-end pb-20 pt-40 overflow-hidden">
  <!-- Background photo -->
  <div class="absolute inset-0">
    <img
      src="/assets/images/services/design-build-hero.webp"
      alt="Architects and builders collaborating on a design-build project at Moksha Construction"
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
        <li><a href="/services/design-build" class="hover:text-accent-400 transition-colors">Services</a></li>
        <li aria-hidden="true"><span class="text-text-4">/</span></li>
        <li class="text-accent-400 font-medium" aria-current="page">Design &amp; Build</li>
      </ol>
    </nav>

    <!-- Eyebrow -->
    <p class="section-label reveal">OUR SERVICES</p>

    <!-- H1 -->
    <h1 class="text-(--text-hero) font-bold tracking-tight text-text max-w-3xl reveal reveal-delay-1">
      Design &amp; Build. One Team. One Vision.
    </h1>

    <!-- Subtext -->
    <p class="text-(--text-body-lg) text-text-2 mt-6 max-w-xl reveal reveal-delay-2">
      From concept sketch to certificate of occupancy — seamless execution under one roof.
    </p>

    <!-- CTA buttons -->
    <div class="flex flex-col sm:flex-row items-start gap-4 mt-10 reveal reveal-delay-3">
      <a href="/contact#quote" class="btn-primary">Start Your Design <span aria-hidden="true">→</span></a>
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
        <p class="section-label">THE DESIGN-BUILD ADVANTAGE</p>
        <h2 class="text-(--text-h2) font-bold tracking-tight text-text mb-6">
          Design and Construction, United
        </h2>
        <div class="space-y-5 text-text-2 text-(--text-body-lg) leading-relaxed">
          <p>
            Design-build eliminates the friction between architects and builders by uniting them under one contract and one accountability structure. At Moksha Construction, our architects, designers, and builders collaborate from day one — ensuring that what gets designed can actually get built, on time and within your budget.
          </p>
          <p>
            This integrated approach reduces project timelines by 20–30% compared to traditional design-bid-build delivery, because design and construction phases overlap instead of running sequentially. Fewer change orders. Fewer surprises. Better results.
          </p>
        </div>
      </div>

      <!-- Right: quick facts card (40%) -->
      <aside class="lg:col-span-2 reveal reveal-delay-2">
        <div class="card p-8 border-t-[3px] border-t-accent-400">
          <h3 class="text-(--text-h3) font-bold text-text mb-6">By the Numbers</h3>
          <ul class="space-y-4">
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">20–30% faster delivery vs. traditional</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">5–10% cost savings vs. design-bid-build</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">One contract. One team. One call.</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Guaranteed maximum price, set earlier</span>
            </li>
            <li class="flex items-start gap-3">
              <span class="mt-1 w-5 h-5 rounded-full bg-accent-400/15 flex items-center justify-center shrink-0">
                <svg class="w-3 h-3 text-accent-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
              </span>
              <span class="text-text-2 text-sm">Residential, commercial &amp; industrial</span>
            </li>
          </ul>
          <a href="/contact#quote" class="btn-primary w-full justify-center mt-8">Get a Consultation →</a>
        </div>
      </aside>

    </div>
  </div>
</section>

<!-- ============================================================
     BENEFITS — 3 feature cards
============================================================ -->
<section class="py-(--section-y) bg-base">
  <div class="max-w-[var(--container)] mx-auto px-6">

    <div class="text-center max-w-2xl mx-auto mb-16">
      <p class="section-label reveal">WHY DESIGN-BUILD</p>
      <h2 class="text-(--text-h2) font-bold tracking-tight text-text reveal reveal-delay-1">
        The Integrated Advantage
      </h2>
      <p class="text-text-2 mt-4 reveal reveal-delay-2">
        When design and construction live under one roof, everything moves faster, costs less, and delivers better.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

      <!-- Benefit 1 -->
      <article class="card p-8 reveal">
        <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center mb-6">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <h3 class="text-(--text-h3) font-bold text-text mb-3">Single Point of Accountability</h3>
        <p class="text-text-2 leading-relaxed">
          One contract. One team. One phone call when you have a question. Design-build eliminates the finger-pointing between designers and contractors that plagues traditional delivery methods.
        </p>
      </article>

      <!-- Benefit 2 -->
      <article class="card p-8 reveal reveal-delay-1">
        <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center mb-6">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
        </div>
        <h3 class="text-(--text-h3) font-bold text-text mb-3">Faster Delivery</h3>
        <p class="text-text-2 leading-relaxed">
          Overlapping design and construction phases means your project breaks ground sooner and completes faster. Our design-build projects typically deliver 20–30% ahead of traditional timelines.
        </p>
      </article>

      <!-- Benefit 3 -->
      <article class="card p-8 reveal reveal-delay-2">
        <div class="w-12 h-12 rounded-xl bg-accent-400/10 flex items-center justify-center mb-6">
          <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <h3 class="text-(--text-h3) font-bold text-text mb-3">Budget Certainty</h3>
        <p class="text-text-2 leading-relaxed">
          Real-time cost feedback during design prevents the sticker shock of bidding a completed design. We value-engineer as we design — not after the blueprints are done.
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
      <h2 class="text-(--text-h2) font-bold tracking-tight text-text reveal reveal-delay-1">
        From Concept to Certificate
      </h2>
    </div>

    <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" role="list">

      <!-- Step 1 -->
      <li class="reveal">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">01</div>
          <h3 class="text-lg font-bold text-text mb-3">Discover</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            We meet with you to understand your vision, goals, site, and budget. This is where we listen — and where the seeds of great design are planted.
          </p>
        </div>
      </li>

      <!-- Step 2 -->
      <li class="reveal reveal-delay-1">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">02</div>
          <h3 class="text-lg font-bold text-text mb-3">Design</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Our design team develops concept drawings, schematic designs, and construction documents — with real-time cost feedback built in so budget and design stay aligned throughout.
          </p>
        </div>
      </li>

      <!-- Step 3 -->
      <li class="reveal reveal-delay-2">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">03</div>
          <h3 class="text-lg font-bold text-text mb-3">Build</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Construction begins — often while final design details are still being refined. Our integrated approach means overlapping phases instead of waiting for a complete set of drawings.
          </p>
        </div>
      </li>

      <!-- Step 4 -->
      <li class="reveal reveal-delay-3">
        <div class="card p-8 h-full">
          <div class="text-(--text-display) font-black text-accent-400/20 leading-none mb-4 select-none" aria-hidden="true">04</div>
          <h3 class="text-lg font-bold text-text mb-3">Deliver</h3>
          <p class="text-text-2 text-sm leading-relaxed">
            Final walkthrough, punch list, and certificate of occupancy. We hand you a building that matches what we designed — because we never lost the thread between vision and construction.
          </p>
        </div>
      </li>

    </ol>

  </div>
</section>

<!-- ============================================================
     SMART & CULTURAL SPACES
============================================================ -->
<section class="py-(--section-y) bg-base">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      <!-- Copy -->
      <div class="reveal">
        <p class="section-label">DESIGN WITH INTENTION</p>
        <h2 class="text-(--text-h2) font-bold tracking-tight text-text mb-6">
          Spaces That Reflect Who You Are
        </h2>
        <div class="space-y-5 text-text-2 leading-relaxed">
          <p>
            Our design team doesn't just follow trends — we listen to your vision and translate it into spaces that function beautifully and reflect your identity.
          </p>
          <p>
            Whether it's a tech-forward smart home with integrated automation, a culturally significant religious structure, or a contemporary commercial environment, Moksha Construction designs with intention and builds with precision.
          </p>
        </div>
        <div class="mt-8 space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-1 h-full min-h-[1.25rem] bg-accent-400 rounded-full shrink-0 mt-1"></div>
            <p class="text-text-2 text-sm">Smart home integration — automation, connectivity, energy systems</p>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-1 h-full min-h-[1.25rem] bg-accent-400 rounded-full shrink-0 mt-1"></div>
            <p class="text-text-2 text-sm">Religious &amp; cultural construction — temples, churches, community spaces</p>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-1 h-full min-h-[1.25rem] bg-accent-400 rounded-full shrink-0 mt-1"></div>
            <p class="text-text-2 text-sm">Contemporary commercial — offices, retail, hospitality, mixed-use</p>
          </div>
        </div>
      </div>

      <!-- Visual accent block -->
      <div class="reveal reveal-delay-1">
        <div class="relative rounded-[var(--radius-xl)] overflow-hidden bg-surface border border-[oklch(100%_0_0/0.06)] p-10">
          <!-- Decorative gold accent line -->
          <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-accent-400 via-accent-400/50 to-transparent rounded-l-[var(--radius-xl)]"></div>
          <blockquote class="space-y-6">
            <p class="text-(--text-body-lg) text-text-2 leading-relaxed italic font-accent">
              "We don't just build what's on the drawings. We build what the client actually envisioned — and those two things are only the same when design and construction work together from day one."
            </p>
            <footer class="flex items-center gap-4">
              <div class="w-10 h-10 rounded-full bg-accent-400/10 flex items-center justify-center text-accent-400 font-bold text-sm shrink-0">PP</div>
              <div>
                <p class="text-text font-semibold text-sm">Parth Patel</p>
                <p class="text-text-3 text-xs">Managing Director, Moksha Construction</p>
              </div>
            </footer>
          </blockquote>
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
