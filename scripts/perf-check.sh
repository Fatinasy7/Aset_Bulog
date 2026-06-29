#!/usr/bin/env bash
set -euo pipefail

# perf-check.sh
# Quick performance check helper for local development.
# Usage examples:
#  ./scripts/perf-check.sh --url http://127.0.0.1:8000                        # run lighthouse against URL
#  ./scripts/perf-check.sh --start-server --build --optimize                  # build, optimize, start php artisan serve, then run lighthouse
#  ./scripts/perf-check.sh --url http://127.0.0.1:8000 --mobile               # run mobile emulation
# Notes: requires: node (npx), php, npm. On Windows, run via WSL or adapt commands.

URL="http://127.0.0.1:8000"
START_SERVER=false
DO_BUILD=false
DO_OPTIMIZE=false
EMULATE_MOBILE=false

while [[ $# -gt 0 ]]; do
  case "$1" in
    --url) URL="$2"; shift 2;;
    --start-server) START_SERVER=true; shift;;
    --build) DO_BUILD=true; shift;;
    --optimize) DO_OPTIMIZE=true; shift;;
    --mobile) EMULATE_MOBILE=true; shift;;
    --help) echo "Usage: $0 [--url URL] [--start-server] [--build] [--optimize] [--mobile]"; exit 0;;
    *) echo "Unknown arg: $1"; exit 1;;
  esac
done

command -v npx >/dev/null 2>&1 || { echo "npx is required. Install Node.js and npm."; exit 1; }
command -v php >/dev/null 2>&1 || { echo "php is required."; exit 1; }

ROOT_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT_DIR"

if $DO_BUILD; then
  echo "Running npm ci && npm run build..."
  if [ -f package-lock.json ] || [ -f yarn.lock ]; then
    npm ci
  else
    npm install
  fi
  npm run build
fi

if $DO_OPTIMIZE; then
  echo "Running Laravel optimize commands..."
  php artisan config:cache || true
  php artisan route:cache || true
  php artisan view:cache || true
  php artisan optimize || true
fi

PID_SERVER=""
if $START_SERVER; then
  echo "Starting php artisan serve on 127.0.0.1:8000..."
  php artisan serve --host=127.0.0.1 --port=8000 >/dev/null 2>&1 &
  PID_SERVER=$!
  echo "Started artisan serve (PID: $PID_SERVER). Waiting 1s for server to boot..."
  sleep 1
fi

mkdir -p reports
TS=$(date +%Y%m%d-%H%M%S)

run_lighthouse(){
  local emu="$1"
  local out="reports/lighthouse-${emu}-${TS}.html"
  echo "Running Lighthouse (${emu}) -> ${out}"
  npx --yes lighthouse "$URL" --output html --output-path "$out" --quiet --chrome-flags='--no-sandbox' --emulated-form-factor=${emu}
  echo "Report saved: $out"
}

if $EMULATE_MOBILE; then
  run_lighthouse mobile
else
  run_lighthouse desktop
fi

if [ -n "$PID_SERVER" ]; then
  echo "Stopping artisan serve (PID: $PID_SERVER)"
  kill "$PID_SERVER" || true
fi

echo "Performance check complete. Reports are in the 'reports/' directory." 
