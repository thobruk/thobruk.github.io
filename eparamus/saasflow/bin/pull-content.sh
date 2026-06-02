#!/usr/bin/env bash
# pull-content.sh — Pull live page content from production into content/*.html
# Uses a single SSH connection via ControlMaster to avoid fail2ban.
#
# Usage:
#   bash bin/pull-content.sh --host=user@host [--wp-path=PATH]

set -uo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CONTENT_DIR="$SCRIPT_DIR/../content"
HOST=""
WP_PATH="/var/www/html"

for arg in "$@"; do
  case "$arg" in
    --host=*)    HOST="${arg#*=}" ;;
    --wp-path=*) WP_PATH="${arg#*=}" ;;
  esac
done

if [[ -z "$HOST" ]]; then
  echo "Usage: bash bin/pull-content.sh --host=user@host [--wp-path=PATH]"
  exit 1
fi

# SSH multiplexing socket
SOCK="/tmp/pull-content-ssh-$$"
SSH="ssh -o ControlMaster=auto -o ControlPath=$SOCK -o ControlPersist=60"

# Open the master connection
$SSH -fN "$HOST"

cleanup() { ssh -O exit -o ControlPath="$SOCK" "$HOST" 2>/dev/null; }
trap cleanup EXIT

WP="wp --path=$WP_PATH --allow-root"

declare -A SLUG_FILE=(
  [home]="home"
  [how-it-works]="how-it-works"
  [about]="about"
  [platform]="platform-overview"
  [mid-generator]="platform-mid-generator"
  [workforce-capability-insight]="platform-workforce-capability-insight"
  [solutions]="solutions-overview"
  [learning-leaders]="solutions-learning-leaders"
  [instructional-design-teams]="solutions-instructional-design-teams"
  [business-leaders]="solutions-business-leaders"
  [resources]="resources-overview"
  [articles]="resources-articles"
  [case-studies]="resources-case-studies"
  [videos]="resources-videos"
  [guides]="resources-guides"
)

echo ""
echo "Pulling production content from $HOST"
echo "────────────────────────────────────────────────"

for slug in "${!SLUG_FILE[@]}"; do
  fname="${SLUG_FILE[$slug]}"
  outfile="$CONTENT_DIR/${fname}.html"

  page_id=$($SSH "$HOST" "$WP post list --post_type=page --name=$slug --post_status=any --field=ID --format=ids 2>/dev/null" | tr -d '[:space:]')

  if [[ -z "$page_id" ]]; then
    echo "  ✗ $slug — not found"
    continue
  fi

  $SSH "$HOST" "$WP post get $page_id --field=post_content 2>/dev/null" > "$outfile"
  echo "  ✓ $slug → content/${fname}.html"
done

echo ""
echo "Done. Review changes with: git diff content/"
