<?php
$page_title       = 'Residential, Commercial & Industrial Construction | Moksha Construction | TN, TX, NC';
$page_description = 'Moksha Construction builds custom homes, commercial spaces, and industrial facilities across Tennessee, Texas, and North Carolina. Specializing in smart homes, religious buildings, and multi-sector construction. Free estimates.';
$page_url         = '/services/residential-commercial-industrial';
$current_page     = 'services-residential-commercial-industrial';

$breadcrumbs = [
    ['name' => 'Home',         'url' => '/'],
    ['name' => 'Services',     'url' => '/services/residential-commercial-industrial'],
    ['name' => 'Residential · Commercial · Industrial'],
];

$faqs = [
    [
        'q' => 'Does Moksha Construction build custom homes?',
        'a' => 'Yes. We build custom residential homes from the ground up, including smart homes with integrated technology systems. We also handle renovations, additions, and multi-unit residential developments like our 64-unit Lotus Villa apartment complex.',
    ],
    [
        'q' => 'What types of commercial buildings do you construct?',
        'a' => 'We build offices, retail centers, hotels, exhibition halls, restaurants, mixed-use developments, and religious structures. Our commercial portfolio ranges from 10,000 sq ft retail centers to 280,000 sq ft exhibition facilities.',
    ],
    [
        'q' => 'Do you build religious structures like temples and churches?',
        'a' => 'Yes — this is one of our specializations. Moksha Construction has specific experience in religious and cultural construction, including temples and churches. We understand the unique architectural, cultural, and regulatory requirements these projects demand.',
    ],
    [
        'q' => 'What states do you build in?',
        'a' => 'We are actively licensed and building in Tennessee, Texas, and North Carolina. We are currently expanding into Georgia, South Carolina, and Florida. Contact us to discuss your project regardless of location — we may be able to assist.',
    ],
    [
        'q' => 'Can you handle industrial and warehouse construction?',
        'a' => 'Yes. Moksha Construction builds industrial facilities including warehouses, distribution centers, manufacturing plants, and specialized production facilities. Our industrial experience covers heavy-load foundations, clear-span structures, and utility-intensive mechanical systems.',
    ],
];

require_once __DIR__ . '/../includes/header.php';
?>

<!-- ============================================================
     HERO — Residential / Commercial / Industrial
============================================================ -->
<section class="relative min-h-[70vh] flex items-end pb-20 pt-40 overflow-hidden">
  <!-- Background photo -->
  <div class="absolute inset-0">
    <img
      src="/assets/images/services/commercial.webp"
      alt="Large-scale commercial construction project by Moksha Construction"
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
        <li><a href="/services/residential-commercial-industrial" class="hover:text-accent-400 transition-colors">Services</a></li>
        <li aria-hidden="true"><span class="text-text-4">/</span></li>
        <li class="text-accent-400 font-medium" aria-current="page">Residential · Commercial · Industrial</li>
      </ol>
    </nav>

    <!-- Eyebrow -->
    <p class="section-label reveal">OUR SERVICES</p>

    <!-- H1 -->
    <h1 class="text-[length:var(--text-hero)] font-bold tracking-tight text-text max-w-3xl reveal reveal-delay-1">
      Every Sector. Every Scale. One Standard of Excellence.
    </h1>

    <!-- Subtext -->
    <p class="text-[length:var(--text-body-lg)] text-text-2 mt-6 max-w-xl reveal reveal-delay-2">
      Custom homes, commercial complexes, industrial facilities, and religious structures — built with the same commitment to quality.
    </p>

    <!-- CTA buttons -->
    <div class="flex flex-col sm:flex-row items-start gap-4 mt-10 reveal reveal-delay-3">
      <a href="/contact#quote" class="btn-primary">Get a Free Quote <span aria-hidden="true">→</span></a>
      <a href="#residential" class="btn-secondary">Explore Sectors <span aria-hidden="true">↓</span></a>
    </div>
  </div>
</section>

<!-- ============================================================
     SECTOR QUICK NAV
