<?php
$page_title       = 'Become a Subcontractor | Work With Moksha Construction';
$page_description = 'Apply to subcontract with Moksha Construction. We work with reliable trade partners across Tennessee, Texas, and North Carolina on residential, commercial, and industrial projects. Prompt pay, professional PMs, growing pipeline.';
$page_url         = '/subcontractors';
$current_page     = 'subcontractors';

$breadcrumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Subcontractors'],
];

require_once __DIR__ . '/includes/header.php';
?>

  <!-- ============================================================
       HERO
  ============================================================ -->
  <section class="relative flex items-end pb-16 pt-40 overflow-hidden" style="min-height: 50vh;">
    <div class="absolute inset-0">
      <img
        src="/assets/images/services/general-contracting-1.jpg"
        alt="Moksha Construction crew on an active commercial job site"
        class="w-full h-full object-cover"
        fetchpriority="high"
      >
      <div class="absolute inset-0 bg-gradient-to-t from-base via-base/70 to-base/30"></div>
      <div class="absolute inset-0 bg-gradient-to-r from-base/70 to-transparent"></div>
    </div>

    <div class="absolute top-28 left-0 right-0">
      <div class="max-w-[var(--container)] mx-auto px-6">
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs text-text-3">
          <a href="/" class="hover:text-accent-400 transition-colors">Home</a>
          <span aria-hidden="true">/</span>
          <span class="text-text-2">Subcontractors</span>
        </nav>
      </div>
    </div>

    <div class="relative max-w-[var(--container)] mx-auto px-6">
      <p class="section-label reveal">TRADE PARTNERS</p>
      <h1 class="text-[length:var(--text-display)] font-bold tracking-tight mb-4 reveal reveal-delay-1">
        Build With <em class="font-accent not-italic text-accent-400">Moksha</em>
      </h1>
      <p class="text-[length:var(--text-body-lg)] text-text-2 max-w-2xl reveal reveal-delay-2">
        Reliable trade partners are the backbone of every project we deliver. If your crew shows up, communicates well, and stands behind its work — we want to hear from you.
      </p>
    </div>
  </section>

  <!-- ============================================================
       WHY SUB WITH MOKSHA — Benefits Grid
  ============================================================ -->
  <section class="py-(--section-y) bg-void border-t border-[oklch(100%_0_0/0.06)]">
    <div class="max-w-[var(--container)] mx-auto px-6">
      <div class="text-center mb-14">
        <p class="section-label reveal">WHY SUB WITH US</p>
        <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight mt-2 reveal reveal-delay-1">
          A Partner That <em class="font-accent not-italic text-accent-400">Pays On Time</em>
        </h2>
        <p class="text-text-2 mt-4 max-w-2xl mx-auto reveal reveal-delay-2">
          We treat our trade partners the way we'd want to be treated — clear scopes, fast answers, and predictable pay schedules.
        </p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Benefit 1 -->
        <div class="card p-7 reveal">
          <div class="w-11 h-11 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold mb-2">Prompt Pay, Every Time</h3>
          <p class="text-sm text-text-2 leading-relaxed">Net-30 from approved pay app. No chasing checks. No retainage games. We move money the way we move dirt — on schedule.</p>
        </div>

        <!-- Benefit 2 -->
        <div class="card p-7 reveal reveal-delay-1">
          <div class="w-11 h-11 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold mb-2">Clear Scopes</h3>
          <p class="text-sm text-text-2 leading-relaxed">Bid packages with full drawings, specs, and inclusions/exclusions documented up front. No guessing what's in scope.</p>
        </div>

        <!-- Benefit 3 -->
        <div class="card p-7 reveal reveal-delay-2">
          <div class="w-11 h-11 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold mb-2">Professional PMs</h3>
          <p class="text-sm text-text-2 leading-relaxed">Real project managers on every job. RFIs answered in hours, not weeks. Decisions made in the field, not three emails away.</p>
        </div>

        <!-- Benefit 4 -->
        <div class="card p-7 reveal">
          <div class="w-11 h-11 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold mb-2">Steady Pipeline</h3>
          <p class="text-sm text-text-2 leading-relaxed">Active projects across Tennessee, Texas, and North Carolina — with Georgia, South Carolina, and Florida next. Repeat work for partners who deliver.</p>
        </div>

        <!-- Benefit 5 -->
        <div class="card p-7 reveal reveal-delay-1">
          <div class="w-11 h-11 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold mb-2">Safety First, Always</h3>
          <p class="text-sm text-text-2 leading-relaxed">OSHA-compliant sites with site-specific safety plans. We protect your people because we expect them back tomorrow.</p>
        </div>

        <!-- Benefit 6 -->
        <div class="card p-7 reveal reveal-delay-2">
          <div class="w-11 h-11 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center mb-5">
            <svg class="w-5 h-5 text-accent-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
          </div>
          <h3 class="text-lg font-bold mb-2">Modern Tools</h3>
          <p class="text-sm text-text-2 leading-relaxed">BIM coordination, Trimble layout, Procore for plans and submittals. Less standing around, more building.</p>
        </div>

      </div>
    </div>
  </section>

  <!-- ============================================================
       APPLICATION FORM
  ============================================================ -->
  <section id="apply" class="py-(--section-y)">
    <div class="max-w-[var(--container)] mx-auto px-6">
      <div class="grid grid-cols-1 lg:grid-cols-5 gap-12 xl:gap-16">

        <!-- LEFT: Form (3/5 cols) -->
        <div class="lg:col-span-3 reveal" x-data="subcontractorForm()">

          <h2 class="text-[length:var(--text-h2)] font-bold tracking-tight mb-2">Subcontractor Application</h2>
          <p class="text-text-2 mb-8">Tell us about your company. Our PMs review every application and reach out within 5 business days.</p>

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
            <h3 class="text-[length:var(--text-h3)] font-bold mb-2">Application Received</h3>
            <p class="text-text-2 text-sm leading-relaxed max-w-md mx-auto">
              Thanks for applying. We've added your company to our trade partner list and a PM will reach out within 5 business days. For urgent inquiries, call us at <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-accent-400 hover:text-accent-300 transition-colors"><?= SITE_PHONE ?></a>.
            </p>
          </div>

          <!-- Form -->
          <form
            x-show="!submitted"
            @submit.prevent="submitForm()"
            novalidate
            class="space-y-6"
          >

            <!-- Honeypot (hidden) -->
            <input type="text" name="website_url" x-model="form.website_url" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;width:1px;height:1px;opacity:0" aria-hidden="true">

            <!-- ===== Contact ===== -->
            <fieldset class="space-y-6">
              <legend class="text-xs font-bold tracking-widest uppercase text-accent-400 mb-3">Contact Information</legend>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="first_name" class="form-label">First Name <span class="text-accent-400">*</span></label>
                  <input type="text" id="first_name" name="first_name" x-model="form.first_name" placeholder="John" required autocomplete="given-name" class="form-input" :class="errors.first_name ? 'border-[oklch(68%_0.24_25)]' : ''">
                  <p x-show="errors.first_name" x-text="errors.first_name" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
                </div>
                <div>
                  <label for="last_name" class="form-label">Last Name <span class="text-accent-400">*</span></label>
                  <input type="text" id="last_name" name="last_name" x-model="form.last_name" placeholder="Smith" required autocomplete="family-name" class="form-input" :class="errors.last_name ? 'border-[oklch(68%_0.24_25)]' : ''">
                  <p x-show="errors.last_name" x-text="errors.last_name" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="email" class="form-label">Email <span class="text-accent-400">*</span></label>
                  <input type="email" id="email" name="email" x-model="form.email" placeholder="you@company.com" required autocomplete="email" class="form-input" :class="errors.email ? 'border-[oklch(68%_0.24_25)]' : ''">
                  <p x-show="errors.email" x-text="errors.email" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
                </div>
                <div>
                  <label for="phone" class="form-label">Phone <span class="text-accent-400">*</span></label>
                  <input type="tel" id="phone" name="phone" x-model="form.phone" placeholder="(615) 000-0000" required autocomplete="tel" class="form-input" :class="errors.phone ? 'border-[oklch(68%_0.24_25)]' : ''">
                  <p x-show="errors.phone" x-text="errors.phone" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
                </div>
              </div>
            </fieldset>

            <!-- ===== Company ===== -->
            <fieldset class="space-y-6 pt-4 border-t border-[oklch(100%_0_0/0.06)]">
              <legend class="text-xs font-bold tracking-widest uppercase text-accent-400 mb-3">Company</legend>

              <div>
                <label for="company_name" class="form-label">Company Name <span class="text-accent-400">*</span></label>
                <input type="text" id="company_name" name="company_name" x-model="form.company_name" placeholder="Acme Electrical, LLC" required autocomplete="organization" class="form-input" :class="errors.company_name ? 'border-[oklch(68%_0.24_25)]' : ''">
                <p x-show="errors.company_name" x-text="errors.company_name" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>

              <div>
                <label for="website" class="form-label">Website <span class="text-text-3 font-normal">(optional)</span></label>
                <input type="url" id="website" name="website" x-model="form.website" placeholder="https://yourcompany.com" autocomplete="url" class="form-input">
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="years_in_business" class="form-label">Years in Business</label>
                  <select id="years_in_business" name="years_in_business" x-model="form.years_in_business" class="form-input">
                    <option value="" disabled selected>Select...</option>
                    <option value="< 1 year">Less than 1 year</option>
                    <option value="1 – 3 years">1 – 3 years</option>
                    <option value="4 – 10 years">4 – 10 years</option>
                    <option value="11 – 20 years">11 – 20 years</option>
                    <option value="20+ years">20+ years</option>
                  </select>
                </div>
                <div>
                  <label for="company_size" class="form-label">Company Size</label>
                  <select id="company_size" name="company_size" x-model="form.company_size" class="form-input">
                    <option value="" disabled selected>Select...</option>
                    <option value="1 – 5">1 – 5 employees</option>
                    <option value="6 – 20">6 – 20 employees</option>
                    <option value="21 – 50">21 – 50 employees</option>
                    <option value="51 – 100">51 – 100 employees</option>
                    <option value="100+">100+ employees</option>
                  </select>
                </div>
              </div>
            </fieldset>

            <!-- ===== Trade & Service Area ===== -->
            <fieldset class="space-y-6 pt-4 border-t border-[oklch(100%_0_0/0.06)]">
              <legend class="text-xs font-bold tracking-widest uppercase text-accent-400 mb-3">Trade &amp; Service Area</legend>

              <div>
                <label for="trade" class="form-label">Primary Trade / Specialty <span class="text-accent-400">*</span></label>
                <select id="trade" name="trade" x-model="form.trade" required class="form-input" :class="errors.trade ? 'border-[oklch(68%_0.24_25)]' : ''">
                  <option value="" disabled selected>Select your trade...</option>
                  <option value="Sitework / Excavation">Sitework / Excavation</option>
                  <option value="Concrete / Foundations">Concrete / Foundations</option>
                  <option value="Masonry">Masonry</option>
                  <option value="Steel / Structural">Steel / Structural</option>
                  <option value="Framing / Carpentry">Framing / Carpentry</option>
                  <option value="Roofing">Roofing</option>
                  <option value="Mechanical / HVAC">Mechanical / HVAC</option>
                  <option value="Electrical">Electrical</option>
                  <option value="Plumbing">Plumbing</option>
                  <option value="Drywall / Insulation">Drywall / Insulation</option>
                  <option value="Flooring">Flooring</option>
                  <option value="Painting">Painting</option>
                  <option value="Glass / Glazing">Glass / Glazing</option>
                  <option value="Landscaping / Hardscape">Landscaping / Hardscape</option>
                  <option value="Demolition">Demolition</option>
                  <option value="Other">Other</option>
                </select>
                <p x-show="errors.trade" x-text="errors.trade" class="text-[oklch(68%_0.24_25)] text-xs mt-1" x-cloak></p>
              </div>

              <div x-show="form.trade === 'Other'" x-cloak>
                <label for="trades_other" class="form-label">Specify Trade</label>
                <input type="text" id="trades_other" name="trades_other" x-model="form.trades_other" placeholder="Describe your specialty..." class="form-input">
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="states_licensed" class="form-label">States Licensed</label>
                  <input type="text" id="states_licensed" name="states_licensed" x-model="form.states_licensed" placeholder="TN, GA, NC" class="form-input">
                </div>
                <div>
                  <label for="service_area" class="form-label">Service Area</label>
                  <input type="text" id="service_area" name="service_area" x-model="form.service_area" placeholder="Nashville Metro, 100mi" class="form-input">
                </div>
              </div>
            </fieldset>

            <!-- ===== Compliance ===== -->
            <fieldset class="space-y-6 pt-4 border-t border-[oklch(100%_0_0/0.06)]">
              <legend class="text-xs font-bold tracking-widest uppercase text-accent-400 mb-3">Compliance</legend>

              <div>
                <label for="license_number" class="form-label">License Number <span class="text-text-3 font-normal">(if applicable)</span></label>
                <input type="text" id="license_number" name="license_number" x-model="form.license_number" placeholder="e.g. TN-12345" class="form-input">
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="insured" class="form-label">General Liability Insured?</label>
                  <select id="insured" name="insured" x-model="form.insured" class="form-input">
                    <option value="" disabled selected>Select...</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="in_progress">In progress</option>
                  </select>
                </div>
                <div>
                  <label for="bonded" class="form-label">Bonded?</label>
                  <select id="bonded" name="bonded" x-model="form.bonded" class="form-input">
                    <option value="" disabled selected>Select...</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                  </select>
                </div>
              </div>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label for="emr_rating" class="form-label">EMR Rating <span class="text-text-3 font-normal">(optional)</span></label>
                  <input type="text" id="emr_rating" name="emr_rating" x-model="form.emr_rating" placeholder="e.g. 0.85" class="form-input">
                </div>
                <div>
                  <label for="union_status" class="form-label">Union Status</label>
                  <select id="union_status" name="union_status" x-model="form.union_status" class="form-input">
                    <option value="" disabled selected>Select...</option>
                    <option value="union">Union</option>
                    <option value="non_union">Non-Union</option>
                    <option value="either">Either / Both</option>
                  </select>
                </div>
              </div>
            </fieldset>

            <!-- ===== Project Fit ===== -->
            <fieldset class="space-y-6 pt-4 border-t border-[oklch(100%_0_0/0.06)]">
              <legend class="text-xs font-bold tracking-widest uppercase text-accent-400 mb-3">Project Fit</legend>

              <div>
                <label for="project_types_interest" class="form-label">Project Types of Interest</label>
                <input type="text" id="project_types_interest" name="project_types_interest" x-model="form.project_types_interest" placeholder="Commercial, Residential, Industrial..." class="form-input">
              </div>

              <div>
                <label for="largest_project" class="form-label">Largest Project Completed</label>
                <input type="text" id="largest_project" name="largest_project" x-model="form.largest_project" placeholder="$2.5M warehouse fit-out" class="form-input">
              </div>

              <div>
                <label for="references_text" class="form-label">References <span class="text-text-3 font-normal">(GCs you've worked with — optional)</span></label>
                <textarea id="references_text" name="references_text" x-model="form.references_text" rows="3" placeholder="Company name, contact, phone..." class="form-input resize-none"></textarea>
              </div>

              <div>
                <label for="message" class="form-label">Anything Else? <span class="text-text-3 font-normal">(optional)</span></label>
                <textarea id="message" name="message" x-model="form.message" rows="4" placeholder="Tell us anything else we should know about your company..." class="form-input resize-none"></textarea>
              </div>
            </fieldset>

            <!-- Submit -->
            <div class="pt-4">
              <button
                type="submit"
                class="btn-primary w-full sm:w-auto justify-center"
                :disabled="loading"
                :class="loading && 'opacity-70 cursor-not-allowed'"
              >
                <span x-show="!loading">Submit Application</span>
                <span x-show="loading" x-cloak class="flex items-center gap-2">
                  <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                  </svg>
                  Sending...
                </span>
                <span aria-hidden="true" x-show="!loading">&rarr;</span>
              </button>

              <p x-show="submitError" x-text="submitError" class="text-[oklch(68%_0.24_25)] text-sm mt-3" x-cloak></p>
            </div>
          </form>

          <p class="mt-6 text-text-3 text-sm">
            We review every application. PMs typically reach out within 5 business days. Questions? Call <a href="tel:<?= SITE_PHONE_RAW ?>" class="text-accent-400 hover:text-accent-300 transition-colors"><?= SITE_PHONE ?></a> or email <a href="mailto:<?= SITE_EMAIL ?>" class="text-accent-400 hover:text-accent-300 transition-colors"><?= SITE_EMAIL ?></a>.
          </p>

        </div><!-- /form -->

        <!-- RIGHT: Sidebar (2/5 cols) -->
        <div class="lg:col-span-2 space-y-4 reveal reveal-delay-2">

          <h2 class="text-[length:var(--text-h3)] font-bold tracking-tight mb-6">What Happens Next</h2>

          <!-- Step 1 -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-9 h-9 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0 text-accent-400 font-bold text-sm">1</div>
              <div>
                <h3 class="font-semibold text-text mb-1">Submit your application</h3>
                <p class="text-sm text-text-2 leading-relaxed">Fill out the form. We'll receive your information instantly.</p>
              </div>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-9 h-9 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0 text-accent-400 font-bold text-sm">2</div>
              <div>
                <h3 class="font-semibold text-text mb-1">Quick review (5 days)</h3>
                <p class="text-sm text-text-2 leading-relaxed">A PM reviews your trade, license, and insurance. We may request COIs and references.</p>
              </div>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-9 h-9 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0 text-accent-400 font-bold text-sm">3</div>
              <div>
                <h3 class="font-semibold text-text mb-1">Onboard &amp; bid</h3>
                <p class="text-sm text-text-2 leading-relaxed">Approved subs are added to our bid list. You'll get invitations on projects matching your trade and area.</p>
              </div>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="card p-6">
            <div class="flex items-start gap-4">
              <div class="w-9 h-9 rounded-lg bg-accent-400/10 border border-accent-400/20 flex items-center justify-center shrink-0 text-accent-400 font-bold text-sm">4</div>
              <div>
                <h3 class="font-semibold text-text mb-1">Build together</h3>
                <p class="text-sm text-text-2 leading-relaxed">Win the bid, sign the contract, hit the site. Net-30 pay from approved pay app.</p>
              </div>
            </div>
          </div>

          <!-- Trust strip -->
          <div class="card p-6 bg-accent-400/[0.04] border-accent-400/20">
            <p class="text-xs text-accent-400 uppercase tracking-widest font-bold mb-2">Already a partner?</p>
            <p class="text-sm text-text-2 leading-relaxed">If you've worked with us before, just mention the project name in the "Anything Else" field — we'll fast-track your application.</p>
          </div>

        </div>

      </div>
    </div>
  </section>

<?php require __DIR__ . '/includes/footer.php'; ?>

<script>
function subcontractorForm() {
  return {
    form: {
      first_name: '', last_name: '', email: '', phone: '',
      company_name: '', website: '', years_in_business: '', company_size: '',
      trade: '', trades_other: '', states_licensed: '', service_area: '',
      license_number: '', insured: '', bonded: '', emr_rating: '', union_status: '',
      project_types_interest: '', largest_project: '', references_text: '', message: '',
      website_url: '', // honeypot
    },
    errors:      {},
    loading:     false,
    submitted:   false,
    submitError: '',

    validate() {
      this.errors = {};
      if (!this.form.first_name.trim())   this.errors.first_name   = 'First name is required.';
      if (!this.form.last_name.trim())    this.errors.last_name    = 'Last name is required.';
      if (!this.form.email.trim())        this.errors.email        = 'Email is required.';
      else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.form.email)) this.errors.email = 'Please enter a valid email.';
      if (!this.form.phone.trim())        this.errors.phone        = 'Phone is required.';
      if (!this.form.company_name.trim()) this.errors.company_name = 'Company name is required.';
      if (!this.form.trade)               this.errors.trade        = 'Please select your trade.';
      return Object.keys(this.errors).length === 0;
    },

    async submitForm() {
      this.submitError = '';
      if (!this.validate()) return;

      this.loading = true;
      try {
        const payload = new FormData();
        Object.entries(this.form).forEach(([k, v]) => payload.append(k, v));
        payload.append('source', 'website-subcontractor');
        payload.append('submitted_at', new Date().toISOString());

        const res = await fetch('/api/subcontractor.php', {
          method:   'POST',
          body:     payload,
          redirect: 'follow',
        });

        const data = await res.json();
        if (data.success) {
          this.submitted = true;
          window.scrollTo({ top: document.getElementById('apply').offsetTop - 100, behavior: 'smooth' });
        } else {
          this.submitError = data.error || 'Something went wrong. Please try again or call us at <?= SITE_PHONE ?>.';
        }
      } catch (err) {
        this.submitError = 'Unable to send your application. Please call <?= SITE_PHONE ?> or email <?= SITE_EMAIL ?>.';
      } finally {
        this.loading = false;
      }
    },
  };
}
</script>
