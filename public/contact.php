<?php
$page_title       = 'Contact Moksha Construction | Free Estimates | (615) 234-0272';
$page_description = 'Contact Moksha Construction for a free construction estimate. Offices in Nashville and Atlanta. Call (615) 234-0272 or submit our quote request form. We respond within 48 hours.';
$page_url         = '/contact';
$current_page     = 'contact';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Contact'],
];

require_once __DIR__ . '/includes/header.php';
?>

  <!-- ============================================================
       HERO (Short — 40vh)
  ============================================================ -->
  <section class="relative flex items-end pb-16 pt-40 overflow-hidden" style="min-height: 40vh;">
    <!-- Background -->
    <div class="absolute inset-0">
      <img
        src="/assets/images/services/design-build-hero.webp"
        alt="Moksha Construction — let's build something together"
        class="w-full h-full object-cover"
        fetchpriority="high"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-base via-base/70 to-base/30"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-base/60 to-transparent"></div>
    </div>

    <!-- Breadcrumb -->
    <div class="absolute top-28 left-0 right-0">
      <div class="max-w-[var(--container)] mx-auto px-6">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-text-3">
          <a href="/" class="hover:text-accent-400 transition-colors">Home</a>
          <span aria-hidden="true">/</span>
          <span class="text-text-2">Contact</span>
        </nav>
      </div>
    </div>

    <div class="relative max-w-[var(--container)] mx-auto px-6">
      <p class="section-label reveal">GET IN TOUCH</p>
      <h1 class="text-(--text-display) font-bold tracking-tight mb-4 reveal reveal-delay-1">
        Let's Build Something<br>
        <em class="font-accent not-italic text-accent-400">Together</em>
      </h1>
      <p class="text-(--text-body-lg) text-text-2 reveal reveal-delay-2">
        Free estimates. No obligation. We respond within 48 hours.
      </p>
    </div>
  </section>

  <!-- ============================================================
       FORM + CONTACT INFO (Two-column)
  ============================================================ -->
  <section id="quote" class="py-(--section-y)">
    <div class="max-w-[var(--container)] mx-auto px-6">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 xl:gap-16">

        <!-- LEFT: Quote Form (60% → 3/5 cols) -->
        <div class="lg:col-span-3 reveal" x-data="contactForm()">

          <h2 class="text-(--text-h2) font-bold tracking-tight mb-2">Request a Free Quote</h2>
          <p class="text-text-2 mb-8">Tell us about your project. The more detail you share, the better we can prepare for your first conversation.</p>

          <!-- Success State -->
          <div
            x-show="submitted"
            x-transition:enter="transition ease-out duration-400"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="rounded-2xl border border-[oklch(72%_0.20_145/0.30)] bg-[oklch(72%_0.20_145/0.06)] p-8 text-center"
            x-cloak
          >
            <div class="w-14 h-14 rounded-full bg-[oklch(72%_0.20_145/0.15)] border border-[oklch(72%_0.20_145/0.30)] flex items-center justify-center mx-auto mb-4">
              <svg class="w-7 h-7 text-[oklch(72%_0.20_145)]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
              </svg>
            </div>
            <h3 class="text-(--text-h3) font-bold mb-2">Request Received</h3>
            <p class="text-text-2 text-sm leading-relaxed max-w-sm mx-auto">
              Thank you! We've received your quote request and will respond within 48 hours. If your project is urgent, call us directly at <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-accent-400 hover:text-accent-300 transition-colors"><?= SITE_PHONE ?></a>.
            </p>
          </div>

          <!-- Form -->
          <form
            x-show="!submitted"
            @submit.prevent="submitForm()"
            novalidate
            class="space-y-6"
          >
            <!-- Name row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="first_name" class="form-label">First Name <span class="text-accent-400" aria-label="required">*</span></label>
                <input
                  type="text"
                  id="first_name"
                  name="first_name"
                  x-model="form.first_name"
                  placeholder="Parth"
                  required
                  autocomplete="given-name"
                  class="form-input"
                  :class="errors.first_name ? 'border-[oklch(68%_0.24_25)]' : ''"
                >
                <p x-show="errors.first_name" x-text="errors.first_name" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>
              <div>
                <label for="last_name" class="form-label">Last Name <span class="text-accent-400" aria-label="required">*</span></label>
                <input
                  type="text"
                  id="last_name"
                  name="last_name"
                  x-model="form.last_name"
                  placeholder="Patel"
                  required
                  autocomplete="family-name"
                  class="form-input"
                  :class="errors.last_name ? 'border-[oklch(68%_0.24_25)]' : ''"
                >
                <p x-show="errors.last_name" x-text="errors.last_name" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>
            </div>

            <!-- Email + Phone row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="email" class="form-label">Email Address <span class="text-accent-400" aria-label="required">*</span></label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  x-model="form.email"
                  placeholder="you@company.com"
                  required
                  autocomplete="email"
                  class="form-input"
                  :class="errors.email ? 'border-[oklch(68%_0.24_25)]' : ''"
                >
                <p x-show="errors.email" x-text="errors.email" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>
              <div>
                <label for="phone" class="form-label">Phone Number <span class="text-accent-400" aria-label="required">*</span></label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  x-model="form.phone"
                  placeholder="(615) 000-0000"
                  required
                  autocomplete="tel"
                  class="form-input"
                  :class="errors.phone ? 'border-[oklch(68%_0.24_25)]' : ''"
                >
                <p x-show="errors.phone" x-text="errors.phone" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>
            </div>

            <!-- Company -->
            <div>
              <label for="company" class="form-label">Company Name <span class="text-text-3 font-normal">(optional)</span></label>
              <input
                type="text"
                id="company"
                name="company"
                x-model="form.company"
                placeholder="Acme Corp"
                autocomplete="organization"
                class="form-input"
              >
            </div>

            <!-- Project Type + Location row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="project_type" class="form-label">Project Type <span class="text-accent-400" aria-label="required">*</span></label>
                <select
                  id="project_type"
                  name="project_type"
                  x-model="form.project_type"
                  required
                  class="form-input"
                  :class="errors.project_type ? 'border-[oklch(68%_0.24_25)]' : ''"
                >
                  <option value="" disabled selected>Select a type...</option>
                  <option value="Residential">Residential</option>
                  <option value="Commercial">Commercial</option>
                  <option value="Industrial">Industrial</option>
                  <option value="Religious / Cultural">Religious / Cultural</option>
                  <option value="Other">Other</option>
                </select>
                <p x-show="errors.project_type" x-text="errors.project_type" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>
              <div>
                <label for="location" class="form-label">Project Location</label>
                <input
                  type="text"
                  id="location"
                  name="location"
                  x-model="form.location"
                  placeholder="Nashville, TN"
                  class="form-input"
                >
              </div>
            </div>

            <!-- Budget + Timeline row -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="budget" class="form-label">Estimated Budget Range</label>
                <select
                  id="budget"
                  name="budget"
                  x-model="form.budget"
                  class="form-input"
                >
                  <option value="" disabled selected>Select a range...</option>
                  <option value="Under $100K">Under $100K</option>
                  <option value="$100K – $500K">$100K – $500K</option>
                  <option value="$500K – $1M">$500K – $1M</option>
                  <option value="$1M – $5M">$1M – $5M</option>
                  <option value="$5M+">$5M+</option>
                  <option value="Not Sure">Not Sure</option>
                </select>
              </div>
              <div>
                <label for="timeline" class="form-label">Estimated Timeline</label>
                <select
                  id="timeline"
                  name="timeline"
                  x-model="form.timeline"
                  class="form-input"
                >
                  <option value="" disabled selected>Select a timeline...</option>
                  <option value="ASAP">ASAP</option>
                  <option value="1 – 3 Months">1 – 3 Months</option>
                  <option value="3 – 6 Months">3 – 6 Months</option>
                  <option value="6 – 12 Months">6 – 12 Months</option>
                  <option value="Just Planning">Just Planning</option>
                </select>
              </div>
            </div>

            <!-- Message -->
            <div>
              <label for="message" class="form-label">Tell Us About Your Project <span class="text-accent-400" aria-label="required">*</span></label>
              <textarea
                id="message"
                name="message"
                x-model="form.message"
                rows="5"
                required
                placeholder="Describe your project — size, scope, location, any special requirements..."
                class="form-input resize-none"
                :class="errors.message ? 'border-[oklch(68%_0.24_25)]' : ''"
              ></textarea>
              <p x-show="errors.message" x-text="errors.message" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
            </div>

            <!-- Submit button -->
            <div>
              <button
                type="submit"
                class="btn-primary w-full sm:w-auto justify-center"
                :disabled="loading"
                :class="loading && 'opacity-70 cursor-not-allowed'"
              >
                <span x-show="!loading">Submit Quote Request</span>
                <span x-show="loading" x-cloak class="flex items-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  Sending...
                </span>
                <span aria-hidden="true" x-show="!loading">&rarr;</span>
              </button>

              <!-- General error -->
              <p x-show="submitError" x-text="submitError" class="text-[oklch(68%_0.24_25)] text-sm mt-3" x-cloak></p>
            </div>

          </form>

          <!-- Response promise -->
          <p class="mt-6 text-text-3 text-sm">
            We respond to every inquiry within 48 hours. If your project is urgent, call us directly at
            <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-accent-400 hover:text-accent-300 transition-colors"><?= SITE_PHONE ?></a>.
          </p>

        </div><!-- /form col -->

        <!-- RIGHT: Contact Info (40% → 2/5 cols) -->
        <div class="lg:col-span-2 space-y-4 reveal reveal-delay-2">

          <h2 class="text-(--text-h3) font-bold tracking-tight mb-6">Contact Information</h2>

          <!-- Call Us -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-text mb-1">Call Us</h3>
                <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-accent-400 hover:text-accent-300 transition-colors font-medium text-lg block"><?= SITE_PHONE ?></a>
                <p class="text-xs text-text-3 mt-1">Available Monday – Friday, 8am – 6pm CST</p>
              </div>
            </div>
          </div>

          <!-- Email Us -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>
                </svg>
              </div>
              <div>
                <h3 class="font-semibold text-text mb-1">Email Us</h3>
                <a href="mailto:<?= SITE_EMAIL ?>" class="text-accent-400 hover:text-accent-300 transition-colors font-medium block"><?= SITE_EMAIL ?></a>
                <p class="text-xs text-text-3 mt-1">We respond to all inquiries within one business day.</p>
              </div>
            </div>
          </div>

          <!-- Nashville Office -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
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

          <!-- Atlanta Office -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                  <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
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

          <!-- Social Links -->
          <div class="card p-6">
            <h3 class="font-semibold text-text mb-4">Follow Us</h3>
            <div class="flex items-center gap-3">
              <a href="<?= SOCIAL_INSTAGRAM ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow Moksha Construction on Instagram"
                 class="w-10 h-10 rounded-lg bg-surface border border-[oklch(100%_0_0/0.08)] flex items-center justify-center text-text-3 hover:text-accent-400 hover:border-accent-400/30 transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
              </a>
              <a href="<?= SOCIAL_FACEBOOK ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow Moksha Construction on Facebook"
                 class="w-10 h-10 rounded-lg bg-surface border border-[oklch(100%_0_0/0.08)] flex items-center justify-center text-text-3 hover:text-accent-400 hover:border-accent-400/30 transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
              </a>
              <a href="<?= SOCIAL_LINKEDIN ?>" target="_blank" rel="noopener noreferrer" aria-label="Follow Moksha Construction on LinkedIn"
                 class="w-10 h-10 rounded-lg bg-surface border border-[oklch(100%_0_0/0.08)] flex items-center justify-center text-text-3 hover:text-accent-400 hover:border-accent-400/30 transition-all">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
              </a>
            </div>
          </div>

        </div><!-- /right col -->

      </div><!-- /grid -->
    </div><!-- /container -->
  </section>

  <!-- ============================================================
       MAP SECTION (Full-width placeholder)
  ============================================================ -->
  <section class="bg-void border-t border-[oklch(100%_0_0/0.06)]">
    <div
      class="relative flex items-center justify-center overflow-hidden"
      style="height: 400px;"
      aria-label="Office locations map placeholder"
    >
      <!-- Dark map placeholder -->
      <div class="absolute inset-0 bg-gradient-to-br from-brand-950 via-void to-base opacity-90"></div>

      <!-- Grid lines for map texture -->
      <div class="absolute inset-0 opacity-5"
           style="background-image: linear-gradient(oklch(100% 0 0 / 1) 1px, transparent 1px), linear-gradient(90deg, oklch(100% 0 0 / 1) 1px, transparent 1px); background-size: 40px 40px;">
      </div>

      <!-- Office pin markers -->
      <div class="absolute" style="top: 42%; left: 38%;">
        <div class="relative flex flex-col items-center">
          <div class="w-4 h-4 rounded-full bg-accent-400 shadow-[0_0_16px_oklch(88%_0.24_97/0.7)] animate-pulse"></div>
          <div class="mt-1 bg-surface border border-[oklch(100%_0_0/0.10)] rounded-lg px-3 py-1.5 text-xs text-text-2 whitespace-nowrap shadow-lg">
            Nashville, TN
          </div>
        </div>
      </div>

      <div class="absolute" style="top: 36%; left: 36%;">
        <div class="relative flex flex-col items-center">
          <div class="w-3.5 h-3.5 rounded-full bg-accent-400/80 shadow-[0_0_12px_oklch(88%_0.24_97/0.5)] animate-pulse" style="animation-delay: 0.3s;"></div>
          <div class="mt-1 bg-surface border border-[oklch(100%_0_0/0.10)] rounded-lg px-3 py-1.5 text-xs text-text-2 whitespace-nowrap shadow-lg">
            Clarksville, TN
          </div>
        </div>
      </div>

      <div class="absolute" style="top: 56%; left: 44%;">
        <div class="relative flex flex-col items-center">
          <div class="w-3.5 h-3.5 rounded-full bg-brand-400 shadow-[0_0_12px_oklch(62%_0.22_310/0.6)] animate-pulse" style="animation-delay: 0.6s;"></div>
          <div class="mt-1 bg-surface border border-[oklch(100%_0_0/0.10)] rounded-lg px-3 py-1.5 text-xs text-text-2 whitespace-nowrap shadow-lg">
            Atlanta, GA
          </div>
        </div>
      </div>

      <!-- Center label -->
      <div class="relative text-center">
        <p class="text-text-4 text-sm">Interactive map — coming soon</p>
        <p class="text-text-4 text-xs mt-1">Nashville &middot; Clarksville &middot; Atlanta</p>
      </div>
    </div>
  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<script>