============================================================ -->
<div class="bg-surface border-b border-[oklch(100%_0_0/0.06)] sticky top-[60px] z-40">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <nav class="flex items-center gap-0 overflow-x-auto scrollbar-hide -mb-px" aria-label="Sector navigation">
      <a href="#residential"  class="shrink-0 px-6 py-4 text-sm font-medium text-text-2 hover:text-accent-400 border-b-2 border-transparent hover:border-accent-400 transition-all">Residential</a>
      <a href="#commercial"   class="shrink-0 px-6 py-4 text-sm font-medium text-text-2 hover:text-accent-400 border-b-2 border-transparent hover:border-accent-400 transition-all">Commercial</a>
      <a href="#industrial"   class="shrink-0 px-6 py-4 text-sm font-medium text-text-2 hover:text-accent-400 border-b-2 border-transparent hover:border-accent-400 transition-all">Industrial</a>
      <a href="#religious"    class="shrink-0 px-6 py-4 text-sm font-medium text-text-2 hover:text-accent-400 border-b-2 border-transparent hover:border-accent-400 transition-all">Religious</a>
    </nav>
  </div>
</div>

<!-- ============================================================
     SECTOR 1: RESIDENTIAL — Image left, copy right
============================================================ -->
<section class="py-(--section-y) bg-base" id="residential">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

      <!-- Image (left) -->
      <div class="reveal order-2 lg:order-1">
        <div class="relative rounded-[var(--radius-xl)] overflow-hidden aspect-[4/3]">
          <img
            src="/assets/images/projects/residential/main.webp"
            alt="Custom residential home built by Moksha Construction in Tennessee"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <!-- Gold accent corner -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-accent-400/60 via-accent-400/20 to-transparent"></div>
        </div>
      </div>

      <!-- Copy (right) -->
      <div class="order-1 lg:order-2 reveal reveal-delay-1">
        <!-- Badge -->
        <span class="inline-block px-3 py-1 bg-accent-400/10 border border-accent-400/20 rounded-full text-xs font-bold text-accent-400 uppercase tracking-widest mb-6">Residential</span>

        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text mb-6">
          Your Dream Home, Engineered to Last
        </h2>

        <div class="space-y-4 text-text-2 leading-relaxed mb-8">
          <p>
            From custom-built family homes to apartment complexes, Moksha Construction brings residential visions to life. We specialize in smart home construction — integrating automation systems, structured wiring, energy-efficient HVAC, and modern finishes that today's homeowners expect.
          </p>
          <p>
            Whether you're building your first home in Clarksville, renovating a Nashville property, or developing a multi-unit residential complex, our team handles every phase from permitting to final walkthrough.
          </p>
        </div>

        <!-- Featured project callout -->
        <div class="bg-surface border border-[oklch(100%_0_0/0.08)] rounded-[var(--radius-md)] p-5 mb-8">
          <p class="text-xs font-bold text-accent-400 uppercase tracking-widest mb-2">Featured Project</p>
          <p class="text-text font-semibold">Lotus Villa Apartments</p>
          <p class="text-text-2 text-sm mt-1">64-unit ground-up residential complex with contemporary design, fitness center, and community spaces.</p>
        </div>

        <!-- Capabilities list -->
        <ul class="grid grid-cols-2 gap-3">
          <?php
          $residential_caps = [
              'Custom Homes',
              'Smart Home Integration',
              'Apartment Complexes',
              'Home Renovations',
              'Multi-Unit Development',
              'Additions & Expansions',
          ];
          foreach ($residential_caps as $cap): ?>
          <li class="flex items-center gap-2 text-sm text-text-2">
            <svg class="w-4 h-4 text-accent-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= htmlspecialchars($cap) ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     SECTOR 2: COMMERCIAL — Copy left, image right
