#!/bin/sh
set -e

cd /var/www/html

# ── Ensure writable dirs (important when /storage is a persistent volume) ───
mkdir -p \
    storage/app/public \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache
chown -R nobody:nobody storage bootstrap/cache

# ── Public storage symlink ───────────────────────────────────────────────────
[ -L public/storage ] || php artisan storage:link >/dev/null 2>&1 || true

# ── Application key ─────────────────────────────────────────────────────────
# In production Coolify injects APP_KEY via env; generate only as fallback.
if [ -z "$APP_KEY" ] && ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    php artisan key:generate --force
fi

# ── Optional migrations (set RUN_MIGRATIONS=true in Coolify env) ─────────────
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "→ Running migrations..."
    php artisan migrate --force --no-interaction
fi

# ── Runtime caches (safe to re-run every boot) ───────────────────────────────
php artisan config:cache
php artisan view:cache

echo "✓ Application ready"
exec "$@"