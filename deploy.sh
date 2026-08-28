#!/usr/bin/env bash
set -Eeuo pipefail

# Post-checkout deployment refresh for gummibeer.dev.
# The deployment system is responsible for checking out the desired Git revision
# before running this script.

cd "$(dirname "${BASH_SOURCE[0]}")"

export APP_ENV="${APP_ENV:-production}"
export COMPOSER_ALLOW_SUPERUSER="${COMPOSER_ALLOW_SUPERUSER:-1}"

log() {
    printf '\n\033[1;36m==> %s\033[0m\n' "$1"
}

fail() {
    printf '\n\033[1;31mERROR: %s\033[0m\n' "$1" >&2
    exit 1
}

for executable in php composer node npm; do
    command -v "$executable" >/dev/null 2>&1 || fail "Missing required executable: $executable"
done

[[ -f .env ]] || fail 'Missing .env file.'
grep -Eq '^APP_KEY=.+$' .env || fail 'APP_KEY is missing from .env.'

log 'Preparing writable Laravel directories'
mkdir -p \
    bootstrap/cache \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/statamic/search
chmod -R ug+rwX bootstrap/cache storage

log 'Installing production PHP dependencies'
composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

log 'Installing Node dependencies'
npm ci --no-audit --no-fund

log 'Building Vite assets'
npm run build

log 'Clearing Laravel caches'
php artisan optimize:clear --no-interaction

log 'Refreshing the Statamic Stache from deployed content'
php artisan statamic:stache:clear --no-interaction
php artisan statamic:stache:warm --no-interaction

log 'Regenerating all Open Graph images'
php artisan generate:og:images --force --no-interaction

# OG images live inside the Statamic asset container, so rebuild the Stache once
# more after generating them to ensure the new asset files are indexed.
log 'Refreshing the Statamic Stache after OG generation'
php artisan statamic:stache:clear --no-interaction
php artisan statamic:stache:warm --no-interaction

log 'Clearing the Statamic Glide image cache'
php artisan statamic:glide:clear --no-interaction

log 'Clearing the Statamic static page cache when enabled'
if static_output="$(php artisan statamic:static:clear --no-interaction 2>&1)"; then
    printf '%s\n' "$static_output"
elif grep -q 'Static caching is not enabled' <<<"$static_output"; then
    printf '%s\n' 'Static caching is disabled; nothing to clear.'
else
    printf '%s\n' "$static_output" >&2
    exit 1
fi

log 'Rebuilding all Statamic local search indexes'
rm -rf storage/statamic/search
mkdir -p storage/statamic/search
php artisan statamic:search:update --all --no-interaction

log 'Rebuilding Laravel production caches'
php artisan optimize --no-interaction

log 'Restoring runtime write permissions'
chmod -R ug+rwX bootstrap/cache storage

log 'Deployment refresh complete'
