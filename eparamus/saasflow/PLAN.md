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

## Project Structure

```
saasflow/
  Dockerfile                    — wordpress:6.5-apache + wp-cli baked in
  docker-compose.yml            — WordPress + MariaDB, theme bind-mounted
  Makefile                      — shortcut commands (see Local Dev section)

  theme/                        — WordPress block theme (bind-mounted into container)
    style.css                   — theme header + custom component CSS (ep-* classes)
    theme.json                  — design tokens: colors, typography, spacing
    functions.php               — enqueue fonts, register pattern categories + button styles
    index.php                   — required fallback (comment only)
    templates/
      index.html                — catch-all template: header + post-content + footer
      page.html                 — same as index.html (used for all standard pages)
      front-page.html           — same structure, applied to the static front page
    parts/
      header.html               — nav block template part (sticky, dropdowns)
      footer.html               — footer block template part (dark bg, 4 columns)
    patterns/                   — reusable block patterns (available in editor)
      hero.php
      section-4-cards.php
      section-contrast-block.php
      section-steps.php
      section-2col.php
      section-cta.php

  content/                      — full page block markup (source of truth for seeding)
    home.html
    how-it-works.html
    about.html
    platform-overview.html
    platform-mid-generator.html
    platform-workforce-capability-insight.html
    solutions-overview.html
    solutions-learning-leaders.html
    solutions-instructional-design-teams.html
    solutions-business-leaders.html
    resources-overview.html
    resources-articles.html
    resources-case-studies.html
    resources-videos.html
    resources-guides.html

  bin/
    seed.sh                     — WP-CLI: upserts all pages, sets front page + permalinks
    docker-init.sh              — bootstrap script: install WP core then run seed.sh
```

### How patterns and content relate

`patterns/` files are reusable templates editors can insert in Gutenberg. Pattern files are `.php` (not `.html`) so WordPress registers them correctly as theme-bundled patterns.

`content/` files are the full page sequences — each is the assembled block markup for one page, seeded into the database via WP-CLI. Editors own the content after seeding; re-running `seed.sh` resets it to the source file.

---

## Local Dev Setup

### First time

```bash
cd saasflow/
make up      # build image + start containers (~30s on first run for image build)
make init    # wait for db, install WP core, seed all pages
make open    # http://localhost:8080
make admin   # http://localhost:8080/wp-admin  (admin / admin)
```

### Iteration loop

