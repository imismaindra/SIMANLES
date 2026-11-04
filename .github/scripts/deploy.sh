#!/usr/bin/env bash
# =============================================================================
# deploy.sh — Post-deploy script yang dijalankan oleh GitHub Actions via SSH
# Jalankan dari root direktori aplikasi di server cPanel
# =============================================================================
set -euo pipefail

APP_PATH="${APP_PATH:-$(pwd)}"
PHP="${PHP_BIN:-php}"

cd "$APP_PATH"

echo "▶ [1/7] Maintenance mode ON..."
$PHP artisan down \
  --retry=5 \
  --render="errors::503" \
  --secret="${MAINTENANCE_SECRET:-}" \
  || true

echo "▶ [2/7] Running migrations..."
$PHP artisan migrate --force

echo "▶ [3/7] Clearing caches..."
$PHP artisan config:clear
$PHP artisan route:clear
$PHP artisan view:clear
$PHP artisan event:clear
$PHP artisan cache:clear
$PHP artisan schedule:clear-mutex 2>/dev/null || true

echo "▶ [4/7] Rebuilding optimized caches..."
$PHP artisan optimize        # config + route + events
$PHP artisan view:cache

echo "▶ [5/7] Storage symlink..."
$PHP artisan storage:link --relative 2>/dev/null || true

echo "▶ [6/7] Fixing permissions..."
find storage    -type d -exec chmod 755 {} \;
find storage    -type f -exec chmod 644 {} \;
find bootstrap/cache -type d -exec chmod 755 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;

echo "▶ [7/7] Maintenance mode OFF..."
$PHP artisan up

echo "✅ Deployment complete!"
