#!/usr/bin/env bash
set -euo pipefail

# Default PORT for local docker run
: "${PORT:=8080}"

# Ensure Apache listens on $PORT
if grep -qE '^Listen ' /etc/apache2/ports.conf; then
  sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
else
  echo "Listen ${PORT}" >> /etc/apache2/ports.conf
fi

# Laravel optimizations (safe to run on boot)
if [ -f artisan ]; then
  # Generate key only if not set
  if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force || true
  fi
  php artisan migrate --force || true
  php artisan storage:link || true
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
fi

# Run Apache in foreground
exec apache2-foreground
