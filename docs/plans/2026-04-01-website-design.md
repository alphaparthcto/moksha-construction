# Moksha Construction — Complete Website Build Plan

> Created 2026-04-01 | Phase 1: Public-Facing Site
> Stack: PHP 8.2 + Tailwind CSS v4 + Alpine.js + Vite
> Hosting: InMotion VPS (same as Matchbox)

---

## TABLE OF CONTENTS

1. [Design System — "Royal Forge"](#1-design-system--royal-forge)
2. [Site Architecture](#2-site-architecture)
3. [Page 1: Homepage — Complete Copy](#3-page-1-homepage)
4. [Page 2: General Contracting — Complete Copy](#4-page-2-general-contracting)
5. [Page 3: Construction Management — Complete Copy](#5-page-3-construction-management)
6. [Page 4: Design & Build — Complete Copy](#6-page-4-design--build)
7. [Page 5: Residential | Commercial | Industrial — Complete Copy](#7-page-5-residential-commercial-industrial)
8. [Page 6: Projects — Complete Copy](#8-page-6-projects)
9. [Page 7: About — Complete Copy](#9-page-7-about)
10. [Page 8: Contact — Complete Copy](#10-page-8-contact)
11. [SEO & Schema Markup](#11-seo--schema-markup)
12. [AEO Strategy](#12-aeo-strategy)
13. [Technical Architecture](#13-technical-architecture)
14. [Phase 2: Project CMS](#14-phase-2-project-cms)
15. [Build Phases & Timeline](#15-build-phases)

---

## 1. DESIGN SYSTEM — "Royal Forge"

### Brand Colors (Extracted from Logo)

The Moksha logo uses three colors:
- **`#FFE907`** — Bright Yellow-Gold (Hue ~97 in OKLCH)
- **`#9517B3`** — Royal Purple (Hue ~310 in OKLCH)
- **`#BFBFBF`** — Silver Gray

### OKLCH Color System

```css
@theme {
  /* ============================================================
     BRAND PURPLE — Primary brand color from logo (#9517B3)
     OKLCH Hue: 310 (violet-magenta)
  ============================================================ */
  --color-brand-950: oklch(12% 0.06 310);   /* near-black purple */
  --color-brand-900: oklch(18% 0.10 310);
  --color-brand-800: oklch(28% 0.14 310);
  --color-brand-700: oklch(38% 0.18 310);
  --color-brand-600: oklch(46% 0.22 310);   /* #9517B3 approximation */
  --color-brand-500: oklch(54% 0.24 310);   /* primary interactive */
  --color-brand-400: oklch(62% 0.22 310);
  --color-brand-300: oklch(72% 0.18 310);
  --color-brand-200: oklch(82% 0.12 310);
  --color-brand-100: oklch(92% 0.06 310);
  --color-brand-50:  oklch(97% 0.02 310);

  /* ============================================================
     ACCENT GOLD — Secondary brand color from logo (#FFE907)
     OKLCH Hue: 97 (warm yellow-gold)
  ============================================================ */
  --color-accent-950: oklch(18% 0.06 97);
  --color-accent-900: oklch(28% 0.10 97);
  --color-accent-800: oklch(38% 0.16 97);
  --color-accent-700: oklch(50% 0.20 97);
  --color-accent-600: oklch(62% 0.24 97);
  --color-accent-500: oklch(78% 0.28 97);
  --color-accent-400: oklch(88% 0.24 97);   /* #FFE907 approximation */
  --color-accent-300: oklch(92% 0.18 97);
  --color-accent-200: oklch(95% 0.10 97);
  --color-accent-100: oklch(97% 0.05 97);
  --color-accent-50:  oklch(99% 0.02 97);

  /* ============================================================
     SURFACE SYSTEM — Dark mode with purple tint
  ============================================================ */
  --color-void:     oklch(6% 0.008 310);    /* deepest background */
  --color-base:     oklch(9% 0.012 310);    /* page background */
  --color-subtle:   oklch(12% 0.014 310);   /* subtle section alt */
  --color-surface:  oklch(15% 0.016 310);   /* cards */
  --color-raised:   oklch(19% 0.018 310);   /* elevated cards, modals */
  --color-overlay:  oklch(24% 0.020 310);   /* dropdowns, tooltips */

  /* ============================================================
     TEXT HIERARCHY
  ============================================================ */
  --color-text:     oklch(94% 0.006 310);   /* primary — warm white, NOT pure */
  --color-text-2:   oklch(78% 0.010 310);   /* secondary */
  --color-text-3:   oklch(58% 0.008 310);   /* tertiary / captions */
  --color-text-4:   oklch(40% 0.006 310);   /* disabled */

  /* ============================================================
     BORDERS — white-based for dark mode
  ============================================================ */
  --color-border-subtle:  oklch(100% 0 0 / 0.06);
  --color-border:         oklch(100% 0 0 / 0.10);
  --color-border-strong:  oklch(100% 0 0 / 0.18);
  --color-border-accent:  oklch(88% 0.24 97 / 0.40);  /* gold border */

  /* ============================================================
     SEMANTIC
  ============================================================ */
  --color-success:   oklch(72% 0.20 145);
  --color-warning:   oklch(82% 0.20 80);
  --color-error:     oklch(68% 0.24 25);

  /* ============================================================
     SHADOWS — glow in dark mode
  ============================================================ */
  --shadow-sm:   0 0 8px oklch(46% 0.22 310 / 0.15);
  --shadow-md:   0 0 20px oklch(46% 0.22 310 / 0.20);
  --shadow-lg:   0 0 40px oklch(46% 0.22 310 / 0.25);
  --shadow-gold: 0 0 30px oklch(88% 0.24 97 / 0.20);
}
```

### Typography

```css
@theme {
  --font-display: "Inter", system-ui, sans-serif;
  --font-body:    "Inter", system-ui, sans-serif;
  --font-accent:  "Playfair Display", Georgia, serif;

  /* Fluid type scale — clamp(min, preferred, max) */
  --text-hero:    clamp(3rem, 6vw + 1rem, 5.5rem);    /* 48-88px */
  --text-display: clamp(2.25rem, 4vw + 0.5rem, 3.5rem); /* 36-56px */
  --text-h2:      clamp(1.75rem, 3vw + 0.25rem, 2.75rem); /* 28-44px */
  --text-h3:      clamp(1.25rem, 2vw + 0.25rem, 1.75rem); /* 20-28px */
  --text-body-lg: clamp(1.125rem, 1.2vw, 1.25rem);    /* 18-20px */
  --text-body:    1rem;                                  /* 16px */
  --text-sm:      0.875rem;                              /* 14px */
  --text-xs:      0.75rem;                               /* 12px */

  --tracking-tight:  -0.02em;
  --tracking-normal: 0;
  --tracking-wide:   0.08em;
  --tracking-widest: 0.14em;
}
```

### Visual Language

- **Hero**: Full-viewport video loop with dark overlay gradient (`rgba(6,3,10,0.65)` → `transparent`)
- **Grain**: SVG noise overlay at 4% opacity for tactile depth
- **Accent usage**: Gold (`--accent-400`) on < 5% of surface — CTAs, highlights, hover states, decorative lines
- **Purple**: Used in glows, gradients, subtle background tints — never as text color on dark
- **Photography**: Full-bleed, high-contrast construction photos with dark overlays
- **Cards**: `--color-surface` background, `1px solid var(--color-border-subtle)`, inner highlight `inset 0 1px 0 rgba(255,255,255,0.06)`
- **Hover effects**: Gold border glow (`box-shadow: var(--shadow-gold)`), slight lift (`translateY(-4px)`)
- **Scroll animations**: Fade-up on enter viewport, staggered card reveals — CSS scroll-driven where possible, GSAP ScrollTrigger for complex sequences
- **Reduced motion**: All animations disabled with `prefers-reduced-motion: reduce`

### Button Styles

| Type | Style | Usage |
|------|-------|-------|
| **Primary** | Gold bg (`--accent-400`), dark text, hover: brighten + lift | Main CTAs ("Get a Free Quote") |
| **Secondary** | Transparent, gold border, gold text, hover: gold bg fill | Secondary actions ("View Projects") |
| **Ghost** | Transparent, white text, subtle underline, hover: gold text | Tertiary links ("Learn More →") |

---

## 2. SITE ARCHITECTURE

### Navigation

```
┌──────────────────────────────────────────────────────────────────────┐
│ [Logo]              Home  Services▾  Projects  About  Contact       │
│                                                [📞 (615) 234-0272]  │
│                                                [Get a Free Quote →] │
└──────────────────────────────────────────────────────────────────────┘

Services Dropdown:
├── General Contracting
├── Construction Management
├── Design & Build
└── Residential | Commercial | Industrial
```

- **Sticky header** with backdrop blur on scroll (`backdrop-filter: blur(16px) saturate(180%)`)
- **Shrinks** from 80px to 60px height on scroll
- **Phone number**: Click-to-call, always visible on desktop; in mobile menu
- **Gold CTA button**: Always visible, leads to `/contact#quote`
- **Mobile**: Full-screen overlay menu with staggered fade-in, purple-to-black gradient background
- **Social links in header**: No — keep header clean. Socials in footer only.

### URL Structure

| Page | URL | Notes |
|------|-----|-------|
| Homepage | `/` | |
| General Contracting | `/services/general-contracting` | |
| Construction Management | `/services/construction-management` | |
| Design & Build | `/services/design-build` | |
| Res/Com/Ind | `/services/residential-commercial-industrial` | |
| Projects | `/projects` | Static now, CMS later |
| Project Detail | `/projects/{slug}` | Phase 2 CMS |
| About | `/about` | |
| Contact | `/contact` | Includes quote form |

### Social Links (Footer + Schema)

- **Instagram**: `https://instagram.com/mokshaconstruction` *(get exact URL from client)*
- **Facebook**: `https://facebook.com/mokshaconstruction` *(get exact URL from client)*
- **LinkedIn**: `https://linkedin.com/company/moksha-construction` *(get exact URL from client)*

### Footer Structure

```
┌──────────────────────────────────────────────────────────────────────┐
│                                                                      │
│  [Logo - White]                                                      │
│                                                                      │
│  "Building legacies across the Southeast"                            │
│                                                                      │
│  ─── Navigation ───    ─── Services ───         ─── Contact ───     │
│  Home                  General Contracting       📞 (615) 234-0272  │
│  About                 Construction Mgmt         ✉ info@moksha...   │
│  Projects              Design & Build            Nashville Office    │
│  Contact               Res | Com | Ind           Atlanta Office      │
│                                                                      │
│  ─── Follow Us ───                                                   │
│  [IG] [FB] [LinkedIn]                                                │
│                                                                      │
│  Licensed in TN · TX · NC | Expanding to GA · SC · FL               │
│  © 2026 Moksha Construction · Website by APETech                     │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

---

## 3. PAGE 1: HOMEPAGE

### Meta Tags

```html
<title>Moksha Construction | General Contractor in Clarksville & Nashville, TN</title>
<meta name="description" content="Moksha Construction is a licensed general contractor in Clarksville, TN serving Nashville, Atlanta, and the Southeast. Residential, commercial, industrial & religious construction. Get a free quote today.">
<link rel="canonical" href="https://moksha.construction/">

<!-- Open Graph -->
<meta property="og:title" content="Moksha Construction — We Build Legacies">
<meta property="og:description" content="Licensed general contractor in Clarksville & Nashville, TN. Residential, commercial, industrial & religious construction across the Southeast.">
<meta property="og:image" content="https://moksha.construction/assets/images/og-home.jpg">
<meta property="og:url" content="https://moksha.construction/">
<meta property="og:type" content="website">
<meta property="og:site_name" content="Moksha Construction">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Moksha Construction — We Build Legacies">
<meta name="twitter:description" content="Licensed general contractor in Clarksville & Nashville, TN. Get a free quote.">
<meta name="twitter:image" content="https://moksha.construction/assets/images/og-home.jpg">
```

---

### Section 1: Hero (Full Viewport — Video Background)

**Video**: Cinematic construction b-roll loop (aerial crane shots, welding sparks, concrete pour, steel beams rising). We'll source a royalty-free video from Pexels or the client provides their own footage. Muted, autoplay, loop. Poster image fallback for mobile/slow connections.

**Overlay**: Dark gradient from bottom (`rgba(6,3,10,0.7)`) fading to transparent at top, with subtle purple radial glow in bottom-left corner.

**Copy**:

> **[Eyebrow — uppercase, gold, tracked]**
> GENERAL CONTRACTOR · CLARKSVILLE, TN
>
> **[H1 — Display font, 5.5rem, tight tracking]**
> We Don't Just Build Structures.
> We Build Legacies.
>
> **[Subhead — body-lg, secondary text color]**
> Licensed across Tennessee, Texas, and North Carolina — with offices in Nashville and Atlanta. From ground-up commercial builds to custom residential projects, Moksha Construction delivers on time, on budget, and built to last.
>
> **[CTA Buttons]**
> `[Get a Free Quote →]` (gold, primary)
> `[View Our Work]` (ghost, secondary)

**Scroll indicator**: Subtle animated chevron at bottom of viewport, fades on scroll.

---

### Section 2: Stats Bar (Overlapping hero/next section)

Dark card that "floats" between hero and next section, offset by -60px. Gold top border line.

```
┌─────────────────┬─────────────────┬─────────────────┬─────────────────┐
│     15+         │      5          │    280K+        │    Multi-       │
│   Years of      │   States &      │   Square Feet   │   Sector       │
│   Experience    │   Growing       │   Delivered      │   Expertise    │
└─────────────────┴─────────────────┴─────────────────┴─────────────────┘
```

*(Numbers animate/count up on scroll into view)*

---

### Section 3: About Intro

**Layout**: Two columns — left 40% (gold accent bar + label), right 60% (copy)

> **[Label — uppercase, gold, tracked]**
> WHO WE ARE
>
> **[H2]**
> Precision-Built for the Southeast
>
> **[Body]**
> Moksha Construction is a full-service general contractor headquartered in Clarksville, Tennessee, with offices in Nashville and Atlanta. We specialize in general contracting, construction management, and design-build services across residential, commercial, industrial, and religious construction.
>
> Our team combines 15 years of collective experience with a global perspective — delivering projects that meet the highest standards of craftsmanship while staying on schedule and within budget. We're licensed across multiple states and growing, because our clients keep coming back.
>
> **[CTA]**
> `[About Our Company →]` (ghost link)

---

### Section 4: Services Grid

**Layout**: 2×2 grid of cards. Each card has a full-bleed background photo with dark overlay, service icon, name, one-liner, and arrow link.

> **[Section Label — uppercase, gold]**
> WHAT WE DO
>
> **[H2]**
> Services Built Around Your Vision

**Card 1: General Contracting**
- Image: `drone-bim.webp`
- Copy: "Complete project oversight from permits to punch list. We coordinate every trade so you don't have to."
- Link: `/services/general-contracting`

**Card 2: Construction Management**
- Image: `trimble-gps.webp`
- Copy: "Data-driven project management that eliminates overruns and keeps your build on track."
- Link: `/services/construction-management`

**Card 3: Design & Build**
- Image: `design-build-hero.webp`
- Copy: "One team, one vision, one point of accountability — from concept sketch to final walkthrough."
- Link: `/services/design-build`

**Card 4: Residential · Commercial · Industrial**
- Image: `commercial.webp`
- Copy: "Custom homes, retail centers, warehouses, and everything in between. We build across sectors."
- Link: `/services/residential-commercial-industrial`

**Hover effect**: Dark overlay lightens to reveal more of the photo, gold border glow appears on edges, card lifts 4px.

---

### Section 5: Featured Projects

**Layout**: Three large project cards in a horizontal row (scrollable on mobile).

> **[Section Label — uppercase, gold]**
> OUR WORK
>
> **[H2]**
> Projects That Speak for Themselves
>
> **[Subtext]**
> From 90-suite hotels to 280,000 sq ft exhibition centers — see what Moksha Construction delivers.

**Project Card 1: Expansive Exhibition Center**
- Image: `exhibition/main.png`
- Badge: "COMMERCIAL"
- Title: "Expansive Exhibition Center"
- Detail: "280,000 sq ft · Clarksville, TN"
- Hover: Gold overlay with "View Project →"

**Project Card 2: Versatile Office Building**
- Image: `office/main.png`
- Badge: "COMMERCIAL"
- Title: "Versatile Office Building"
- Detail: "200,000 sq ft · Live Sound Studio, Theaters, Cafe"
- Hover: Gold overlay with "View Project →"

**Project Card 3: Luxurious Hotel of Distinction**
- Image: `hotel/main.png`
- Badge: "HOSPITALITY"
- Title: "Luxurious Hotel of Distinction"
- Detail: "90 Room Suites · Clarksville, TN"
- Hover: Gold overlay with "View Project →"

> **[CTA]**
> `[View All Projects →]` (ghost link, centered)

---

### Section 6: Why Moksha (Differentiators)

**Layout**: Left — large atmospheric construction photo (parallax scroll). Right — stacked differentiator items.

> **[Section Label — uppercase, gold]**
> WHY MOKSHA
>
> **[H2]**
> What Sets Us Apart

**Differentiator 1: Multi-State, Growing Fast**
> **[Gold number]** 01
> **[H3]** Licensed Across Five States
> We hold active licenses in Tennessee, Texas, and North Carolina — with Georgia, South Carolina, and Florida in progress. One contractor, no borders.

**Differentiator 2: Technology-Forward Builds**
> **[Gold number]** 02
> **[H3]** Smart Homes & Modern Construction
> Our in-house IT specialists integrate cutting-edge technology into every build. From connected home systems to BIM-driven project management, we build for the future.

**Differentiator 3: Religious & Cultural Specialists**
> **[Gold number]** 03
> **[H3]** Religious & Cultural Construction
> We're one of the only contractors in the Southeast with deep experience building temples, churches, and culturally significant structures. We understand the unique requirements, sensitivities, and craftsmanship these projects demand.

**Differentiator 4: Global Team, Local Roots**
> **[Gold number]** 04
> **[H3]** Diverse Expertise, Deep Local Knowledge
> Our team brings perspectives from across the country and the globe — backed by roots in Clarksville and Nashville. We understand this market because we live here.

---

### Section 7: Partners

**Layout**: Grayscale logo row on dark background. Logos turn full-color on hover. Subtle infinite horizontal scroll on mobile.

> **[Section Label — uppercase, gold]**
> TRUSTED PARTNERS
>
> **[H2]**
> Built With the Best

Logos: Lowe's, Sherwin-Williams, United Rentals + 3 others (pending client ID)

---

### Section 8: Team / Leadership

**Layout**: Dark card with Parth's photo on left, bio on right. Gold accent border on left edge. Signature graphic below bio.

> **[Section Label — uppercase, gold]**
> LEADERSHIP
>
> **[H2]**
> Meet the Builder Behind the Brand
>
> **[Photo]** Parth Patel
> **[Name]** Parth Patel
> **[Title]** Managing Director
>
> **[Bio]**
> Parth Patel founded Moksha Construction on a simple belief: every structure should outlast the generation that built it. From custom residential homes to large-scale commercial projects, Parth leads a team that turns blueprints into landmarks — with an unwavering commitment to quality, transparency, and client trust.
>
> Under Parth's leadership, Moksha Construction has grown from a Clarksville-based firm into a multi-state operation with offices in Nashville and Atlanta, delivering projects across Tennessee, Texas, North Carolina, and beyond.
>
> **[Signature graphic]**

---

### Section 9: Values Strip

**Layout**: Horizontal scroll of 5 value cards. Each card: icon + value name + one-liner. Dark cards with gold icon accent.

| Value | One-Liner |
|-------|-----------|
| Integrity | "Honest work. Honest pricing. Every time." |
| Transparency | "You see every dollar, every timeline, every decision." |
| Accountability | "We own our outcomes — the good and the challenges." |
| Respect | "Your vision first. Cultural sensitivity always." |
| Compliance | "Licensed, insured, and code-compliant — no shortcuts." |

---

### Section 10: Service Areas / Map

**Layout**: Left — dark-styled Mapbox map showing Nashville, Clarksville, Atlanta pins. Right — office cards stacked.

> **[Section Label — uppercase, gold]**
> WHERE WE BUILD
>
> **[H2]**
> Serving the Southeast — and Growing

**Nashville Office Card**:
> 315 Deaderick Street, Suite 1550
> Nashville, TN 37238

**Atlanta Office Card**:
> 1 W Court Square
> Decatur, GA 30030

> **[Multi-State Badge]**
> Licensed: Tennessee · Texas · North Carolina
> Expanding: Georgia · South Carolina · Florida

---

### Section 11: CTA Banner

**Layout**: Full-width section with subtle purple-to-gold diagonal gradient overlay on a construction photo background.

> **[H2 — Display, centered]**
> Ready to Build Something Extraordinary?
>
> **[Subtext]**
> Tell us about your project. We'll have a proposal in your inbox within 48 hours.
>
> **[CTA Buttons]**
> `[Get a Free Quote →]` (gold, primary)
> `[Call (615) 234-0272]` (ghost, click-to-call)

---

## 4. PAGE 2: GENERAL CONTRACTING

### Meta Tags

```html
<title>General Contracting Services | Moksha Construction | Clarksville & Nashville, TN</title>
<meta name="description" content="Moksha Construction provides full-service general contracting in Clarksville, Nashville, and across the Southeast. From scheduling and budgeting to quality control — we manage every detail of your build. Get a free quote.">
```

### Hero

- **Breadcrumb**: Home / Services / General Contracting
- **Background**: `drone-bim.webp` with dark overlay
- **H1**: "General Contracting That Delivers"
- **Subtext**: "Complete project oversight from foundation to finish. Licensed in Tennessee, Texas, and North Carolina."

### Section: Intro (2-column)

> **Left (60%)**
>
> As your general contractor, Moksha Construction takes full responsibility for your build — from the first permit application to the final punch list walkthrough. We coordinate architects, engineers, and subcontractors into a single, efficient operation so your project stays on schedule and within budget.
>
> Based in Clarksville, Tennessee, with a second office in Nashville, we serve clients across the Southeast with residential, commercial, industrial, and religious construction projects ranging from 10,000 to 280,000+ square feet.
>
> **Right (40%) — Quick Facts Card**
> - Licensed in 3 states (TN, TX, NC)
> - 15+ years collective experience
> - Projects up to 280,000 sq ft
> - Offices in Nashville & Atlanta
> - Free detailed estimates

### Section: What We Handle

3 feature blocks with gold-accented icons:

**1. Project Coordination & Scheduling**
> We build the master schedule and manage every phase — excavation, framing, MEP, finishes. Our project managers maintain daily coordination with every trade on site, flagging conflicts before they become delays.

**2. Budget Management & Cost Control**
> Transparent budgets with line-item breakdowns. We track costs against estimates in real time, giving you full visibility into where your money goes — and catching variances before they compound.

**3. Quality Assurance & Inspections**
> Every material and installation meets or exceeds code requirements. We conduct internal quality audits at every milestone and coordinate all municipal inspections to keep your Certificate of Occupancy on track.

### Section: Our Process (4 Steps)

```
01 DISCOVER       02 PLAN          03 BUILD         04 DELIVER
We learn your     Detailed scope,  Active site      Final
vision, site,     schedule, and    management       inspections,
and budget.       trade plan.      with weekly       punch list,
                                   reporting.        handover.
```

### Section: Smart Homes & Technology

> **[H3]** Building Smart Homes for the Connected Generation
>
> We don't just build structures — we build intelligent spaces. Our in-house IT specialists work alongside our construction team to integrate home automation, structured wiring, smart HVAC controls, and security systems into every residential project. Whether you're building a tech-forward family home or a connected commercial space, Moksha delivers the infrastructure for tomorrow.

### Section: Related Projects

Show 2 project cards: Exhibition Center + Office Building

### Section: FAQ (FAQPage Schema)

**Q: What does a general contractor do?**
A: A general contractor manages all aspects of a construction project, including hiring subcontractors, scheduling work phases, managing the budget, obtaining permits, and ensuring quality standards are met. At Moksha Construction, we serve as the single point of accountability for your entire build.

**Q: How much does it cost to hire a general contractor in Tennessee?**
A: General contractor fees in Tennessee typically range from 10% to 20% of total project cost, depending on project complexity, size, and scope. Moksha Construction provides free, detailed estimates with line-item cost breakdowns. Call us at (615) 234-0272 for a no-obligation quote.

**Q: Do you work on both residential and commercial projects?**
A: Yes. Moksha Construction handles residential projects (custom homes, renovations, apartments), commercial builds (offices, retail, hotels, exhibition centers), industrial facilities (warehouses, manufacturing), and religious structures (temples, churches). We're licensed in Tennessee, Texas, and North Carolina.

**Q: How long does a typical construction project take?**
A: Timelines vary by project size and complexity. A residential home typically takes 6-12 months, while a large commercial build like our 280,000 sq ft exhibition center may take 18-24 months. We provide a detailed schedule during the planning phase and keep you updated with weekly progress reports.

**Q: Are you licensed and insured?**
A: Yes. Moksha Construction is fully licensed, bonded, and insured. We hold active contractor licenses in Tennessee, Texas, and North Carolina, with expansion into Georgia, South Carolina, and Florida in progress.

### Section: CTA

> **[H2]** Start Your Project With a Free Estimate
> Tell us about your build — we respond within 48 hours.
> `[Get a Free Quote →]` (gold)
> `[Call (615) 234-0272]` (ghost)

---

## 5. PAGE 3: CONSTRUCTION MANAGEMENT

### Meta Tags

```html
<title>Construction Management Services | Moksha Construction | Nashville & Clarksville, TN</title>
<meta name="description" content="Expert construction management in Nashville and Clarksville, TN. Moksha Construction delivers projects on time and on budget with data-driven scheduling, cost control, and transparent reporting. Free consultation.">
```

### Hero

- **H1**: "Construction Management That Eliminates the Guesswork"
- **Subtext**: "Data-driven project management. Transparent reporting. Zero surprises."

### Intro

> Effective construction management is the difference between a project that delivers on its promises and one that spirals into delays and overruns. At Moksha Construction, our construction management team takes a proactive approach — identifying problems before they surface, optimizing schedules for efficiency, and maintaining transparent communication at every milestone.
>
> We serve as your owner's representative on the job site, ensuring that every subcontractor, material delivery, and inspection aligns with your project goals. Our clients across Tennessee, Texas, and North Carolina trust us because we treat their budgets like our own.

### What We Manage

**1. Pre-Construction Planning**
> Before a shovel hits dirt, we build a comprehensive project plan — scope definition, trade procurement, value engineering, and risk assessment. Our pre-construction process identifies cost savings and scheduling efficiencies that compound throughout the build.

**2. Schedule Optimization**
> We use modern scheduling tools to map critical path activities, manage float time, and coordinate trade sequences. Our project managers monitor progress against the baseline daily, adjusting in real time to prevent cascading delays.

**3. Cost Tracking & Reporting**
> Monthly cost reports with variance analysis. Change order tracking with impact projections. Budget-to-actual comparisons at every milestone. You always know exactly where your project stands financially.

**4. Quality & Safety Compliance**
> Our safety-first approach includes daily job site inspections, OSHA compliance monitoring, subcontractor safety orientations, and incident-free project goals. Quality audits happen at every phase gate — not just at the end.

### Process (4 Steps)

```
01 ASSESS          02 STRATEGIZE     03 EXECUTE        04 CLOSE OUT
Site analysis,     Master schedule,  Daily oversight,   Final
feasibility,       budget lock,      weekly reports,    inspections,
risk review.       trade awards.     issue resolution.  documentation.
```

### FAQ (FAQPage Schema)

**Q: What's the difference between a general contractor and a construction manager?**
A: A general contractor performs the construction work directly, while a construction manager acts as the owner's representative — overseeing the project on your behalf, managing contractors, and ensuring quality, schedule, and budget compliance. Moksha Construction offers both services.

**Q: How does construction management save money?**
A: Professional construction management typically saves 5-15% on total project costs through value engineering, competitive bid management, schedule optimization, and early identification of potential issues that could cause costly changes later.

**Q: What size projects benefit from construction management?**
A: Any project over $500,000 typically benefits from dedicated construction management. For projects above $1 million, the cost savings from professional oversight almost always exceed the management fee.

### CTA

> **[H2]** Put Your Project in Expert Hands
> `[Get a Free Quote →]` · `[Call (615) 234-0272]`

---

## 6. PAGE 4: DESIGN & BUILD

### Meta Tags

```html
<title>Design & Build Services | Moksha Construction | Clarksville & Nashville, TN</title>
<meta name="description" content="Design-build construction services in Clarksville and Nashville, TN. Moksha Construction delivers seamless design-to-construction execution with one team, one budget, one timeline. Get a free consultation.">
```

### Hero

- **H1**: "Design & Build. One Team. One Vision."
- **Subtext**: "From concept sketch to certificate of occupancy — seamless execution under one roof."

### Intro

> Design-build eliminates the friction between architects and builders by uniting them under one contract and one accountability structure. At Moksha Construction, our architects, designers, and builders collaborate from day one — ensuring that what gets designed can actually get built, on time and within your budget.
>
> This integrated approach reduces project timelines by 20-30% compared to traditional design-bid-build delivery, because design and construction phases overlap instead of running sequentially. Fewer change orders. Fewer surprises. Better results.

### Benefits (3 Cards)

**1. Single Point of Accountability**
> One contract. One team. One phone call when you have a question. Design-build eliminates the finger-pointing between designers and contractors that plagues traditional delivery methods.

**2. Faster Delivery**
> Overlapping design and construction phases means your project breaks ground sooner and completes faster. Our design-build projects typically deliver 20-30% ahead of traditional timelines.

**3. Budget Certainty**
> Real-time cost feedback during design prevents the sticker shock of bidding a completed design. We value-engineer as we design — not after the blueprints are done.

### Smart & Cultural Spaces

> **[H3]** Spaces That Reflect Who You Are
>
> Our design team doesn't just follow trends — we listen to your vision and translate it into spaces that function beautifully and reflect your identity. Whether it's a tech-forward smart home with integrated automation, a culturally significant religious structure, or a contemporary commercial environment, Moksha Construction designs with intention and builds with precision.

### FAQ

**Q: What is design-build construction?**
A: Design-build is a project delivery method where one company handles both the design and construction of a building. This streamlines communication, reduces costs, and accelerates timelines by eliminating the gap between design and construction teams. Moksha Construction offers design-build services for residential, commercial, and industrial projects.

**Q: Is design-build more expensive than traditional construction?**
A: Design-build is typically 5-10% less expensive than traditional design-bid-build because it eliminates redundant processes, reduces change orders, and allows for continuous value engineering. You also benefit from a guaranteed maximum price earlier in the process.

**Q: Can I bring my own architect to a design-build project?**
A: Yes. We work with client-selected architects as well as our in-house design team. In either case, we integrate the design process with construction planning from day one to maintain the benefits of design-build delivery.

### CTA

> **[H2]** Let's Design Your Next Build Together
> `[Get a Free Quote →]` · `[Call (615) 234-0272]`

---

## 7. PAGE 5: RESIDENTIAL | COMMERCIAL | INDUSTRIAL

### Meta Tags

```html
<title>Residential, Commercial & Industrial Construction | Moksha Construction | TN, TX, NC</title>
<meta name="description" content="Moksha Construction builds custom homes, commercial spaces, and industrial facilities across Tennessee, Texas, and North Carolina. Specializing in smart homes, religious buildings, and multi-sector construction. Free estimates.">
```

### Hero

- **H1**: "Every Sector. Every Scale. One Standard of Excellence."
- **Subtext**: "Custom homes, commercial complexes, industrial facilities, and religious structures — built with the same commitment to quality."

### Three Sector Sections (Full-width alternating layout)

**RESIDENTIAL** (Image left, copy right)
> **[Badge]** RESIDENTIAL
> **[H2]** Your Dream Home, Engineered to Last
>
> From custom-built family homes to apartment complexes, Moksha Construction brings residential visions to life. We specialize in smart home construction — integrating automation systems, structured wiring, energy-efficient HVAC, and modern finishes that today's homeowners expect.
>
> Whether you're building your first home in Clarksville, renovating a Nashville property, or developing a multi-unit residential complex, our team handles every phase from permitting to final walkthrough.
>
> **Featured**: Lotus Villa Apartments — 64-unit ground-up residential complex with contemporary design, fitness center, and community spaces.

**COMMERCIAL** (Image right, copy left)
> **[Badge]** COMMERCIAL
> **[H2]** Spaces That Drive Business Forward
>
> Moksha Construction understands that commercial spaces need to work as hard as the businesses inside them. We design and build offices, retail centers, hotels, restaurants, exhibition venues, and mixed-use developments — tailored to your operational needs and brand identity.
>
> Our commercial portfolio includes a 280,000 sq ft exhibition center, a 200,000 sq ft office building with integrated live sound studios and theaters, and a 90-suite luxury hotel — all delivered on schedule.
>
> **Featured**: Expansive Exhibition Center — 280,000 sq ft multi-purpose event hub.

**INDUSTRIAL** (Image left, copy right)
> **[Badge]** INDUSTRIAL
> **[H2]** Built for Operational Efficiency
>
> Industrial construction demands specialized expertise — heavy-load foundations, clear-span structures, utility-intensive mechanical systems, and strict compliance with operational safety standards. Moksha Construction delivers industrial facilities engineered for long-term durability and peak performance.
>
> From warehouses and distribution centers to manufacturing plants and specialized production facilities, we build industrial spaces that support your operations for decades.

### Religious Construction (Unique Differentiator Section)

Full-width dark section with gold accent border.

> **[Badge — Gold]** UNIQUE SPECIALIZATION
> **[H2]** Religious & Cultural Construction
>
> Moksha Construction is one of the few general contractors in the Southeast with deep experience in religious and cultural construction. We've built temples, churches, and community worship spaces — each requiring unique architectural sensitivities, specialized craftsmanship, and a genuine respect for the spiritual significance of the structure.
>
> We understand that a temple is not just a building. It's a sacred space for generations. Our team works closely with religious leaders, cultural consultants, and specialized artisans to deliver structures that honor tradition while meeting modern building codes and accessibility requirements.
>
> If you're planning a religious or cultural construction project, we'd welcome the conversation.

### FAQ

**Q: Does Moksha Construction build custom homes?**
A: Yes. We build custom residential homes from the ground up, including smart homes with integrated technology systems. We also handle renovations, additions, and multi-unit residential developments like our 64-unit Lotus Villa apartment complex.

**Q: What types of commercial buildings do you construct?**
A: We build offices, retail centers, hotels, exhibition halls, restaurants, mixed-use developments, and religious structures. Our commercial portfolio ranges from 10,000 sq ft retail centers to 280,000 sq ft exhibition facilities.

**Q: Do you build religious structures like temples and churches?**
A: Yes — this is one of our specializations. Moksha Construction has specific experience in religious and cultural construction, including temples and churches. We understand the unique architectural, cultural, and regulatory requirements these projects demand.

### CTA

> **[H2]** Whatever You're Building, We're Ready
> `[Get a Free Quote →]` · `[Call (615) 234-0272]`

---

## 8. PAGE 6: PROJECTS

### Meta Tags

```html
<title>Our Projects | Moksha Construction Portfolio | Clarksville & Nashville, TN</title>
<meta name="description" content="View Moksha Construction's project portfolio — from 280,000 sq ft exhibition centers to luxury hotels and apartment complexes. See our work across Tennessee and the Southeast.">
```

### Hero

- **H1**: "Our Work Speaks for Itself"
- **Subtext**: "Hotels, exhibition centers, office buildings, retail spaces, and residential complexes — built by Moksha Construction."

### Filter Bar

```
[All]  [Commercial]  [Residential]  [Hospitality]  [Industrial]
```

CSS-only filter for Phase 1. JavaScript filter with URL params for Phase 2.

### Project Grid (2-column masonry)

Each project card:
- Full-bleed image
- Type badge (top-left, gold background)
- Project name (bottom, white on dark overlay)
- Size + location (secondary text)
- Hover: image zoom, gold overlay, "View Project →"

**Project 1: Expansive Exhibition Center**
- Type: COMMERCIAL
- Size: 280,000 sq ft
- Location: Clarksville, TN
- Description: "A sprawling exhibition center serving as a multi-purpose hub for trade shows, conventions, cultural gatherings, and large-scale events. State-of-the-art amenities with flexible layouts designed for maximum versatility."

**Project 2: Versatile Office Building**
- Type: COMMERCIAL
- Size: 200,000 sq ft
- Location: Tennessee
- Description: "A landmark office building featuring an integrated live sound studio, multiple presentation theaters, and a two-story cafe — redefining the modern workplace with seamless multi-use functionality."

**Project 3: Luxurious Hotel of Distinction**
- Type: HOSPITALITY
- Size: 90 Room Suites
- Location: Clarksville, TN
- Description: "An upscale hospitality venue featuring 90 luxurious room suites with world-class amenities and personalized services. Every detail crafted to deliver the ultimate guest experience."

**Project 4: Lotus Villa Apartments**
- Type: RESIDENTIAL
- Size: 64 Units
- Location: Tennessee
- Description: "A ground-up apartment complex featuring contemporary architectural design, spacious units, a fitness center, communal gathering spaces, and landscaped green areas. Built for modern community living."

**Project 5: Commercial Retail Center**
- Type: COMMERCIAL
- Size: 10,000 sq ft
- Location: Tennessee
- Description: "A ground-up retail center optimizing every square foot for both functionality and visual appeal. Innovative design blended with practical commercial considerations."

### CMS Features (Phase 2)

- **Add Project**: Title, slug, type (dropdown), size, location, year, description (rich text), featured image, gallery (drag-drop), status (Published/Draft/Hidden)
- **Hide/Show toggle**: Each project has a Published/Hidden toggle. Hidden projects are invisible on the public site but remain in the database for future use.
- **Reorder**: Drag-and-drop ordering for the project grid
- **Gallery**: Upload multiple images per project, reorder them, set a featured image

### CTA

> **[H2]** Have a Project in Mind?
> **[Subtext]** We'd love to hear about it. Free estimates, no obligation.
> `[Get a Free Quote →]` · `[Call (615) 234-0272]`

---

## 9. PAGE 7: ABOUT

### Meta Tags

```html
<title>About Moksha Construction | Licensed General Contractor | Clarksville, TN</title>
<meta name="description" content="Learn about Moksha Construction — a licensed general contractor in Clarksville, TN with offices in Nashville and Atlanta. 15+ years of experience in residential, commercial, industrial, and religious construction across the Southeast.">
```

### Hero

- **H1**: "Built on Integrity. Growing by Reputation."
- **Subtext**: "From Clarksville to Atlanta — Moksha Construction is building the Southeast, one landmark at a time."

### Section: Our Story

> **[H2]** Who We Are
>
> Moksha Construction was founded in Clarksville, Tennessee, with a clear mission: build structures that outlast the generation that built them. What started as a local contracting firm has grown into a multi-state construction company with offices in Nashville and Atlanta, serving clients across Tennessee, Texas, North Carolina, and beyond.
>
> We're not the biggest contractor in the Southeast — and we're not trying to be. We're building a company where every project gets the full weight of our attention, our expertise, and our commitment to doing things right. Our clients don't come back because we're cheap. They come back because we deliver.

### Section: Stats Bar

```
┌────────────┬────────────┬────────────┬────────────┬────────────┐
│    15+     │    5       │    3       │   280K+    │    2       │
│   Years    │  States    │  Offices   │  Sq Ft     │ Sectors    │
│ Experience │ & Growing  │ Nashville  │ Delivered  │ Res + Com  │
│            │            │ Atlanta    │            │ + Ind      │
│            │            │ Clarksville│            │            │
└────────────┴────────────┴────────────┴────────────┴────────────┘
```

### Section: Leadership (Parth Patel)

Same layout as homepage team section but expanded:

> **[Name]** Parth Patel
> **[Title]** Managing Director
>
> Parth Patel founded Moksha Construction on the belief that construction should be personal — that every client deserves a builder who treats their project like their own.
>
> With experience spanning residential custom homes, large-scale commercial developments, and culturally significant religious structures, Parth has built a team that reflects his standards: transparent communication, meticulous craftsmanship, and a relentless focus on delivering what was promised.
>
> Under his leadership, Moksha Construction has expanded from Clarksville into Nashville, Atlanta, and across multiple states — driven not by aggressive sales, but by the referrals of satisfied clients.

### Section: Our Values (Expanded)

5 value blocks — each with icon, name, and detailed description:

**Integrity**
> We say what we mean and build what we promise. Our estimates are honest. Our timelines are realistic. When something goes wrong — and in construction, it sometimes does — we own it and fix it. No finger-pointing, no hidden costs.

**Transparency**
> Every client gets full visibility into their project. Detailed budgets with line-item breakdowns. Weekly progress reports with photos. Open access to our project management systems. You'll never wonder what's happening on your job site.

**Accountability**
> We take full responsibility for every project outcome. If we commit to a timeline, we hit it. If we commit to a budget, we track every dollar. When we make a mistake, we acknowledge it, learn from it, and make it right.

**Respect**
> Your vision comes first. We listen before we recommend. We respect diverse cultural backgrounds and incorporate specific preferences into our work — from residential homes to religious structures. We also respect the environment, prioritizing sustainable practices in every build.

**Compliance**
> Fully licensed, bonded, and insured. We comply with all applicable building codes, safety regulations, OSHA requirements, and industry standards. Safety isn't a checkbox — it's a daily priority on every Moksha job site.

### Section: Service Area Map

Dark-styled Mapbox map with:
- Active states highlighted (TN, TX, NC) in gold
- Expanding states shown (GA, SC, FL) in purple outline
- Office markers for Nashville, Clarksville, Atlanta

### Section: Partners

Same as homepage — grayscale logo row.

### CTA

> **[H2]** Ready to Work Together?
> **[Subtext]** Let's talk about your project.
> `[Get a Free Quote →]` · `[Call (615) 234-0272]`

---

## 10. PAGE 8: CONTACT

### Meta Tags

```html
<title>Contact Moksha Construction | Free Estimates | (615) 234-0272</title>
<meta name="description" content="Contact Moksha Construction for a free construction estimate. Offices in Nashville and Atlanta. Call (615) 234-0272 or submit our quote request form. We respond within 48 hours.">
```

### Hero (Short — 40vh)

- **H1**: "Let's Build Something Together"
- **Subtext**: "Free estimates. No obligation. We respond within 48 hours."

### Two-Column Layout

**Left Column (60%) — Smart Quote Form**

Combined contact + quote form. Submits to Google Sheet via Apps Script.

Fields:
- First Name * (text)
- Last Name * (text)
- Email * (email)
- Phone * (tel)
- Company Name (text, optional)
- Project Type * (dropdown: Residential, Commercial, Industrial, Religious/Cultural, Other)
- Project Location (text)
- Estimated Budget Range (dropdown: Under $100K, $100K–$500K, $500K–$1M, $1M–$5M, $5M+, Not Sure)
- Estimated Timeline (dropdown: ASAP, 1–3 Months, 3–6 Months, 6–12 Months, Just Planning)
- Tell Us About Your Project * (textarea)

Submit button: `[Submit Quote Request →]` (gold)

Success state: Green check + "Thank you! We've received your request and will respond within 48 hours."

**Right Column (40%) — Contact Info Cards**

**Call Us**
> 📞 (615) 234-0272
> Click to call — available Monday through Friday, 8am–6pm CST

**Email Us**
> ✉ info@moksha.construction
> We respond to all inquiries within one business day.

**Nashville Office**
> 315 Deaderick Street, Suite 1550
> Nashville, TN 37238
> [View on Map →]

**Atlanta Office**
> 1 W Court Square
> Decatur, GA 30030
> [View on Map →]

**Follow Us**
> [Instagram] [Facebook] [LinkedIn]

### Section: Map

Full-width dark Mapbox embed showing both office locations with gold markers.

### Response Promise

> **[Centered text below form]**
> "We respond to every inquiry within 48 hours. If your project is urgent, call us directly at (615) 234-0272."

---

## 11. SEO & SCHEMA MARKUP

### JSON-LD: GeneralContractor (Every Page)

```json
{
  "@context": "https://schema.org",
  "@type": "GeneralContractor",
  "@id": "https://moksha.construction/#organization",
  "name": "Moksha Construction",
  "url": "https://moksha.construction",
  "logo": "https://moksha.construction/assets/branding/logos/Moksha_Logo_Full Color Logo wText Horizontal - onBlack.svg",
  "image": "https://moksha.construction/assets/images/og-home.jpg",
  "description": "Moksha Construction is a licensed general contractor in Clarksville, TN serving Nashville, Atlanta, and the Southeast with residential, commercial, industrial, and religious construction services.",
  "telephone": "+1-615-234-0272",
  "email": "info@moksha.construction",
  "foundingLocation": "Clarksville, TN",
  "areaServed": [
    {"@type": "State", "name": "Tennessee"},
    {"@type": "State", "name": "Texas"},
    {"@type": "State", "name": "North Carolina"},
    {"@type": "State", "name": "Georgia"},
    {"@type": "State", "name": "South Carolina"},
    {"@type": "State", "name": "Florida"}
  ],
  "address": [
    {
      "@type": "PostalAddress",
      "streetAddress": "315 Deaderick Street, Suite 1550",
      "addressLocality": "Nashville",
      "addressRegion": "TN",
      "postalCode": "37238",
      "addressCountry": "US"
    },
    {
      "@type": "PostalAddress",
      "streetAddress": "1 W Court Square",
      "addressLocality": "Decatur",
      "addressRegion": "GA",
      "postalCode": "30030",
      "addressCountry": "US"
    }
  ],
  "sameAs": [
    "https://instagram.com/mokshaconstruction",
    "https://facebook.com/mokshaconstruction",
    "https://linkedin.com/company/moksha-construction"
  ],
  "hasOfferCatalog": {
    "@type": "OfferCatalog",
    "name": "Construction Services",
    "itemListElement": [
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "General Contracting"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Construction Management"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Design & Build"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Residential Construction"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Commercial Construction"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Industrial Construction"}},
      {"@type": "Offer", "itemOffered": {"@type": "Service", "name": "Religious & Cultural Construction"}}
    ]
  }
}
```

### BreadcrumbList (Service & Interior Pages)

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://moksha.construction/"},
    {"@type": "ListItem", "position": 2, "name": "Services", "item": "https://moksha.construction/services/"},
    {"@type": "ListItem", "position": 3, "name": "General Contracting"}
  ]
}
```

### FAQPage (Service Pages)

Each service page gets its own FAQPage schema with 4-5 questions. See individual page sections above for Q&A content.

### WebSite (Homepage Only)

```json
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "Moksha Construction",
  "url": "https://moksha.construction",
  "potentialAction": {
    "@type": "SearchAction",
    "target": "https://moksha.construction/?s={search_term_string}",
    "query-input": "required name=search_term_string"
  }
}
```

### Technical SEO Checklist

- [x] Unique `<title>` per page (see meta tags in each page section)
- [x] Unique `<meta name="description">` per page
- [x] Single `<h1>` per page
- [x] Proper heading hierarchy (H1 → H2 → H3)
- [x] All images have descriptive `alt` text
- [x] `<link rel="canonical">` on every page
- [x] OG + Twitter Card tags on every page
- [x] JSON-LD structured data on every page
- [x] `sitemap.xml` with all pages + lastmod
- [x] `robots.txt` with sitemap reference
- [x] `.htaccess`: HTTPS redirect, www canonical, GZIP, cache headers, security headers
- [x] WebP images with `<picture>` fallback
- [x] Lazy loading for below-fold images (`loading="lazy"`)
- [x] Hero image preloaded (`<link rel="preload">` + `fetchpriority="high"`)
- [x] Self-hosted fonts with `font-display: swap`
- [x] Click-to-call `tel:` links
- [x] `mailto:` links
- [x] Internal linking between related pages
- [x] Breadcrumb navigation

### robots.txt

```
User-agent: *
Allow: /

Sitemap: https://moksha.construction/sitemap.xml
```

### sitemap.xml

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>https://moksha.construction/</loc><lastmod>2026-04-01</lastmod><priority>1.0</priority></url>
  <url><loc>https://moksha.construction/services/general-contracting</loc><lastmod>2026-04-01</lastmod><priority>0.8</priority></url>
  <url><loc>https://moksha.construction/services/construction-management</loc><lastmod>2026-04-01</lastmod><priority>0.8</priority></url>
  <url><loc>https://moksha.construction/services/design-build</loc><lastmod>2026-04-01</lastmod><priority>0.8</priority></url>
  <url><loc>https://moksha.construction/services/residential-commercial-industrial</loc><lastmod>2026-04-01</lastmod><priority>0.8</priority></url>
  <url><loc>https://moksha.construction/projects</loc><lastmod>2026-04-01</lastmod><priority>0.8</priority></url>
  <url><loc>https://moksha.construction/about</loc><lastmod>2026-04-01</lastmod><priority>0.7</priority></url>
  <url><loc>https://moksha.construction/contact</loc><lastmod>2026-04-01</lastmod><priority>0.9</priority></url>
</urlset>
```

---

## 12. AEO STRATEGY (AI Engine Optimization)

### Principles Applied Across All Copy

1. **Definitive statements first**: Every page opens with a clear, factual statement AI can extract.
   - Example: "Moksha Construction is a licensed general contractor headquartered in Clarksville, Tennessee, with offices in Nashville and Atlanta."

2. **Structured Q&A**: FAQ sections on every service page use exact question-answer format that AI search engines (Perplexity, Google AI Overviews, ChatGPT) can extract and cite.

3. **Specific numbers over vague claims**: "280,000 sq ft exhibition center" not "large projects." "15+ years" not "extensive experience."

4. **Entity relationships**: Copy explicitly connects Moksha → locations → services → projects, making it easy for AI to build a knowledge graph.

5. **Passage-level citability**: Every paragraph is written to stand alone as a citable fact. No paragraph depends on the previous one for context.

6. **Natural keyword integration**: Primary keywords appear in the first sentence of every section, not stuffed throughout.

### llms.txt (Root Directory)

```
# Moksha Construction

> Licensed general contractor in Clarksville, TN. Offices in Nashville and Atlanta.

## Services
- General Contracting
- Construction Management
- Design & Build
- Residential Construction
- Commercial Construction
- Industrial Construction
- Religious & Cultural Construction

## Service Area
Licensed in Tennessee, Texas, North Carolina. Expanding to Georgia, South Carolina, Florida.

## Contact
- Phone: (615) 234-0272
- Email: info@moksha.construction
- Website: https://moksha.construction

## Offices
- Nashville: 315 Deaderick Street, Suite 1550, Nashville, TN 37238
- Atlanta: 1 W Court Square, Decatur, GA 30030
```

---

## 13. TECHNICAL ARCHITECTURE

### Stack

| Layer | Technology | Why |
|-------|-----------|-----|
| Language | PHP 8.2+ | InMotion VPS native, same as Matchbox |
| CSS | Tailwind CSS v4 | CSS-first `@theme`, utility classes, tiny bundle |
| JS | Alpine.js 3.x | Lightweight interactivity (menus, filters, counters) |
| Build | Vite 6.x | Asset bundling, CSS purging, HMR |
| Animations | GSAP ScrollTrigger (free) + CSS | Scroll reveals, counter animations |
| Smooth Scroll | Lenis | Premium smooth scroll feel |
| Maps | Mapbox GL JS (dark style) | Beautiful dark map, free tier |
| Forms | Google Apps Script → Sheet | Same as DECT, no backend in Phase 1 |
| Images | WebP + AVIF with `<picture>` | Performance |
| Icons | Lucide Icons (SVG inline) | Clean, consistent |
| Fonts | Inter (variable) + Playfair Display | Self-hosted, font-display: swap |
| Video | HTML5 `<video>` with poster | Hero background video |

### File Structure

```
moksha-construction/
├── public/                          # Apache web root
│   ├── index.php                    # Homepage
│   ├── about.php
│   ├── contact.php
│   ├── projects.php
│   ├── services/
│   │   ├── general-contracting.php
│   │   ├── construction-management.php
│   │   ├── design-build.php
│   │   └── residential-commercial-industrial.php
│   ├── assets/
│   │   ├── css/                     # Compiled Tailwind
│   │   ├── js/                      # Compiled JS
│   │   ├── images/                  # Optimized images
│   │   │   ├── branding/            # Logos
│   │   │   ├── projects/            # Project photos
│   │   │   ├── services/            # Service photos
│   │   │   ├── team/                # Team photos
│   │   │   └── partners/            # Partner logos
│   │   ├── video/                   # Hero video
│   │   └── fonts/                   # Self-hosted Inter + Playfair
│   ├── sitemap.xml
│   ├── robots.txt
│   ├── llms.txt
│   ├── favicon.ico
│   ├── apple-touch-icon.png
│   ├── site.webmanifest
│   └── .htaccess
├── includes/                        # PHP partials (NOT in web root)
│   ├── header.php                   # <head> + nav
│   ├── footer.php                   # Footer + scripts
│   ├── nav.php                      # Navigation component
│   ├── schema.php                   # JSON-LD generator
│   ├── meta.php                     # Meta tag generator
│   ├── cta-banner.php               # Reusable CTA section
│   ├── project-card.php             # Project card component
│   ├── service-card.php             # Service card component
│   ├── stats-bar.php                # Stats counter bar
│   └── config.php                   # Site-wide config (phone, email, etc.)
├── src/                             # Source files (dev only)
│   ├── css/
│   │   └── app.css                  # Tailwind @import + @theme + custom
│   └── js/
│       └── app.js                   # Alpine, GSAP, Lenis init
├── package.json
├── vite.config.js
├── docs/
│   └── plans/
├── assets/                          # Original downloaded assets
└── SITE_CONTENT.md
```

### .htaccess

```apache
# HTTPS redirect
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Remove trailing slashes
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)/$ /$1 [L,R=301]

# Clean URLs (remove .php)
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^services/(.+)$ /services/$1.php [L]
RewriteRule ^(about|contact|projects)$ /$1.php [L]

# GZIP compression
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml
</IfModule>

# Browser caching
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/avif "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
</IfModule>

# Security headers
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Referrer-Policy "strict-origin-when-cross-origin"
Header set Permissions-Policy "camera=(), microphone=(), geolocation=()"
```

### Performance Targets

| Metric | Target |
|--------|--------|
| Lighthouse Performance | 95+ |
| LCP | < 2.5s |
| INP | < 200ms |
| CLS | < 0.1 |
| Total page weight (initial) | < 1.5MB |
| CSS bundle (purged) | < 20KB |
| JS bundle | < 40KB |
| Hero video | < 5MB (compressed, 720p) |
| Image format | AVIF > WebP > JPEG |
| Font weight | < 80KB (subset Inter variable + Playfair) |

---

## 14. PHASE 2: PROJECT CMS

### Database Schema (MySQL)

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,  -- bcrypt hashed
  role ENUM('admin', 'editor') DEFAULT 'editor',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  slug VARCHAR(255) NOT NULL UNIQUE,
  type ENUM('residential', 'commercial', 'industrial', 'hospitality', 'religious') NOT NULL,
  size VARCHAR(100),              -- "280,000 sq ft" or "90 Room Suites"
  location VARCHAR(255),
  year YEAR,
  description TEXT,               -- Rich text (HTML)
  featured_image VARCHAR(500),    -- Path to main image
  status ENUM('published', 'draft', 'hidden') DEFAULT 'draft',
  sort_order INT DEFAULT 0,
  created_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE project_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  image_path VARCHAR(500) NOT NULL,
  alt_text VARCHAR(255),
  sort_order INT DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);
```

### Admin Panel Features

1. **Login**: Email + password (session-based auth)
2. **Dashboard**: List all projects with status badges (Published / Draft / Hidden)
3. **Add Project**: Form with all fields + drag-drop image upload
   - Title → auto-generates slug (editable)
   - Type dropdown
   - Size, location, year fields
   - Rich text editor for description (TinyMCE or similar)
   - Featured image upload
   - Gallery upload (multi-file, drag-drop reorder)
   - Status toggle: Published / Draft / Hidden
4. **Edit Project**: Same form, pre-filled
5. **Delete Project**: Soft confirmation dialog
6. **Image handling**: Auto-resize to max 2000px width, convert to WebP, generate thumbnail

### CMS User Experience for Parth & Hari

The admin is designed so they can:
1. Click "New Project"
2. Fill in the blanks (title, type, size, location, description)
3. Drag & drop photos
4. Click "Publish" → it's live on the site immediately
5. Click "Hide" → it disappears from public site but stays in the system
6. Click "Edit" → change anything, re-publish

No coding. No FTP. No technical knowledge needed.

---

## 15. BUILD PHASES

### Phase 1: Public-Facing Site

| Step | Task | Dependencies |
|------|------|-------------|
| 1.1 | Project scaffolding (Vite + Tailwind v4 + PHP) | None |
| 1.2 | Design system CSS (colors, typography, components) | 1.1 |
| 1.3 | PHP includes (header, footer, nav, meta, schema) | 1.2 |
| 1.4 | Homepage — all 11 sections | 1.3 |
| 1.5 | Service pages (4 pages) | 1.3 |
| 1.6 | Projects page | 1.3 |
| 1.7 | About page | 1.3 |
| 1.8 | Contact page + Google Sheet integration | 1.3 |
| 1.9 | Image optimization (WebP/AVIF conversion) | 1.4-1.8 |
| 1.10 | SEO layer (sitemap, robots, schema, .htaccess) | 1.4-1.8 |
| 1.11 | Animations (GSAP scroll reveals, counters, Lenis) | 1.4-1.8 |
| 1.12 | Mobile responsiveness pass | 1.4-1.8 |
| 1.13 | Cross-browser testing + Lighthouse audit | 1.9-1.12 |
| 1.14 | Deploy to InMotion VPS | 1.13 |

### Phase 2: Project CMS

| Step | Task |
|------|------|
| 2.1 | MySQL database setup + schema |
| 2.2 | Auth system (login, sessions) |
| 2.3 | Admin dashboard + project list |
| 2.4 | Project create/edit form with image upload |
| 2.5 | Dynamic project detail pages (`/projects/{slug}`) |
| 2.6 | Hide/Show/Draft toggle |
| 2.7 | Image processing (resize, WebP, thumbnails) |
| 2.8 | Deploy CMS to InMotion |

### Phase 3: Growth (Future)

- Blog/Insights section
- City landing pages (10-15 quality pages)
- FAQ page with schema
- Testimonials with Review schema
- Careers page
- Google Business Profile optimization

---

## OPEN ITEMS — NEED FROM CLIENT

1. **Social media URLs**: Exact Instagram, Facebook, and LinkedIn profile URLs
2. **Hero video**: Client-provided construction footage OR we source royalty-free b-roll
3. **Unknown partner logos**: Identify the 3 unknown partners in the current logo row
4. **Atlanta address**: Confirm full address (current site has incomplete address)
5. **Lotus Villa units**: Confirm 28 or 64 units
6. **Office building size**: Confirm 200,000 or 400,000 sq ft
7. **License numbers**: Get actual TN/TX/NC contractor license numbers to display
8. **Testimonials**: Any client quotes they can provide (with permission)
9. **Brand fonts**: Confirm if they have a specific brand font beyond what's in the logo, or if Inter + Playfair Display works
