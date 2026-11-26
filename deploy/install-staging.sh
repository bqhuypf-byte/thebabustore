#!/usr/bin/env bash
set -euo pipefail
cd /opt/thebabustore
compose=(docker compose -f deploy/compose.yaml)
"${compose[@]}" up -d db
for attempt in $(seq 1 60); do
  if "${compose[@]}" exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysql -h 127.0.0.1 -u"$MYSQL_USER" "$MYSQL_DATABASE" -Nse "SELECT 1"' >/dev/null 2>&1; then break; fi
  sleep 2
done
# This installer only imports into a newly created, empty BABU database.
table_count=$("${compose[@]}" exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysql -u"$MYSQL_USER" "$MYSQL_DATABASE" -Nse "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE()"')
if [ "$table_count" = 0 ]; then
  "${compose[@]}" exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysql -u"$MYSQL_USER" "$MYSQL_DATABASE"' < /opt/thebabustore-backups/database.private.sql
else
  echo 'Existing BABU database detected; import skipped.'
fi
sudo chown ubuntu:33 .env
sudo chmod 640 .env
sudo chown -R 33:33 storage bootstrap/cache
"${compose[@]}" run --rm --no-deps app composer install --no-dev --no-scripts --prefer-dist --optimize-autoloader --no-interaction
"${compose[@]}" run --rm --no-deps app php artisan package:discover --ansi
"${compose[@]}" run --rm --no-deps app php artisan storage:link
"${compose[@]}" run --rm --no-deps app php artisan optimize:clear
"${compose[@]}" run --rm --no-deps app php artisan optimize
sudo chown -R 33:33 storage bootstrap/cache
"${compose[@]}" up -d app web
curl --fail --silent --output /dev/null --write-out 'BABU health: %{http_code}\n' http://127.0.0.1:8082/up
curl --fail --silent --output /dev/null --write-out 'Existing website: %{http_code}\n' https://ruouvanxuan.com

