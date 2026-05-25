# eParamus Marketing Site — Build Plan

## Overview

Build the eParamus marketing site as a **WordPress block theme** with Gutenberg block patterns, seeded via WP-CLI.

- **Visual reference:** [SaasFlow Webflow template](https://saasflow-webflow-html-website-template.webflow.io/) — adapt its design system and layout patterns
- **Content reference:** `spec/` files and `spec/original-docs/output.md` — all copy comes from here
- **Mockups:** `mockup/` — content/structure reference only, not visual
- **Prototype:** `saasflow/index.html` + `saasflow/styles.css` — working plain HTML prototype of the homepage; use as the visual and structural reference when building the theme

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
Display:  800, 72px, 105% line-height, -1.6px letter-spacing
H1:       700, 60px, 106% line-height, -1.2px letter-spacing
H2:       700, 48px, 108% line-height, -0.8px letter-spacing
H3:       700, 36px, 112% line-height, -0.4px letter-spacing
H4:       700, 24px, 125% line-height
H5:       600, 18px, 155% line-height
Body:     400, 18px, 155% line-height
Font:     Plus Jakarta Sans (Google Fonts)
```

### Section types
- **Hero** — full-width, display headline, subtext, two CTAs, microcopy, dot-grid background
- **4-card grid** — icon + heading + body + optional link
- **Contrast block** — two-column side-by-side (muted left / active right)
- **4-step process** — numbered steps with connecting line, dark background variant
- **2-column section** — text + stacked cards, or text + text
- **CTA section** — colored background (blue or dark), headline + buttons + optional 3-step panel
- **Footer** — multi-column links + copyright

---

## WordPress Project Structure

```
theme/                        — WordPress block theme (install in wp-content/themes/)
  style.css                   — theme header + custom component CSS
  theme.json                  — design tokens: colors, typography, spacing
  functions.php               — enqueue styles, register pattern categories
  index.php                   — fallback template
  templates/
    front-page.php            — homepage template
    page.php                  — default page template
  parts/
    header.html               — nav block template part
    footer.html               — footer block template part
  patterns/                   — reusable block patterns (available in editor)
    hero.html
    section-4-cards.html
    section-contrast-block.html
    section-steps.html
    section-2col.html
    section-cta.html

content/                      — full page block markup (source of truth for seeding)
  home.html
  how-it-works.html
  platform-overview.html
  platform-mid-generator.html
  platform-workforce-capability-insight.html
  solutions-learning-leaders.html
  solutions-instructional-design-teams.html
  solutions-business-leaders.html
  resources-articles.html
  resources-case-studies.html
  resources-videos.html
  resources-guides.html
  about.html

bin/
  seed.sh                     — WP-CLI script: creates all pages, sets front page, sets menus
```

### How patterns and content relate

`patterns/` files are reusable templates editors can insert in Gutenberg.
`content/` files are the full page sequences — each is the assembled block markup for one page, seeded into the database via WP-CLI. Editors own the content after seeding; re-running `seed.sh` with `wp post update` resets it to the source file.

---

## Steps

### Step 1 — `theme/theme.json`
Define all design tokens as WordPress block theme settings:
- Color palette (all swatches named and slugged)
- Typography: font family (Plus Jakarta Sans via Google Fonts), font sizes
- Spacing scale
- Border radius presets
- Disable unwanted core block features (padding controls, color pickers outside palette, etc.)

### Step 2 — `theme/style.css` + custom component CSS
Theme header declaration + CSS for components that core blocks can't express cleanly:
- Contrast block layout and muted/active item styles
- Step connector line overlay
- Nav dropdown behavior
- Dot-grid hero background
- Scroll entrance animations (`.fade-up`)
- Button variants beyond core block defaults

Reference `saasflow/styles.css` for all values — this is a direct port.

### Step 3 — `theme/parts/header.html`
Nav as a block template part:
- Site title block (logo)
- Navigation block with dropdowns: Platform, How it works, Solutions, Resources, About
- Button block: "Start with one program" (primary style)
- Sticky header via `theme.json` or CSS

### Step 4 — `theme/parts/footer.html`
Footer as a block template part:
- Logo + tagline column
- Three nav column groups (Platform, Solutions, Company)
- Copyright paragraph
- Dark background via Group block background color

### Step 5 — `theme/patterns/` — block pattern library
One pattern file per reusable section type. Each is a self-contained block sequence editors can insert from the pattern library:

| Pattern file | Section type | Used on |
|---|---|---|
| `hero.html` | Hero section | All page heroes |
| `section-4-cards.html` | 4-column card grid | Platform, Solutions |
| `section-contrast-block.html` | Traditional vs IMPACT split | Homepage, Platform overview |
| `section-steps.html` | Numbered 4-step flow | Homepage, How it works |
| `section-2col.html` | 2-column text/cards | Implementation, Shift |
| `section-cta.html` | Full-width CTA band | All pages (final section) |

### Step 6 — `content/home.html`
Full homepage block sequence assembled from patterns, per `spec/original-docs/output.md`:
1. Hero — "Define. Build. Verify Workforce Capability."
2. The Problem — 4 tension cards + contrast block
3. The Shift — 2-column copy
4. How It Works — 4-step dark section
5. The IMPACT Platform — 4 capability cards
6. Support / Implementation — 2-column + stacked cards
7. Start Small — blue CTA section with 3-step panel

### Step 7 — `content/how-it-works.html`
1. Hero
2. Why Measurable Design Changes Everything (intro + visual flow)
3. 4-step process (Define / Build / Verify / Act)

### Step 8 — `content/platform-overview.html`
6 sections per `output.md`.

### Step 9 — `content/platform-mid-generator.html`
7 sections per `output.md`.

### Step 10 — `content/platform-workforce-capability-insight.html`
7 sections per `output.md`.

### Step 11 — `content/solutions-*.html` (3 files)
Each follows the same 7-section structure. "What eParamus gives you" content from mockup goes after benefit blocks, before implementation/support.

### Step 12 — `content/resources-*.html` (4 files)
Placeholder content. Article cards, case study cards, video grid, guide cards.

### Step 13 — `content/about.html`
Mission and values. No team section.

### Step 14 — `bin/seed.sh`
WP-CLI script that:
1. Creates a page for each `content/*.html` file (or updates if already exists)
2. Sets the homepage as the static front page
3. Creates the nav menu and assigns all items with correct parent/child hierarchy
4. Activates the theme

```bash
# Example structure
wp post create \
  --post_type=page \
  --post_title="Home" \
  --post_name="home" \
  --post_status=publish \
  --post_content="$(cat content/home.html)"

HOME_ID=$(wp post list --post_type=page --name=home --field=ID --format=ids)
wp option update show_on_front page
wp option update page_on_front "$HOME_ID"
```

### Step 15 — Local dev setup + QA
- Local WordPress via LocalWP (or DDEV/Lando)
- Install theme, run `seed.sh`
- QA every page in Gutenberg editor (confirm blocks are editable) and frontend
- Check responsive at 375px, 768px, 1280px

---

## Open Questions

- [ ] **CTA destinations** — "Start with One Program" and "Schedule a Conversation": contact form, Calendly, email, or `#` placeholder for now?
- [ ] **Footer content** — privacy policy link? Any legal copy beyond copyright?
- [ ] **Resources content** — placeholder as-is, or real content to drop in?
- [ ] **About copy** — finalized mission/values, or use what's in the mockup?
- [ ] **Logo/favicon** — text wordmark only, or is there a logo asset?
- [ ] **Hosting** — where does the WordPress instance live? (affects whether we need a deploy step beyond local dev)
