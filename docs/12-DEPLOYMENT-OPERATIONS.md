# 12 — Deployment & Operations

## 1. Hosting options (pick one)

| Option | Pros | Cons | Verdict |
|---|---|---|---|
| **VPS (DigitalOcean/Vultr/Hetzner, Singapore region), 2 vCPU / 2–4 GB** | Full control, queue worker + cron, cheap, good latency to BD | You manage the server | **Recommended** |
| BD shared hosting / cPanel | Local billing, simple | Often old PHP, no queue worker, cron limits, slower | Only if PHP 8.3 + SSH + cron available |
| Managed (Laravel Forge + VPS, Ploi) | Easy deploys/SSL | Extra monthly cost | Good for non-devops owner |

Put **Cloudflare** (free plan) in front: DNS, CDN, SSL, basic WAF, bot protection. Domain from a BD registrar or international (.com).

## 2. Server stack (Ubuntu 24.04)
Nginx · PHP-FPM 8.3 (+ OPcache) · MySQL 8 / MariaDB · Node (build only) · Supervisor (queue worker) · Certbot (if not using Cloudflare origin certs) · fail2ban · ufw · (optional Redis).

## 3. First-time setup (summary)
```bash
# system
apt update && apt upgrade -y
apt install nginx mysql-server php8.3-fpm php8.3-{cli,mysql,mbstring,xml,curl,zip,gd,intl,bcmath} composer supervisor unzip git ufw fail2ban
ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw enable

# app
cd /var/www && git clone <repo> app && cd app
composer install --no-dev --optimize-autoloader
cp .env.example .env && php artisan key:generate
# edit .env (DB, APP_URL, mail, pixel, admin seed)
php artisan migrate --force && php artisan db:seed --class=ProductionSeeder
php artisan storage:link
npm ci && npm run build   # or build in CI and upload /public/build
php artisan optimize
chown -R www-data:www-data storage bootstrap/cache
```

Nginx: root `/var/www/app/public`, `try_files $uri /index.php?$query_string`, gzip/brotli on, `client_max_body_size 10M`, cache headers for `/build/*` and `/storage/*`.

Supervisor program:
```
[program:queue]
command=php /var/www/app/artisan queue:work --sleep=3 --tries=3 --max-time=3600
user=www-data
autostart=true
autorestart=true
numprocs=1
stdout_logfile=/var/log/queue.log
```
Cron: `* * * * * www-data php /var/www/app/artisan schedule:run >> /dev/null 2>&1`

## 4. Deployment process (zero-fuss)
Manual or script (`deploy.sh`):
```bash
php artisan down --retry=30
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan optimize:clear && php artisan optimize
php artisan queue:restart
php artisan up
```
Better: GitHub Actions → build assets → rsync/SSH deploy with release directories (Deployer/Envoyer/Forge). Always run tests in CI before deploy.

Branching: `main` (production), `develop`, feature branches via PR. Tag releases.

## 5. Environments
- **local:** Laravel Herd/Valet/Sail/XAMPP; seeded demo data; `MAIL_MAILER=log`.
- **staging (optional):** subdomain with basic auth, copy of production schema, test orders.
- **production.**
Never run `migrate:fresh` or demo seeders in production.

## 6. Backups & recovery
- DB dump daily (spatie/laravel-backup or cron `mysqldump`), keep 14 daily + 8 weekly; copy to **off-server storage** (Backblaze B2/S3/Google Drive).
- Back up `storage/app/public` (images) weekly (or sync to object storage).
- **Test restore quarterly.** Document RPO ≈ 24 h, RTO ≈ 2 h.
- Keep a copy of `.env` in a password manager.

## 7. Monitoring
- Uptime: UptimeRobot/BetterStack on `/` and `/checkout` (free tier).
- Errors: Sentry/Flare free tier, or log review.
- Alerts: queue stalled (heartbeat), disk > 80%, failed jobs count, order intake stopped for > N hours (daytime).
- Weekly: check `failed_jobs`, slow queries, disk, backups success.

## 8. Go-live checklist
**Config**
- [ ] `.env` production values, `APP_DEBUG=false`, correct `APP_URL`, timezone `Asia/Dhaka`
- [ ] Owner admin account created, password changed, 2FA on
- [ ] Settings filled: store name/phone/WhatsApp, **bKash number**, delivery charges, announcement, social links, logo/favicon
- [ ] Pages written: About, Contact, Delivery, Return & Exchange, Privacy, Terms, Size Guide
- [ ] Demo data removed; real products with real photos and correct stock/variants
- [ ] Meta Pixel ID set and tested (Pixel Helper)
- [ ] Sitemap submitted to Google Search Console; robots.txt checked

**Functional tests (on production with a real phone)**
- [ ] COD order end-to-end (place → approve → ship → deliver → collect)
- [ ] bKash order (place with real TrxID → verify → confirm); reject path; duplicate TrxID blocked
- [ ] Stock decrements and restores on cancel
- [ ] Delivery charge Dhaka vs outside Dhaka
- [ ] Coupon applies/blocked correctly
- [ ] Tracking page works; invoice & packing slip print correctly with Bangla text
- [ ] Order notification shows in admin on mobile
- [ ] Emails/SMS (if enabled) deliver
- [ ] Lighthouse mobile ≥ 85 on Home/PDP/Checkout; test on a low-end Android
- [ ] Backup ran and a restore was tested
- [ ] SSL valid, HTTP→HTTPS redirect, www/non-www canonical

**Business**
- [ ] Courier merchant account ready; packaging & invoice slips ready
- [ ] Call script for COD confirmation; bKash verification routine documented for staff
- [ ] Return/exchange handling process agreed
- [ ] Trade licence/TIN/legal items checked by owner

## 9. Day-to-day operating routine (owner/staff)
1. Morning: open **Pending Approval** + **bKash Verification** → verify/approve, call suspicious ones.
2. Print slips for confirmed orders → pack → book courier → enter consignment IDs.
3. Evening: update delivered/failed/returned; mark COD collected; check low stock.
4. Weekly: review cancellations → blacklist; review top products; backup check; update site banners.

## 10. Scaling notes
Expect hundreds of orders/day max early. If traffic grows: add Redis (cache/queue/session), move media to object storage + CDN, enable page caching for anonymous pages, add read replicas only if needed. Don't pre-optimise.
