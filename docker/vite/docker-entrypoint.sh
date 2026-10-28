#!/bin/sh
set -e

LOCK_HASH="/app/node_modules/.package-lock.sha256"
CURRENT_HASH=$(sha256sum /app/package-lock.json 2>/dev/null | awk '{print $1}')

# Check if node_modules volume is empty or package-lock.json was updated
if [ ! -d "/app/node_modules" ] || [ ! -f "$LOCK_HASH" ] || [ "$CURRENT_HASH" != "$(cat "$LOCK_HASH" 2>/dev/null)" ]; then
    echo ">> [Vite] Dependencies missing or package-lock.json changed. Updating node_modules in volume..."
    npm ci
    echo "$CURRENT_HASH" > "$LOCK_HASH"
fi

exec "$@"
