#!/usr/bin/env bash
# list-revisions.sh — List all pages and their revision count on the WP server
# Usage: bash bin/list-revisions.sh [--wp-path=PATH]

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
WP_PATH="${WP_PATH:-.}"

for arg in "$@"; do
  case "$arg" in
    --wp-path=*) WP_PATH="${arg#*=}" ;;
  esac
done

WP="wp --path=$WP_PATH --allow-root"

echo ""
echo "Pages and revision counts"
echo "────────────────────────────────────────────────"
printf "%-6s %-30s %-8s %s\n" "ID" "Slug" "Revisions" "Modified"

while IFS=$'\t' read -r id slug modified; do
  rev_count=$($WP post list \
    --post_type=revision \
    --post_parent="$id" \
    --format=count \
    --quiet 2>/dev/null || echo 0)
  printf "%-6s %-30s %-8s %s\n" "$id" "$slug" "$rev_count" "$modified"
done < <($WP post list \
  --post_type=page \
  --post_status=publish \
  --fields=ID,post_name,post_modified \
  --format=csv \
  --allow-root 2>/dev/null | tail -n +2 | tr -d '\r' | awk -F',' '{print $1"\t"$2"\t"$3}')

echo ""
