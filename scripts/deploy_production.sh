#!/usr/bin/env bash
set -euo pipefail
# Deploy helper for production: run as the application user (not root).
# Usage: ssh user@host 'bash -s' < deploy_production.sh

REPO_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$REPO_DIR"

echo "Deploy started at $(date) in $REPO_DIR"

if [ "$(id -u)" -eq 0 ]; then
  echo "Warning: running as root is not recommended. Continue at your own risk."
fi

if [ -f bootstrap/cache/config.php ]; then
  echo "Removing cached config..."
  rm -f bootstrap/cache/config.php
fi

echo "Installing composer dependencies (no-dev)..."
composer install --no-dev --optimize-autoloader

echo "Generating optimized autoload..."
composer dump-autoload -o

echo "Clearing framework caches..."
php artisan clear-compiled || true
php artisan config:clear || true
php artisan cache:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "Running database migrations..."
php artisan migrate --force

echo "(Optional) Recreate config cache: uncomment if you want to enable config caching"
# php artisan config:cache

echo "Deploy finished at $(date)"
