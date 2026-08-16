#!/usr/bin/env bash
set -e

export PYTHONPATH="${PYTHONPATH}:$(pwd)/ai_service"

if [[ -n "${DB_SSL_CA_PEM:-}" ]]; then
    printf '%s\n' "$DB_SSL_CA_PEM" > /tmp/aiven-ca.pem
    chmod 644 /tmp/aiven-ca.pem
    export DB_SSL_CA=/tmp/aiven-ca.pem
fi

echo "Starting RADIUS AI service..."

python3 -m uvicorn ai_service.main:app \
    --host 127.0.0.1 \
    --port 8001 &

AI_PID=$!

trap 'kill $AI_PID 2>/dev/null || true' EXIT

echo "Waiting for AI service to become ready..."

MAX_WAIT=180
WAITED=0

until curl -sf http://127.0.0.1:8001/health > /dev/null 2>&1; do

    if [[ $WAITED -ge $MAX_WAIT ]]; then
        echo "ERROR: AI service did not become ready within ${MAX_WAIT}s."
        exit 1
    fi

    sleep 2
    WAITED=$((WAITED + 2))

    echo "Still waiting for AI service... (${WAITED}s)"
done

echo "AI service is READY."
echo "Starting PHP server..."

php -S 0.0.0.0:${PORT:-3000}