#!/usr/bin/env bash
set -euo pipefail
umask 077
source_config=/opt/ruouvanxuan/frontend-react-hoidanit/nginx.conf
backup_dir=/opt/thebabustore-backups/edge
mkdir -p "$backup_dir"
stamp=$(date +%Y%m%d-%H%M%S)
cp "$source_config" "$backup_dir/original-source-$stamp.conf"
docker cp ruouvanxuan-frontend-1:/etc/nginx/conf.d/default.conf "$backup_dir/original-running-$stamp.conf"
if grep -q 'server_name thebabustore.vn' "$source_config"; then
  echo 'BABU host already exists; refusing to append twice.'
  exit 1
fi
cat "$source_config" /opt/thebabustore/deploy/edge-http.conf > "$backup_dir/next.conf"
docker cp "$backup_dir/next.conf" ruouvanxuan-frontend-1:/etc/nginx/conf.d/default.conf
if docker exec ruouvanxuan-frontend-1 nginx -t; then
  cp "$backup_dir/next.conf" "$source_config"
  docker exec ruouvanxuan-frontend-1 nginx -s reload
else
  docker cp "$backup_dir/original-running-$stamp.conf" ruouvanxuan-frontend-1:/etc/nginx/conf.d/default.conf
  exit 1
fi
curl --fail --silent --output /dev/null --write-out 'Existing HTTPS: %{http_code}\n' https://ruouvanxuan.com
curl --silent --output /dev/null --write-out 'BABU pre-launch: %{http_code}\n' -H 'Host: thebabustore.vn' http://127.0.0.1
