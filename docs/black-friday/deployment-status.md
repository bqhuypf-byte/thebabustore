# Production deployment

Deployed on 2026-11-26 to `51.79.205.170`, alongside the existing ruouvanxuan.com Docker application.

- Public URL: https://thebabustore.vn
- Admin login: https://thebabustore.vn/hg-digital/login
- Application directory: `/opt/thebabustore`
- Compose file: `/opt/thebabustore/deploy/compose.yaml`
- Private backup directory: `/opt/thebabustore-backups`
- Runtime: PHP 8.3, Laravel 12.74.0, Livewire 3.8.12, Filament 3.3.58, separate MySQL 8.4 database.
- Source and media comparison before activation: 4,389 files matched local SHA256 hashes.
- Focused regression tests: 43 passed, 257 assertions. Composer production audit reported no advisories.
- Main site, products, Black Friday, customer/admin login and campaign banner returned HTTPS 200 after activation.
- HTTP and www redirect to https://thebabustore.vn.
- Let's Encrypt certificate covers apex and www; initial expiry 2027-02-24. Certbot renewal and Nginx reload hook configured.
- Daily database backup and weekly media backup configured in `/etc/cron.d/thebabustore`; an immediate database backup was verified by gzip integrity check. These backups reside on this VPS.
- Scheduler configured. Legacy public `/optimize` and `/transactions` are blocked. Demo login disabled in production.
- Existing ruouvanxuan.com remained available and returned HTTPS 200 after activation.

## Remaining operational work

- Outgoing mail intentionally uses `log` until SMTP delivery configuration is verified.
- Queue workers remain disabled: the imported database contains 4,795 historical queued jobs. Separate new production jobs before enabling workers; do not process or delete imported jobs indiscriminately.
- Password-reset action needs actual reset-link delivery; current implementation only displays a confirmation.
- Verify Google OAuth authorized callback in the provider console; presence of credentials is not an end-to-end OAuth verification.
- Imported database is a snapshot of local data, not a migration of subsequent orders from the previous hosting provider.

The current shared edge Nginx container serves both domains. Its persistent source configuration at `/opt/ruouvanxuan/frontend-react-hoidanit/nginx.conf` includes BABU virtual hosts; backups of previous configurations are in the protected BABU backup directory.
