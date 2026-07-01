#!/usr/bin/env bash
set -euo pipefail

echo "==> Starting Laravel API on port ${PORT:-8000}"

if [ -z "${APP_KEY:-}" ]; then
  echo "ERROR: APP_KEY is not set. Add APP_KEY in Railway Variables."
  exit 1
fi

php artisan config:clear
php artisan storage:link 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
