# eParamus Marketing Site — Build Plan

## Overview

Build the eParamus marketing site in `saasflow/` as plain HTML/CSS/JS.

- **Visual reference:** [SaasFlow Webflow template](https://saasflow-webflow-html-website-template.webflow.io/) — adapt its design system and layout patterns to eParamus
- **Content reference:** `spec/` files and `spec/original-docs/output.md` — all copy comes from here
- **Mockups:** `mockup/` — content/structure reference only, not visual

---

## Design System (from SaasFlow template)

### Colors
```
--color-primary:   #145aff   /* blue — CTAs, links, accents */
--color-green:     #1ebd53
--color-yellow:    #ffcb3d
--color-orange:    #ff7847
--color-cyan:      #47c1fe

--color-dark-900:  #0d0e10   /* primary text */
--color-dark-800:  #0f1f3d   /* headings */
--color-dark-600:  #40454f   /* secondary text */

--color-gray-300:  #c7cad1
--color-gray-200:  #e1e4eb
--color-gray-100:  #f1f3f6
--color-white:     #ffffff
```

### Typography
```
Display:  700, 72px, 105% line-height, -1.6px letter-spacing
H1:       700, 60px, 106% line-height, -1.2px letter-spacing
H2:       700, 48px, 108% line-height, -0.8px letter-spacing
H3:       700, 36px, 112% line-height, -0.4px letter-spacing
H4:       700, 24px, 125% line-height
H5:       700, 18px, 155% line-height
H6:       700, 16px, 150% line-height,  2px letter-spacing
Body:     400, 18px, 155% line-height
Font:     -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif (no CDN)
```

### Layout patterns (from template)
- **Nav:** logo left, nav items center, CTA button right; transparent-over-white background
- **Hero:** full-width, large H1/Display headline, subtext, one or two CTA buttons
- **3-column feature card grid:** icon + headline + description per card
- **4-column grid:** tighter benefit/feature tiles
- **Alternating 2-column sections:** text block + image/visual, alternating left/right
- **Contrast block:** two-column side-by-side comparison (e.g. Traditional vs IMPACT)
- **Stats row:** large numbers with labels (for case studies / social proof)
- **CTA section:** centered, dark or colored background, headline + button(s)
- **Footer:** multi-column links + social icons + copyright

---

## File Structure

```
saasflow/
  styles.css                               — shared design system
  index.html                               — homepage
  how-it-works.html
  about.html
  platform/
    index.html                             — platform overview
    mid-generator.html
    workforce-capability-insight.html
  solutions/
    learning-leaders.html
    instructional-design-teams.html
    business-leaders.html
  resources/
    articles.html
    case-studies.html
    videos.html
    guides.html
```

**CSS path:** All pages reference `styles.css` using a root-relative path (`/styles.css`) so subdirectory pages don't need `../`.

**Nav approach:** Since there's no build step, the nav HTML block is copy-pasted into each page. When the nav changes, update all files. (Accept the tradeoff; it's 14 files.)

---

## Steps

### Step 1 — `styles.css`
Create the shared stylesheet with:
- CSS custom properties (all colors, type scale, spacing)
- Reset/base
- Nav + dropdown styles
- Hero layout
- Section/container layout (max-width, padding)
- Card component (3-col grid, 4-col grid)
- Alternating 2-column section
- Contrast block (left/right comparison)
- CTA section (centered, dark background variant)
- Button styles (primary `#145aff`, secondary/outline)
- Footer
- Responsive (mobile-first, breakpoint at 768px)

### Step 2 — Nav + Footer templates
Write the shared nav HTML (with dropdown JS) and footer HTML that will be copy-pasted into every page. Nav matches eParamus structure:
- Logo | Platform (dropdown) · How it works · Solutions (dropdown) · Resources (dropdown) · About | **Start with one program** (CTA button)