| What changed | Command |
|---|---|
| Theme CSS/PHP/patterns | Just reload — theme dir is bind-mounted, changes are immediate |
| Page content (content/*.html) | `make seed` — upserts all pages |
| Page slugs or parent hierarchy | `make reset` — deletes and recreates all pages |
| Preview without touching DB | `make dry` |
| Arbitrary wp-cli command | `make wp CMD="post list --post_type=page"` |
| Shell into the container | `make shell` |
| Wipe everything and start fresh | `make nuke && make up && make init` |

### How the Docker stack works

- **One container** (`wordpress`) runs Apache + PHP + wp-cli. wp-cli is baked into the image via `Dockerfile` (one `RUN curl` line on top of `wordpress:6.5-apache`).
- **`theme/`** is bind-mounted into the container at `wp-content/themes/eparamus`. Edits on the host are reflected immediately.
- **`content/` and `bin/`** are bind-mounted at `/saasflow/content` and `/saasflow/bin` so `seed.sh` and `docker-init.sh` can read them from inside the container.
- **WordPress files** live in a named volume (`wp_data`) so they persist across `make down / make up` cycles.
- **`docker-init.sh`** uses `/dev/tcp/db/3306` to wait for MariaDB — more reliable than `wp db check`, which requires wp-config.php to already exist.

---

## Build Steps

### Step 1 — `theme/theme.json`
Define all design tokens as WordPress block theme settings:
- Color palette (all swatches named and slugged)
- Typography: font family (Plus Jakarta Sans via Google Fonts), font sizes
- Spacing scale
- Disable unwanted core block features (color pickers outside palette, etc.)

### Step 2 — `theme/style.css` + custom component CSS
Theme header declaration + CSS for components that core blocks can't express:
- `ep-section` padding and background variants (`--gray`, `--dark`, `--blue`)
- `ep-contrast` split panel layout
- `ep-steps` numbered steps with connecting line
- `ep-card` hover lift, grid layouts (`ep-grid-3`, `ep-grid-4`)
- `ep-hero` dot-grid background + gradient overlay
- Button variants: `is-style-secondary`, `is-style-ghost`, `is-style-white`
- `.site-header` sticky + backdrop-filter
- `.ep-fade-up` / `.is-visible` scroll entrance animations

Reference `saasflow/styles.css` for all values — this is a direct port.

### Step 3 — `theme/parts/header.html`
Nav as a block template part:
- Site title block (logo/wordmark)
- Navigation block with dropdowns: Platform, How it works, Solutions, Resources, About
- Button block: "Start with one program" (primary style)
- Sticky header via CSS (`.site-header`)

### Step 4 — `theme/parts/footer.html`
Footer as a block template part:
- Logo + tagline column
- Three nav column groups (Platform, Solutions, Company)
- Copyright paragraph
- Dark background via Group block background color (`dark-900`)

### Step 5 — `theme/templates/` ⚠️ required for pages to render
Block themes need HTML templates or nothing renders at all. Three templates, all identical in structure:

```html
<!-- wp:template-part {"slug":"header","tagName":"header"} /-->

<!-- wp:group {"tagName":"main","style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},"layout":{"type":"default"}} -->
<main class="wp-block-group" style="padding-top:0;padding-bottom:0;">
  <!-- wp:post-content /-->
</main>
<!-- /wp:group -->

<!-- wp:template-part {"slug":"footer","tagName":"footer"} /-->
```

**Critical:** use `"layout":{"type":"default"}` on the `<main>` wrapper — not `"type":"constrained"`. The `alignfull` sections in page content need to break out to full viewport width. Constrained layout caps them at the content width.

Files: `templates/index.html`, `templates/page.html`, `templates/front-page.html`

### Step 6 — `theme/patterns/` — block pattern library
One `.php` file per reusable section type. Pattern files use a PHP docblock header for WordPress registration:
```php
<?php
/**
 * Title: Hero Section
 * Slug: eparamus/hero
 * Categories: eparamus-sections
 */
?>
<!-- block markup here -->
```

| Pattern file | Section type |
|---|---|
| `hero.php` | Hero section |
| `section-4-cards.php` | 4-column card grid |
| `section-contrast-block.php` | Traditional vs IMPACT split |
| `section-steps.php` | Numbered 4-step flow (dark bg) |
| `section-2col.php` | 2-column text layout |
| `section-cta.php` | Full-width CTA band with 3-step panel |

### Step 7 — `bin/seed.sh`
WP-CLI script that upserts all pages:
- `upsert_page TITLE SLUG CONTENT_FILE [PARENT_ID]` — updates if slug exists, creates if not
- `--reset` flag: delete + recreate (use when slugs or parent hierarchy change)
- `--dry-run` flag: prints what would happen without touching the DB
- Sets static front page, permalink structure `/%postname%/`, activates theme

### Steps 8–15 — `content/*.html` files
One file per page. Each file is pure block markup (no `<html>`, no `<body>` — just the `post_content`). Every section follows this wrapper pattern:

```html
<!-- wp:group {
  "align":"full",
  "className":"ep-section [variant]",
  "style":{"spacing":{"padding":{"top":"0","bottom":"0"}}},
  "layout":{"type":"constrained"}
} -->
<div class="wp-block-group alignfull ep-section [variant]">
  <!-- section content -->
</div>
<!-- /wp:group -->
```

Section variants: none (white), `ep-section--gray`, `ep-section--dark has-dark-800-background-color has-background`, `ep-section--blue has-primary-background-color has-background`.

Use `<!-- wp:html -->` blocks for custom-classed markup that core blocks can't produce (`.ep-steps`, `.ep-grid-3`, `.ep-grid-4`, `.ep-contrast`).

---

## Open Questions

- [ ] **CTA destinations** — "Start with One Program" and "Schedule a Conversation": contact form, Calendly, email, or `#` placeholder?
- [ ] **Footer content** — privacy policy link? Any legal copy beyond copyright?
- [ ] **Resources content** — placeholder pages are live; real content to drop in later
- [ ] **Logo/favicon** — text wordmark only, or is there a logo asset?
- [ ] **Hosting** — where does the WordPress instance live? (affects deploy step)
