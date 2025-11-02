#!/usr/bin/env bash
set -euo pipefail

PROJECT_ROOT="$(pwd)"
echo "== Flea Market App Local Verification =="
echo "Project root: $PROJECT_ROOT"
echo

# Helper for colored output
green() { printf "\033[32m%s\033[0m\n" "$*"; }
red()   { printf "\033[31m%s\033[0m\n" "$*"; }
yellow(){ printf "\033[33m%s\033[0m\n" "$*"; }

# 0) Basic sanity checks
if ! command -v docker >/dev/null 2>&1; then
  red "Docker is not installed or not in PATH."
  exit 1
fi
if command -v docker compose >/dev/null 2>&1; then
  DC="docker compose"
elif command -v docker-compose >/dev/null 2>&1; then
  DC="docker-compose"
else
  red "docker compose / docker-compose not found."
  exit 1
fi

if [ ! -f "docker-compose.yml" ] && [ ! -f "docker-compose.yaml" ]; then
  red "docker-compose.yml not found. Run this from the repository root."
  exit 1
fi

# 1) Bring containers up if not running
yellow "Checking containers..."
$DC ps
echo

# 2) Ensure .env exists
if [ ! -f ".env" ]; then
  yellow ".env not found. Creating from .env.example ..."
  $DC exec -T php cp .env.example .env || { red "Failed to copy .env"; exit 1; }
  $DC exec -T php php artisan key:generate || true
fi

# 3) Composer install (idempotent)
yellow "Installing PHP dependencies (composer install)..."
$DC exec -T php bash -lc "composer install --no-interaction --prefer-dist"
echo

# 4) App key (idempotent)
yellow "Ensuring app key is set..."
$DC exec -T php php artisan key:generate || true
echo

# 5) Database connectivity check
yellow "Checking MySQL connectivity..."
if $DC exec -T mysql mysql -ularavel_user -plaravel_pass -e "SELECT 1" >/dev/null 2>&1; then
  green "MySQL connectivity OK."
else
  red "MySQL connectivity failed. Verify DB credentials in .env and container health."
  exit 1
fi
echo

# 6) Migrations & seed
yellow "Running migrations & seed..."
$DC exec -T php php artisan migrate --force
$DC exec -T php php artisan db:seed --force || true
echo

# 7) Storage link (optional)
yellow "Creating storage symlink (if needed)..."
$DC exec -T php php artisan storage:link || true
echo

# 8) Route & config sanity
yellow "Listing routes count..."
ROUTES=$($DC exec -T php php artisan route:list | wc -l | tr -d '[:space:]')
echo "Routes count: $ROUTES"
echo

# 9) HTTP health checks
yellow "HTTP health checks (localhost)..."
H1=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/ || true)
H2=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/register || true)

echo "GET /           -> HTTP $H1"
echo "GET /register   -> HTTP $H2"

if [[ "$H1" =~ ^2|3 ]] && [[ "$H2" =~ ^2|3 ]]; then
  green "HTTP endpoints reachable."
else
  yellow "Endpoints did not return 2xx/3xx. Check nginx/PHP-FPM logs."
fi
echo

# 10) Useful logs hints
yellow "Recent container logs (last 50 lines each):"
for svc in php nginx mysql; do
  echo "--- $svc logs ---"
  $DC logs --tail=50 $svc || true
  echo
done

green "All checks done."