### Step 3 — Homepage (`index.html`)
Seven sections per `spec/original-docs/output.md`:
1. **Hero** — Display headline, subheadline, supporting line, two CTAs, microcopy
2. **The Problem** — 4 tension-point cards + left/right contrast visual (Traditional Metrics vs Capability Questions)
3. **The Shift** — "The Problem Isn't Measurement. It's Design" — 2-column or centered copy section
4. **How It Works** — 4-step horizontal flow (Define → Build → Verify → See)
5. **The IMPACT Platform** — 4 capability cards (MID Generator, Skill Verification, Performance Definition, Workforce Capability Insight)
6. **Support / Implementation** — 4 support element cards (Guided Onboarding, Designer Education, Stakeholder Alignment, Ongoing Partnership)
7. **Start Small** — 3-step visual + final CTA block

### Step 4 — How It Works (`how-it-works.html`)
Per `spec/how-it-works.md` and additions in `output.md`:
1. **Intro** — "Why Measurable Design Changes Everything" + copy
2. **Visual example flow** — simple vertical diagram: Skill → Learning → Verification → Insight (using safety inspection example)
3. **4-step process** — Define / Build / Verify / Act with full spec copy

### Step 5 — Platform: Overview (`platform-overview.html`)
Per `output.md`:
1. Hero
2. One Connected System. Four Core Capabilities. — 4 linked capability cards
3. Why It's Different — left/right contrast block (Traditional Systems vs IMPACT)
4. Designed for Real-World Adoption — centered copy + CTA
5. Technology Supported by Guided Implementation
6. Final CTA section

### Step 6 — Platform: MID Generator (`platform-mid-generator.html`)
Per `output.md`:
1. Hero
2. The Challenge
3. What the MID Generator Does — 4-step process flow visual
4. What Makes It Different — 2-column or alternating section
5. Key Benefits — 4-card grid
6. Organizational Impact
7. Implementation Support + Final CTA

### Step 7 — Platform: Workforce Capability Insight (`platform-workforce-capability-insight.html`)
Per `output.md`:
1. Hero
2. Most Learning Data Stops at Activity
3. From Learning Data to Workforce Insight — visual flow (Define Skills → Verify Learning → Measure Application → See Workforce Capability)
4. Insight for Every Stakeholder — 4 cards (Employees / Managers / Learning Leaders / Senior Leaders)
5. Why It Matters
6. Continuous Improvement — Closed-Loop System
7. Start Small + Final CTA

### Step 8 — Solutions: Learning Leaders (`solutions-learning-leaders.html`)
Per `output.md`. Existing mockup "What eParamus gives you" copy goes **after** the benefit blocks but **before** implementation/support.
1. Hero
2. The Challenge
3. A Different Approach
4. What Learning Leaders Can Now See — 6 benefit blocks
5. [Existing "What eParamus gives you" content from mockup]
6. Strategic Value
7. Practical Implementation + Supported Transformation + Final CTA

### Step 9 — Solutions: Instructional Design Teams (`solutions-instructional-design-teams.html`)
Same 7-section structure, ID Teams copy from `output.md`.

### Step 10 — Solutions: Business Leaders (`solutions-business-leaders.html`)
Same 7-section structure, Business Leaders copy from `output.md`.

### Step 11 — Resources (4 pages)
Placeholder content matching mockup structure. Layout follows SaasFlow blog/card style.
- `resources-articles.html` — article cards with tags and dates
- `resources-case-studies.html` — customer story cards with outcome stats
- `resources-videos.html` — video grid with thumbnail placeholders and durations
- `resources-guides.html` — downloadable guide cards with page counts

### Step 12 — About (`about.html`)
Mission and values copy. **Remove Team section** per `output.md` note.

### Step 13 — Final pass
- All nav links work across all pages
- CTA link targets consistent
- Visual QA: open each page in browser, check desktop and mobile

---

## Open Questions

- [ ] **CTA destinations** — "Start with One Program" and "Schedule a Conversation": contact form, Calendly, email, or `#` placeholder for now?
- [ ] **Footer content** — what links and legal copy? (copyright, privacy policy, nav repeat?)
- [ ] **Resources content** — use mockup placeholders as-is, or is there real content?
- [ ] **About copy** — finalized mission/values copy, or use what's in the mockup?
- [ ] **Logo/favicon** — text-only "eParamus" wordmark as in mockup, or is there a logo asset?
- [ ] **Brand color** — use `#145aff` from the SaasFlow template, or does eParamus have its own primary color?