============================================================ -->
<section class="py-(--section-y) bg-subtle" id="commercial">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

      <!-- Copy (left) -->
      <div class="reveal">
        <!-- Badge -->
        <span class="inline-block px-3 py-1 bg-brand-600/15 border border-brand-500/20 rounded-full text-xs font-bold text-brand-300 uppercase tracking-widest mb-6">Commercial</span>

        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text mb-6">
          Spaces That Drive Business Forward
        </h2>

        <div class="space-y-4 text-text-2 leading-relaxed mb-8">
          <p>
            Moksha Construction understands that commercial spaces need to work as hard as the businesses inside them. We design and build offices, retail centers, hotels, restaurants, exhibition venues, and mixed-use developments — tailored to your operational needs and brand identity.
          </p>
          <p>
            Our commercial portfolio includes a 280,000 sq ft exhibition center, a 200,000 sq ft office building with integrated live sound studios and theaters, and a 90-suite luxury hotel — all delivered on schedule.
          </p>
        </div>

        <!-- Featured project callout -->
        <div class="bg-raised border border-[oklch(100%_0_0/0.08)] rounded-[var(--radius-md)] p-5 mb-8">
          <p class="text-xs font-bold text-accent-400 uppercase tracking-widest mb-2">Featured Project</p>
          <p class="text-text font-semibold">Expansive Exhibition Center</p>
          <p class="text-text-2 text-sm mt-1">280,000 sq ft multi-purpose event hub serving conventions, trade shows, and cultural gatherings in Clarksville, TN.</p>
        </div>

        <!-- Capabilities list -->
        <ul class="grid grid-cols-2 gap-3">
          <?php
          $commercial_caps = [
              'Office Buildings',
              'Retail Centers',
              'Hotels & Hospitality',
              'Exhibition Centers',
              'Mixed-Use Development',
              'Restaurants & Cafes',
          ];
          foreach ($commercial_caps as $cap): ?>
          <li class="flex items-center gap-2 text-sm text-text-2">
            <svg class="w-4 h-4 text-accent-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= htmlspecialchars($cap) ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Image (right) -->
      <div class="reveal reveal-delay-1">
        <div class="relative rounded-[var(--radius-xl)] overflow-hidden aspect-[4/3]">
          <img
            src="/assets/images/projects/exhibition/main.png"
            alt="280,000 sq ft Expansive Exhibition Center built by Moksha Construction in Clarksville, TN"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <!-- Gold accent corner -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-accent-400/60 via-accent-400/20 to-transparent"></div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     SECTOR 3: INDUSTRIAL — Image left, copy right
============================================================ -->
<section class="py-(--section-y) bg-base" id="industrial">
  <div class="max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">

      <!-- Image (left) -->
      <div class="reveal order-2 lg:order-1">
        <div class="relative rounded-[var(--radius-xl)] overflow-hidden aspect-[4/3]">
          <img
            src="/assets/images/services/industrial.webp"
            alt="Industrial facility construction project managed by Moksha Construction"
            class="w-full h-full object-cover"
            loading="lazy"
          >
          <!-- Gold accent corner -->
          <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-accent-400/60 via-accent-400/20 to-transparent"></div>
        </div>
      </div>

      <!-- Copy (right) -->
      <div class="order-1 lg:order-2 reveal reveal-delay-1">
        <!-- Badge -->
        <span class="inline-block px-3 py-1 bg-[oklch(100%_0_0/0.05)] border border-[oklch(100%_0_0/0.10)] rounded-full text-xs font-bold text-text-2 uppercase tracking-widest mb-6">Industrial</span>

        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text mb-6">
          Built for Operational Efficiency
        </h2>

        <div class="space-y-4 text-text-2 leading-relaxed mb-8">
          <p>
            Industrial construction demands specialized expertise — heavy-load foundations, clear-span structures, utility-intensive mechanical systems, and strict compliance with operational safety standards. Moksha Construction delivers industrial facilities engineered for long-term durability and peak performance.
          </p>
          <p>
            From warehouses and distribution centers to manufacturing plants and specialized production facilities, we build industrial spaces that support your operations for decades.
          </p>
        </div>

        <!-- Capabilities list -->
        <ul class="grid grid-cols-2 gap-3">
          <?php
          $industrial_caps = [
              'Warehouses',
              'Distribution Centers',
              'Manufacturing Plants',
              'Production Facilities',
              'Heavy-Load Foundations',
              'Clear-Span Structures',
          ];
          foreach ($industrial_caps as $cap): ?>
          <li class="flex items-center gap-2 text-sm text-text-2">
            <svg class="w-4 h-4 text-accent-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-5.121-5.121a1 1 0 111.414-1.414L8.414 12.172l6.879-6.879a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <?= htmlspecialchars($cap) ?>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

    </div>
  </div>
