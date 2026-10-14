#!/bin/sh
set -eu
cd "$(dirname "$0")/.."
if [ ! -f .env ]; then
    umask 077
    {
        printf 'APP_PORT=8080\nLOCAL_UID=%s\nLOCAL_GID=%s\nCAKE_DEBUG=true\n' "$(id -u)" "$(id -g)"
        printf 'SECURITY_SALT=%s\n' "$(openssl rand -hex 32)"
        printf 'MYSQL_DATABASE=cakephp\nMYSQL_USER=cakephp\n'
        printf 'MYSQL_PASSWORD=%s\n' "$(openssl rand -hex 24)"
        printf 'MYSQL_ROOT_PASSWORD=%s\n' "$(openssl rand -hex 24)"
    } > .env
fi
umask 022
docker compose build app
docker compose run --rm --no-deps app composer install --no-interaction --prefer-dist
mkdir -p app/tmp/cache/models app/tmp/cache/persistent app/tmp/cache/views app/logs
chmod -R u+rwX,go-w app/tmp app/logs
docker compose up -d --wait
