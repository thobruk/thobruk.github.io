#!/usr/bin/env bash
# =============================================================
# bin/docker-init.sh — Bootstrap WordPress in the Docker stack
#
# Run once after `docker compose up -d`:
#   docker compose run --rm wpcli bash /saasflow/bin/docker-init.sh
# =============================================================
set -euo pipefail

WP="wp --path=/var/www/html --allow-root"

echo ""
echo "Waiting for database..."
until bash -c 'echo > /dev/tcp/db/3306' 2>/dev/null; do
  printf "."
  sleep 1
done
echo " ready."
echo ""

echo "Installing WordPress core..."
if $WP core is-installed --quiet 2>/dev/null; then
  echo "  (already installed — skipping)"
else
  $WP core install \
    --url="http://localhost:8080" \
    --title="eParamus" \
    --admin_user=admin \
    --admin_password=admin \
    --admin_email=admin@eparamus.test \
    --skip-email
fi
echo ""

WP_PATH=/var/www/html bash /saasflow/bin/seed.sh
