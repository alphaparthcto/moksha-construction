<?php
$page_title       = 'About Moksha Construction | Licensed General Contractor | Clarksville, TN';
$page_description = 'Learn about Moksha Construction — a licensed general contractor in Clarksville, TN with offices in Nashville and Atlanta. 15+ years of experience in residential, commercial, industrial, and religious construction across the Southeast.';
$page_url         = '/about';
$current_page     = 'about';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'About'],
];

require_once __DIR__ . '/includes/header.php';
?>

  <!-- ============================================================
       HERO
  ============================================================ -->
  <section class="relative min-h-[65vh] flex items-end pb-20 pt-40 overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0">
      <img
        src="/assets/images/services/drone-bim.webp"
        alt="Moksha Construction — building legacies across the Southeast"
        class="w-full h-full object-cover"
        fetchpriority="high"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-base via-base/65 to-base/20"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-base/70 to-transparent"></div>
      <!-- Purple radial glow -->
      <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full bg-brand-600/20 blur-3xl pointer-events-none"></div>
    </div>

    <!-- Breadcrumb -->
    <div class="absolute top-28 left-0 right-0">
      <div class="max-w-[var(--container)] mx-auto px-6">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-text-3">
          <a href="/" class="hover:text-accent-400 transition-colors">Home</a>
          <span aria-hidden="true">/</span>
          <span class="text-text-2">About</span>
        </nav>
      </div>
    </div>

    <div class="relative max-w-[var(--container)] mx-auto px-6">
      <p class="section-label reveal">OUR STORY</p>
      <h1 class="text-[length:var(--text-hero)] font-bold tracking-tight mb-6 reveal reveal-delay-1">
        Built on Integrity.<br>
        <em class="font-accent not-italic text-accent-400">Growing by Reputation.</em>
      </h1>
      <p class="text-[length:var(--text-body-lg)] text-text-2 max-w-2xl reveal reveal-delay-2">
        From Clarksville to Atlanta — Moksha Construction is building the Southeast, one landmark at a time.
      </p>
    </div>
  </section>

  <!-- ============================================================
       OUR STORY
  ============================================================ -->
  <section class="py-(--section-y) bg-subtle">
    <div class="max-w-[var(--container)] mx-auto px-6">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

        <!-- Left: Image -->
        <div class="relative reveal">
          <div class="rounded-2xl overflow-hidden aspect-[4/3]">
            <img
              src="/assets/images/services/commercial.webp"
              alt="Moksha Construction project under development — commercial build in the Southeast"
              class="w-full h-full object-cover"
              loading="lazy"
            >
          </div>
          <!-- Gold accent line -->
          <div class="absolute -left-4 top-8 bottom-8 w-1 bg-accent-400 rounded-full opacity-60"></div>
        </div>

        <!-- Right: Story copy -->
        <div class="reveal reveal-delay-1">
          <p class="section-label">WHO WE ARE</p>
          <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight mb-6">
            A Firm Built to Outlast
          </h2>
          <div class="space-y-5 text-text-2 leading-relaxed">
            <p>
              Moksha Construction was founded in Clarksville, Tennessee, with a clear mission: build structures that outlast the generation that built them. What started as a local contracting firm has grown into a multi-state construction company with offices in Nashville and Atlanta, serving clients across Tennessee, Texas, North Carolina, and beyond.
            </p>
            <p>
              We're not the biggest contractor in the Southeast — and we're not trying to be. We're building a company where every project gets the full weight of our attention, our expertise, and our commitment to doing things right. Our clients don't come back because we're cheap. They come back because we deliver.
            </p>
            <p>
              From custom residential homes and luxury hotels to 280,000 sq ft exhibition centers and culturally significant religious structures, every Moksha project carries the same standard of precision, accountability, and craft.
            </p>
          </div>
          <a href="/projects" class="btn-ghost mt-8 inline-flex">
            View Our Portfolio
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
       STATS BAR
  ============================================================ -->
  <section class="py-(--section-y)">
    <div class="max-w-[var(--container)] mx-auto px-6">
      <div class="stats-bar reveal" style="grid-template-columns: repeat(5, 1fr);">

        <div>
          <div class="stat-number" data-count="15" data-suffix="+">15+</div>
          <div class="stat-label">Years of Experience</div>
        </div>

        <div>
          <div class="stat-number" data-count="5">5</div>
          <div class="stat-label">States &amp; Growing</div>
        </div>

        <div>
          <div class="stat-number" data-count="3">3</div>
          <div class="stat-label">Offices — Nashville, Atlanta &amp; Clarksville</div>
        </div>

        <div>
          <div class="stat-number" data-count="280" data-suffix="K+">280K+</div>
          <div class="stat-label">Sq Ft Delivered</div>
        </div>

        <div>
          <div class="stat-number">Multi-<br>Sector</div>
          <div class="stat-label">Res &middot; Com &middot; Ind</div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
       OUR TEAM
  ============================================================ -->
  <section class="py-(--section-y) bg-subtle">
    <div class="max-w-[var(--container)] mx-auto px-6">

      <div class="text-center mb-14 reveal">
        <p class="section-label">OUR TEAM</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight">
          The People Behind<br>
          <em class="font-accent not-italic text-accent-400">Every Project</em>
        </h2>
        <p class="text-text-2 mt-4 max-w-xl mx-auto text-[length:var(--text-body-lg)]">
          A tight-knit leadership team with decades of combined experience in construction, project management, and technology.
        </p>
      </div>

      <?php
      $team = [
          [
              'name'     => 'Rakesh Patel',
              'role'     => 'Chief Executive Officer',
              'initials' => 'RP',
              'image'    => '/assets/images/team/rakesh-patel.jpg',
              'bio'      => 'Visionary leader with decades of experience in construction and real estate development across the Southeast. Rakesh sets the strategic direction for Moksha Construction and oversees all operations.',
          ],
          [
              'name'     => 'Parth Patel',
              'role'     => 'Managing Director',
              'initials' => 'PP',
              'image'    => '/assets/images/team/parth-patel.jpg',
              'bio'      => 'Oversees overall company operations, setting strategic direction and driving growth across all construction activities. Leads business development, secures new projects, and maintains key relationships with clients, developers, and subcontractors. Works closely with the executive team to ensure projects are delivered on time, within budget, and to the highest quality standards while expanding the company\'s market presence.',
          ],
          [
              'name'     => 'Hari Patel',
              'role'     => 'Chief Financial Officer',
              'initials' => 'HP',
              'image'    => '/assets/images/team/hari-patel.jpg',
              'bio'      => 'Serves as a key member of the executive leadership team, guiding the company\'s financial strategy and decision-making. Oversees budgeting, job costing, cash flow, and financial reporting across all construction projects. Partners with leadership and project teams to manage costs, control risk, and maintain profitability, while supporting long-term growth, financing, and investment planning.',
          ],
          [
              'name'     => 'Parth Patel',
              'role'     => 'Chief Technology Officer',
              'initials' => 'PP',
              'image'    => '/assets/images/team/parth-patel-tech.jpg',
              'bio'      => 'Bridges the gap between construction and technology — implementing BIM workflows, project management systems, and digital tools that keep Moksha ahead of the curve.',
          ],
      ];
      ?>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($team as $i => $member): ?>
        <div class="card p-0 overflow-hidden group reveal <?= $i > 0 ? 'reveal-delay-' . min($i, 3) : '' ?>">
          <!-- Photo -->
          <div class="relative aspect-[3/4] bg-gradient-to-br from-brand-900 to-brand-700 overflow-hidden">
            <img
              src="<?= htmlspecialchars($member['image']) ?>"
              alt="<?= htmlspecialchars($member['name']) ?>, <?= htmlspecialchars($member['role']) ?> at Moksha Construction"
              class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105"
              loading="lazy"
              onerror="this.style.display='none';this.nextElementSibling.style.display=''"
              onload="this.nextElementSibling.style.display='none'"
            >
            <!-- Fallback monogram -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
              <span class="text-6xl font-bold text-accent-400 opacity-30 select-none"><?= $member['initials'] ?></span>
            </div>
            <!-- Bottom gradient for text legibility -->
            <div class="absolute bottom-0 left-0 right-0 h-1/3 bg-gradient-to-t from-void/80 to-transparent"></div>
          </div>

          <!-- Info -->
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
       VALUES
  ============================================================ -->
  <section class="py-(--section-y)">
    <div class="max-w-[var(--container)] mx-auto px-6">

      <div class="text-center mb-14 reveal">
        <p class="section-label">WHAT WE STAND FOR</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight">The Principles We Build On</h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Integrity -->
        <div class="card p-8 reveal">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-3">Integrity</h3>
          <p class="text-text-2 leading-relaxed text-sm">
            We say what we mean and build what we promise. Our estimates are honest. Our timelines are realistic. When something goes wrong — and in construction, it sometimes does — we own it and fix it. No finger-pointing, no hidden costs.
          </p>
        </div>

        <!-- Transparency -->
        <div class="card p-8 reveal reveal-delay-1">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-3">Transparency</h3>
          <p class="text-text-2 leading-relaxed text-sm">
            Every client gets full visibility into their project. Detailed budgets with line-item breakdowns. Weekly progress reports with photos. Open access to our project management systems. You'll never wonder what's happening on your job site.
          </p>
        </div>

        <!-- Accountability -->
        <div class="card p-8 reveal reveal-delay-2">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.35 3.836c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m8.9-4.414c.376.023.75.05 1.124.08 1.131.094 1.976 1.057 1.976 2.192V16.5A2.25 2.25 0 0118 18.75h-2.25m-7.5-10.5H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V18.75m-7.5-10.5h6.375c.621 0 1.125.504 1.125 1.125v9.375"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-3">Accountability</h3>
          <p class="text-text-2 leading-relaxed text-sm">
            We take full responsibility for every project outcome. If we commit to a timeline, we hit it. If we commit to a budget, we track every dollar. When we make a mistake, we acknowledge it, learn from it, and make it right.
          </p>
        </div>

        <!-- Respect -->
        <div class="card p-8 reveal reveal-delay-1">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-3">Respect</h3>
          <p class="text-text-2 leading-relaxed text-sm">
            Your vision comes first. We listen before we recommend. We respect diverse cultural backgrounds and incorporate specific preferences into our work — from residential homes to religious structures. We also respect the environment, prioritizing sustainable practices in every build.
          </p>
        </div>

        <!-- Compliance -->
        <div class="card p-8 reveal reveal-delay-2">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-3">Compliance</h3>
          <p class="text-text-2 leading-relaxed text-sm">
            Fully licensed, bonded, and insured. We comply with all applicable building codes, safety regulations, OSHA requirements, and industry standards. Safety isn't a checkbox — it's a daily priority on every Moksha job site.
          </p>
        </div>

        <!-- Multi-State (bonus card — ties compliance with expansion story) -->
        <div class="card p-8 reveal reveal-delay-3 border-accent-400/20 bg-gradient-to-br from-surface to-brand-950">
          <div class="w-12 h-12 rounded-xl bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-6 h-6 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z"/>
            </svg>
          </div>
          <h3 class="text-[length:var(--text-h3)] font-bold mb-3">Licensed Across 5 States</h3>
          <p class="text-text-2 leading-relaxed text-sm">
            Active licenses in Tennessee, Texas, and North Carolina — with Georgia, South Carolina, and Florida in progress. One trusted contractor, no geographic boundaries.
          </p>
          <div class="mt-4 flex flex-wrap gap-2">
            <span class="text-xs font-semibold bg-accent-400/15 text-accent-400 border border-accent-400/25 px-3 py-1 rounded-full">TN</span>
            <span class="text-xs font-semibold bg-accent-400/15 text-accent-400 border border-accent-400/25 px-3 py-1 rounded-full">TX</span>
            <span class="text-xs font-semibold bg-accent-400/15 text-accent-400 border border-accent-400/25 px-3 py-1 rounded-full">NC</span>
            <span class="text-xs font-semibold bg-brand-400/10 text-brand-300 border border-brand-400/20 px-3 py-1 rounded-full">GA soon</span>
            <span class="text-xs font-semibold bg-brand-400/10 text-brand-300 border border-brand-400/20 px-3 py-1 rounded-full">SC soon</span>
            <span class="text-xs font-semibold bg-brand-400/10 text-brand-300 border border-brand-400/20 px-3 py-1 rounded-full">FL soon</span>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
       SERVICE AREA MAP PLACEHOLDER
  ============================================================ -->
  <section class="py-(--section-y) bg-subtle">
    <div class="max-w-[var(--container)] mx-auto px-6">

      <div class="text-center mb-12 reveal">
        <p class="section-label">WHERE WE BUILD</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight">Serving the Southeast — and Growing</h2>
        <p class="text-text-2 mt-4 text-[length:var(--text-body-lg)]">
          With offices in Nashville and Atlanta — also serving Clarksville and communities across the Southeast.
        </p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Interactive Leaflet Map -->
        <div class="lg:col-span-2 reveal">
          <?php $map_id = 'about-service-map'; $map_height = '460px'; require __DIR__ . '/includes/service-area-map.php'; ?>
          <p class="mt-4 text-sm text-text-3">
            <span class="text-accent-400 font-semibold uppercase tracking-wider text-xs mr-1">Also serving:</span>
            Clarksville, TN | Murfreesboro, TN | Franklin, TN | Decatur, GA | and surrounding communities
          </p>
        </div>

        <!-- Office cards + badge -->
        <div class="space-y-4 reveal reveal-delay-1">

          <!-- Nashville -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div>
                <h3 class="font-semibold text-text mb-1">Nashville Office</h3>
                <p class="text-sm text-text-2 leading-relaxed">
                  <?= OFFICE_NASHVILLE['street'] ?><br>
                  <?= OFFICE_NASHVILLE['city'] ?>, <?= OFFICE_NASHVILLE['state'] ?> <?= OFFICE_NASHVILLE['zip'] ?>
                </p>
                <a href="https://maps.google.com/?q=315+Deaderick+Street+Suite+1550+Nashville+TN" target="_blank" rel="noopener noreferrer" class="text-xs text-accent-400 hover:text-accent-300 mt-2 inline-block transition-colors">View on Map &rarr;</a>
              </div>
            </div>
          </div>

          <!-- Atlanta -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              </div>
              <div>
                <h3 class="font-semibold text-text mb-1">Atlanta Office</h3>
                <p class="text-sm text-text-2 leading-relaxed">
                  <?= OFFICE_ATLANTA['street'] ?><br>
                  <?= OFFICE_ATLANTA['city'] ?>, <?= OFFICE_ATLANTA['state'] ?> <?= OFFICE_ATLANTA['zip'] ?>
                </p>
                <a href="https://maps.google.com/?q=1+W+Court+Square+Decatur+GA+30030" target="_blank" rel="noopener noreferrer" class="text-xs text-accent-400 hover:text-accent-300 mt-2 inline-block transition-colors">View on Map &rarr;</a>
              </div>
            </div>
          </div>

          <!-- Multi-state badge -->
          <div class="rounded-xl border border-accent-400/20 bg-accent-400/5 p-5">
            <p class="text-xs font-semibold text-accent-400 uppercase tracking-widest mb-2">Licensed States</p>
            <p class="text-sm text-text-2 mb-1"><span class="text-text font-medium">Active:</span> Tennessee &middot; Texas &middot; North Carolina</p>
            <p class="text-sm text-text-3"><span class="text-text-2 font-medium">Expanding:</span> Georgia &middot; South Carolina &middot; Florida</p>
          </div>

        </div>
      </div>
    </div>
  </section>

  <!-- ============================================================
       PARTNERS
  ============================================================ -->
  <section class="py-(--section-y)">
    <div class="max-w-[var(--container)] mx-auto px-6">

      <div class="text-center mb-12 reveal">
        <p class="section-label">TRUSTED PARTNERS</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight">Built With the Best</h2>
        <p class="text-text-2 mt-3 text-[length:var(--text-body-lg)]">We partner with industry-leading suppliers and manufacturers to guarantee material quality on every project.</p>
      </div>

      <?php
      $partnerLogos = [
          ['name' => "Lowe's",                       'href' => 'https://lowes.com',          'src' => '/assets/images/partners/lowes.png'],
          ['name' => 'Sherwin-Williams',             'href' => 'https://sherwin-williams.com','src' => '/assets/images/partners/sherwin-williams.png'],
          ['name' => 'MSI Surfaces',                 'href' => 'https://msisurfaces.com',    'src' => '/assets/images/partners/msi.svg'],
          ['name' => 'United Rentals',               'href' => 'https://unitedrentals.com',  'src' => '/assets/images/partners/united-rentals.svg'],
          ['name' => 'Michael Graves',               'href' => 'https://michaelgraves.com',  'src' => '/assets/images/partners/michael-graves.svg'],
          ['name' => 'ApeTech',                      'href' => 'https://apete.ch',           'src' => '/assets/images/partners/apetech.svg'],
      ];
      ?>
      <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 lg:gap-5 items-stretch reveal reveal-delay-1">
        <?php foreach ($partnerLogos as $p): ?>
          <a
            href="<?= htmlspecialchars($p['href']) ?>"
            <?= $p['href'] !== '#' ? 'target="_blank" rel="noopener noreferrer"' : '' ?>
            aria-label="<?= htmlspecialchars($p['name']) ?> — Moksha Construction partner"
            class="partner-logo"
          >
            <img
              src="<?= htmlspecialchars($p['src']) ?>"
              alt="<?= htmlspecialchars($p['name']) ?>"
              loading="lazy"
              width="180"
              height="80"
            >
          </a>
        <?php endforeach; ?>
      </div>

    </div>
  </section>

  <!-- ============================================================
       CTA BANNER
  ============================================================ -->
  <?php require __DIR__ . '/includes/cta-banner.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
