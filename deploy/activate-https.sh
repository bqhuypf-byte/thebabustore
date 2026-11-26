#!/usr/bin/env bash
set -euo pipefail
umask 077
cd /opt/thebabustore
test -s /etc/letsencrypt/live/thebabustore.vn/fullchain.pem || sudo test -s /etc/letsencrypt/live/thebabustore.vn/fullchain.pem
docker exec ruouvanxuan-frontend-1 getent hosts babu-web >/dev/null
source_config=/opt/ruouvanxuan/frontend-react-hoidanit/nginx.conf
backup_dir=/opt/thebabustore-backups/edge
stamp=$(date +%Y%m%d-%H%M%S)
mkdir -p "$backup_dir"
cp "$source_config" "$backup_dir/before-https-$stamp.conf"
python3 - <<'PY'
from pathlib import Path
current = Path('/opt/ruouvanxuan/frontend-react-hoidanit/nginx.conf').read_text()
marker = '# BABU pre-launch host:'
if current.count(marker) != 1:
    raise SystemExit('Expected exactly one pre-launch BABU host; no changes made.')
next_config = current.split(marker)[0] + '\n# BABU production HTTPS\n' + Path('/opt/thebabustore/deploy/edge-https.conf').read_text()
Path('/opt/thebabustore-backups/edge/production-next.conf').write_text(next_config)
PY
docker cp "$backup_dir/production-next.conf" ruouvanxuan-frontend-1:/etc/nginx/conf.d/default.conf
if docker exec ruouvanxuan-frontend-1 nginx -t; then
    cp "$backup_dir/production-next.conf" "$source_config"
    docker exec ruouvanxuan-frontend-1 nginx -s reload
else
    docker cp "$backup_dir/before-https-$stamp.conf" ruouvanxuan-frontend-1:/etc/nginx/conf.d/default.conf
    exit 1
fi
curl --retry 3 --retry-all-errors --fail --silent --output /dev/null --write-out 'BABU HTTPS: %{http_code}\n' https://thebabustore.vn/up
curl --retry 3 --retry-all-errors --fail --silent --output /dev/null --write-out 'Existing HTTPS: %{http_code}\n' https://ruouvanxuan.com
