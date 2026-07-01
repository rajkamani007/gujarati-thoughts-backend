#!/usr/bin/env bash
set -euo pipefail

echo "==> Railway database check"

if [ -n "${MYSQL_URL:-}" ] || [ -n "${DATABASE_URL:-}" ] || [ -n "${MYSQLHOST:-}" ] || [ -n "${DB_HOST:-}" ]; then
  echo "MySQL configuration detected."
else
  echo "ERROR: No MySQL variables found."
  echo "In Railway: open backend service -> Variables -> add MySQL service reference."
  echo "Required: MYSQLHOST, MYSQLPORT, MYSQLUSER, MYSQLPASSWORD, MYSQLDATABASE"
  exit 1
fi

php artisan config:clear
php artisan migrate --force
echo "==> Migrations complete"
