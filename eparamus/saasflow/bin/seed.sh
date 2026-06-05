#!/usr/bin/env bash
# =============================================================
# bin/seed.sh — Seed eParamus pages into WordPress via WP-CLI
#
# Usage:
#   bash bin/seed.sh [options]
#
# Options:
#   --reset          Delete and recreate all pages (default: upsert)
#   --dry-run        Print what would happen without making changes
#   --wp-path=PATH   Path to WordPress root (default: $WP_PATH or current dir)
#   --content=PATH   Path to content/ directory (default: sibling of bin/)
#
# Examples:
#   bash bin/seed.sh
#   bash bin/seed.sh --reset
#   WP_PATH=/var/www/html bash bin/seed.sh
#   bash bin/seed.sh --wp-path=/var/www/html --content=/project/content
# =============================================================
set -uo pipefail

# ── Configuration ─────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_PATH="${WP_PATH:-.}"
CONTENT_DIR="$SCRIPT_DIR/../content"
RESET=false
DRY_RUN=false

for arg in "$@"; do
  case "$arg" in
    --reset)          RESET=true ;;
    --dry-run)        DRY_RUN=true ;;
    --wp-path=*)      WP_PATH="${arg#*=}" ;;
    --content=*)      CONTENT_DIR="${arg#*=}" ;;
  esac
done

WP="wp --path=$WP_PATH --allow-root"

# ── Output helpers ─────────────────────────────────────────────
BOLD="\033[1m"; RESET_C="\033[0m"; GREEN="\033[32m"; YELLOW="\033[33m"; RED="\033[31m"; DIM="\033[2m"

log()  { echo -e "  $*" >&2; }
ok()   { echo -e "  ${GREEN}✓${RESET_C} $*" >&2; }
info() { echo -e "  ${DIM}→${RESET_C} $*" >&2; }
warn() { echo -e "  ${YELLOW}⚠${RESET_C}  $*" >&2; }
die()  { echo -e "  ${RED}✗${RESET_C}  $*" >&2; exit 1; }
dry()  { echo -e "  ${DIM}[dry-run]${RESET_C} $*" >&2; }

# ── Preflight ─────────────────────────────────────────────────
echo ""
echo -e "${BOLD}eParamus — WordPress seed script${RESET_C}"
echo    "────────────────────────────────────────"
info "WordPress:   $WP_PATH"
info "Content dir: $CONTENT_DIR"
[ "$RESET"   = true ] && warn "RESET mode — existing pages will be deleted and recreated"
[ "$DRY_RUN" = true ] && warn "DRY RUN — no changes will be made"
echo ""

# Verify WP-CLI + WordPress
if ! command -v wp &>/dev/null; then
  die "WP-CLI not found. Install from https://wp-cli.org"
fi

if ! $WP core is-installed --quiet 2>/dev/null; then
  die "WordPress not found at '$WP_PATH'. Set --wp-path or \$WP_PATH."
fi

if ! $WP theme is-installed eparamus --quiet 2>/dev/null; then
  die "Theme 'eparamus' is not installed. Copy theme/ into wp-content/themes/eparamus first."
fi

# ── Helpers ───────────────────────────────────────────────────

# Read a content file; warn and return empty string if missing
content_of() {
  local path="$CONTENT_DIR/$1"
  if [ -f "$path" ]; then
    cat "$path"
  else
    warn "Content file not found: $1 — page will be created empty"
    echo ""
  fi
}

# Return the ID of a published/draft page by slug, or empty string
find_page() {
  local slug="$1"
  $WP post list \
    --post_type=page \
    --name="$slug" \
    --post_status=any \
    --field=ID \
    --format=ids \
    2>/dev/null || true
}

# Create or update a page. Prints the page ID.
# upsert_page TITLE SLUG CONTENT_FILE [PARENT_ID]
upsert_page() {
  local title="$1"
  local slug="$2"
  local content_file="$3"
  local parent_id="${4:-0}"
  local existing_id
  existing_id="$(find_page "$slug")"

  if [ "$DRY_RUN" = true ]; then
    if [ -n "$existing_id" ]; then
      dry "Would update: $title (slug: $slug, existing ID: $existing_id)"
    else
      dry "Would create: $title (slug: $slug)"
    fi
    echo "0"
    return
  fi

  local content
  content="$(content_of "$content_file")"

  if [ -n "$existing_id" ] && [ "$RESET" = false ]; then
    # Upsert: update in place
    $WP post update "$existing_id" \
      --post_title="$title" \
      --post_content="$content" \
      --post_parent="$parent_id" \
      --quiet
    ok "Updated:  $title  ${DIM}(ID $existing_id)${RESET_C}"
    echo "$existing_id"

  else
    # Delete first if resetting
    if [ -n "$existing_id" ] && [ "$RESET" = true ]; then
      $WP post delete "$existing_id" --force --quiet
    fi
    # Create fresh
    local new_id
    new_id="$($WP post create \
      --post_type=page \
      --post_title="$title" \
      --post_name="$slug" \
      --post_status=publish \
      --post_content="$content" \
      --post_parent="$parent_id" \
      --porcelain)"
    ok "Created:  $title  ${DIM}(ID $new_id)${RESET_C}"
    echo "$new_id"
  fi
}

