#!/usr/bin/env bash
set -euo pipefail
umask 077
cd /opt/thebabustore
backup_dir=/opt/thebabustore-backups/daily
mkdir -p "$backup_dir"
stamp=$(date +%Y%m%d-%H%M%S)
temporary="$backup_dir/database-$stamp.sql.gz.partial"
docker compose -f deploy/compose.yaml exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" exec mysqldump -u"$MYSQL_USER" --single-transaction --no-tablespaces --set-gtid-purged=OFF "$MYSQL_DATABASE"' | gzip > "$temporary"
gzip -t "$temporary"
mv "$temporary" "${temporary%.partial}"
find "$backup_dir" -maxdepth 1 -name 'database-*.sql.gz' -type f -mtime +14 -delete
if [ "$(date +%u)" = 7 ]; then
  tar -czf "$backup_dir/media-$stamp.tar.gz" storage/app/public
  find "$backup_dir" -maxdepth 1 -name 'media-*.tar.gz' -type f -mtime +21 -delete
fi