function contactForm() {
  return {
    form: {
      first_name:   '',
      last_name:    '',
      email:        '',
      phone:        '',
      company:      '',
      project_type: '',
      location:     '',
      budget:       '',
      timeline:     '',
      message:      '',
    },
    errors:      {},
    loading:     false,
    submitted:   false,
    submitError: '',

    validate() {
      this.errors = {};
      if (!this.form.first_name.trim())   this.errors.first_name   = 'First name is required.';
      if (!this.form.last_name.trim())    this.errors.last_name    = 'Last name is required.';
      if (!this.form.email.trim())        this.errors.email        = 'Email address is required.';
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) this.errors.email = 'Please enter a valid email address.';
      if (!this.form.phone.trim())        this.errors.phone        = 'Phone number is required.';
      if (!this.form.project_type)        this.errors.project_type = 'Please select a project type.';
      if (!this.form.message.trim())      this.errors.message      = 'Please tell us about your project.';
      return Object.keys(this.errors).length === 0;
    },

    async submitForm() {
      this.submitError = '';
      if (!this.validate()) return;

      this.loading = true;
      try {
        const payload = new FormData();
        Object.entries(this.form).forEach(([k, v]) => payload.append(k, v));
        payload.append('source', 'website-contact');
        payload.append('submitted_at', new Date().toISOString());

        const res = await fetch('<?= FORM_ENDPOINT ?>', {
          method:   'POST',
          body:     payload,
          redirect: 'follow',
        });

        if (res.ok) {
          this.submitted = true;
        } else {
          this.submitError = 'Something went wrong. Please try again or call us directly at <?= SITE_PHONE ?>.';
        }
      } catch (err) {
        this.submitError = 'Unable to send your request. Please call us at <?= SITE_PHONE ?> or email <?= SITE_EMAIL ?>.';
      } finally {
        this.loading = false;
      }
    },
  };
}
</script>
