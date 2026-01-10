#!/usr/bin/env bash
set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

echo "Running production config fix in $APP_DIR"

if [ ! -f config/app.php ]; then
  echo "config/app.php not found. Abort." >&2
  exit 1
fi

echo "Backing up bootstrap/cache/... to bootstrap/cache.bak"
rm -rf bootstrap/cache.bak || true
mkdir -p bootstrap/cache.bak
cp -r bootstrap/cache/* bootstrap/cache.bak/ 2>/dev/null || true

echo "Removing compiled config/services files if present"
rm -f bootstrap/cache/config.php bootstrap/cache/services.php

echo "Verifying config/app.php includes conditional checks for dev providers..."
grep -q "Barryvdh\\\LaravelIdeHelper\\\IdeHelperServiceProvider" config/app.php || true

echo "Installing composer dependencies (no-dev)..."
if command -v composer >/dev/null 2>&1; then
  composer install --no-dev --no-interaction --optimize-autoloader
  composer dump-autoload -o
else
  echo "composer not found in PATH. Please install composer or run these steps manually." >&2
  exit 2
fi

echo "Clearing Laravel caches"
php artisan clear-compiled || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Rebuilding config cache"
php artisan config:cache

echo "Done. If errors persist, check bootstrap/cache.bak for previous caches and config/app.php for hard-coded providers."