# ── 1. Theme ───────────────────────────────────────────────────
echo -e "${BOLD}Theme${RESET_C}"
if [ "$DRY_RUN" = false ]; then
  $WP theme activate eparamus --quiet
  ok "Theme activated: eparamus"
else
  dry "Would activate theme: eparamus"
fi
echo ""

# ── 2. Site identity ───────────────────────────────────────────
echo -e "${BOLD}Site identity${RESET_C}"
if [ "$DRY_RUN" = false ]; then
  $WP option update blogname "eParamus" --quiet
  $WP option update blogdescription "Define. Build. Verify Workforce Capability." --quiet
  ok "Name:    eParamus"
  ok "Tagline: Define. Build. Verify Workforce Capability."
else
  dry "Would set blogname and blogdescription"
fi
echo ""

# ── 3. Pages ───────────────────────────────────────────────────
echo -e "${BOLD}Pages${RESET_C}"

# Top-level pages
HOME_ID=$(upsert_page \
  "Home"         "home"         "home.html"         0)

upsert_page \
  "Getting Started" "getting-started" "getting-started.html" 0 > /dev/null

HIWORKS_ID=$(upsert_page \
  "How It Works" "how-it-works" "how-it-works.html" 0)

ABOUT_ID=$(upsert_page \
  "About"        "about"        "about.html"        0)

# Platform section
PLATFORM_ID=$(upsert_page \
  "Platform"     "platform"     "platform-overview.html" 0)

upsert_page \
  "MID Generator" \
  "mid-generator" \
  "platform-mid-generator.html" \
  "$PLATFORM_ID" > /dev/null

upsert_page \
  "Workforce Capability Insight" \
  "workforce-capability-insight" \
  "platform-workforce-capability-insight.html" \
  "$PLATFORM_ID" > /dev/null

# Solutions section
SOLUTIONS_ID=$(upsert_page \
  "Solutions"    "solutions"    "solutions-overview.html" 0)

upsert_page \
  "Learning Leaders" \
  "learning-leaders" \
  "solutions-learning-leaders.html" \
  "$SOLUTIONS_ID" > /dev/null

upsert_page \
  "Instructional Design Teams" \
  "instructional-design-teams" \
  "solutions-instructional-design-teams.html" \
  "$SOLUTIONS_ID" > /dev/null

upsert_page \
  "Business Leaders" \
  "business-leaders" \
  "solutions-business-leaders.html" \
  "$SOLUTIONS_ID" > /dev/null

# Resources section
RESOURCES_ID=$(upsert_page \
  "Resources"    "resources"    "resources-overview.html" 0)

upsert_page \
  "Articles"     "articles"     "resources-articles.html"     "$RESOURCES_ID" > /dev/null
upsert_page \
  "Case Studies" "case-studies" "resources-case-studies.html" "$RESOURCES_ID" > /dev/null
upsert_page \
  "Videos"       "videos"       "resources-videos.html"       "$RESOURCES_ID" > /dev/null
upsert_page \
  "Guides"       "guides"       "resources-guides.html"       "$RESOURCES_ID" > /dev/null

echo ""

# ── 4. Front page ──────────────────────────────────────────────
echo -e "${BOLD}Front page${RESET_C}"
if [ "$DRY_RUN" = false ] && [ "$HOME_ID" != "0" ]; then
  $WP option update show_on_front page --quiet
  $WP option update page_on_front "$HOME_ID" --quiet
  ok "Front page set to: Home (ID $HOME_ID)"
else
  dry "Would set front page to Home"
fi
echo ""

# ── 5. Permalinks ──────────────────────────────────────────────
echo -e "${BOLD}Permalinks${RESET_C}"
if [ "$DRY_RUN" = false ]; then
  $WP rewrite structure '/%postname%/' --hard --quiet
  $WP rewrite flush --hard --quiet
  ok "Structure: /%postname%/ (flushed)"
else
  dry "Would set permalink structure to /%postname%/"
fi
echo ""

# ── 6. Summary ─────────────────────────────────────────────────
if [ "$DRY_RUN" = false ]; then
  echo -e "${BOLD}Published pages${RESET_C}"
  $WP post list \
    --post_type=page \
    --post_status=publish \
    --fields=ID,post_title,post_name,post_parent \
    --format=table
  echo ""
fi

echo -e "${GREEN}${BOLD}Seed complete.${RESET_C}"
echo ""
