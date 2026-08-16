#!/usr/bin/env bash
set -e

export PYTHONPATH="${PYTHONPATH}:$(pwd)/ai_service"

if [[ -n "${DB_SSL_CA_PEM:-}" ]]; then
    printf '%s\n' "$DB_SSL_CA_PEM" > /tmp/aiven-ca.pem
    chmod 644 /tmp/aiven-ca.pem
    export DB_SSL_CA=/tmp/aiven-ca.pem
fi

python3 -m uvicorn ai_service.main:app --host 127.0.0.1 --port 8001 &
AI_PID=$!

trap 'kill $AI_PID 2>/dev/null || true' EXIT

php -S 0.0.0.0:${PORT:-3000}