</section>

<!-- ============================================================
     RELIGIOUS CONSTRUCTION — Full-width dark section, gold accent border
============================================================ -->
<section class="py-(--section-y) bg-void border-y border-accent-400/20 relative overflow-hidden" id="religious">
  <!-- Background glow -->
  <div class="absolute inset-0 pointer-events-none">
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] rounded-full bg-accent-400/5 blur-[100px]"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[400px] h-[300px] rounded-full bg-brand-600/10 blur-[80px]"></div>
  </div>

  <!-- Gold top accent border -->
  <div class="absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r from-transparent via-accent-400 to-transparent"></div>
  <!-- Gold bottom accent border -->
  <div class="absolute bottom-0 left-0 right-0 h-[3px] bg-gradient-to-r from-transparent via-accent-400 to-transparent"></div>

  <div class="relative max-w-[var(--container)] mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

      <!-- Copy -->
      <div class="reveal">
        <!-- Badge -->
        <span class="inline-block px-3 py-1.5 bg-accent-400/15 border border-accent-400/30 rounded-full text-xs font-bold text-accent-400 uppercase tracking-widest mb-8">Unique Specialization</span>

        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight text-text mb-6">
          Religious &amp; Cultural Construction
        </h2>

        <div class="space-y-5 text-text-2 leading-relaxed mb-10">
          <p>
            Moksha Construction is one of the few general contractors in the Southeast with deep experience in religious and cultural construction. We've built temples, churches, and community worship spaces — each requiring unique architectural sensitivities, specialized craftsmanship, and a genuine respect for the spiritual significance of the structure.
          </p>
          <p>
            We understand that a temple is not just a building. It's a sacred space for generations. Our team works closely with religious leaders, cultural consultants, and specialized artisans to deliver structures that honor tradition while meeting modern building codes and accessibility requirements.
          </p>
          <p>
            If you're planning a religious or cultural construction project, we'd welcome the conversation.
          </p>
        </div>

        <a href="/contact#quote" class="btn-primary">
          Start the Conversation <span aria-hidden="true">→</span>
        </a>
      </div>

      <!-- Differentiator cards -->
      <div class="reveal reveal-delay-1 space-y-5">

        <div class="border border-accent-400/15 rounded-[var(--radius-lg)] p-6 bg-accent-400/5">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-accent-400/15 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-text mb-1">Cultural Sensitivity</h3>
              <p class="text-text-2 text-sm leading-relaxed">We work directly with religious leaders and cultural consultants throughout every phase — from site selection through final consecration.</p>
            </div>
          </div>
        </div>

        <div class="border border-accent-400/15 rounded-[var(--radius-lg)] p-6 bg-accent-400/5">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-accent-400/15 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-text mb-1">Specialized Craftsmanship</h3>
              <p class="text-text-2 text-sm leading-relaxed">Our network includes artisans with specific expertise in ornamental stonework, traditional woodwork, and decorative elements specific to various religious traditions.</p>
            </div>
          </div>
        </div>

        <div class="border border-accent-400/15 rounded-[var(--radius-lg)] p-6 bg-accent-400/5">
          <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-lg bg-accent-400/15 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div>
              <h3 class="text-base font-bold text-text mb-1">Code &amp; Accessibility Compliance</h3>
              <p class="text-text-2 text-sm leading-relaxed">Every religious structure meets current building codes, ADA accessibility requirements, and fire safety standards — without compromising architectural integrity.</p>
            </div>
          </div>
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
