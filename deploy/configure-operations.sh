#!/usr/bin/env bash
set -euo pipefail
umask 077
cd /opt/thebabustore
mkdir -p /opt/thebabustore-backups/operations
cat > /opt/thebabustore-backups/operations/reload-nginx <<'HOOK'
#!/bin/sh
set -eu
/usr/bin/docker exec ruouvanxuan-frontend-1 nginx -t
/usr/bin/docker exec ruouvanxuan-frontend-1 nginx -s reload
HOOK
sudo install -m 755 /opt/thebabustore-backups/operations/reload-nginx /etc/letsencrypt/renewal-hooks/deploy/50-babu-nginx-reload
cat > /opt/thebabustore-backups/operations/babu-cron <<'CRON'
SHELL=/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
17 19 * * * ubuntu /bin/bash /opt/thebabustore/deploy/backup.sh >> /opt/thebabustore-backups/backup-cron.log 2>&1
* * * * * ubuntu cd /opt/thebabustore && /usr/bin/docker compose -f deploy/compose.yaml exec -T --user www-data app php artisan schedule:run >> /opt/thebabustore-backups/scheduler.log 2>&1
CRON
sudo install -m 644 /opt/thebabustore-backups/operations/babu-cron /etc/cron.d/thebabustore
bash deploy/backup.sh
for path in / /up /products /black-friday /account/login /hg-digital/login /storage/promotions/black-friday-2026/chrome/banner.webp; do
  code=$(curl --silent --show-error --output /dev/null --write-out '%{http_code}' "https://thebabustore.vn$path")
  printf '%s %s\n' "$code" "$path"
  test "$code" = 200
done
curl --silent --head http://thebabustore.vn | head -n 5
curl --silent --head https://www.thebabustore.vn | head -n 5
curl --silent --output /dev/null --write-out 'Existing HTTPS: %{http_code}\n' https://ruouvanxuan.com
