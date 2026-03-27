#!/bin/bash
set -euo pipefail

# ============================================
# Card Bastion - Deploy script for Hostinger
# Project path: /domains/cardbastion.com/
# Public path:  public_html/
# ============================================

APP_ROOT="/home/u769713173/domains/cardbastion.com"
PHP_BIN="php"
COMPOSER_BIN="composer"

cd "$APP_ROOT"

echo "== Pull latest code =="
git pull origin main

echo "== Install PHP dependencies =="
$COMPOSER_BIN install --no-dev --optimize-autoloader --no-interaction

echo "== Ensure Laravel writable directories =="
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

echo "== Clear old Laravel caches =="
$PHP_BIN artisan optimize:clear || true

echo "== Prepare public_html =="
mkdir -p public_html
find public_html -mindepth 1 -maxdepth 1 -exec rm -rf {} +

echo "== Copy public assets to public_html =="
rsync -av --delete public/ public_html/

echo "== Sync storage to public_html =="
rm -rf public_html/storage
mkdir -p public_html/storage
rsync -av --delete storage/app/public/ public_html/storage/

echo "== Run database migrations =="
$PHP_BIN artisan migrate --force

echo "== Cache Laravel config/routes/views =="
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "== Fix permissions =="
chmod -R 775 storage bootstrap/cache public_html/storage || true

echo "== Deploy completed successfully =="
