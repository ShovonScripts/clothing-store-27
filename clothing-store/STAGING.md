# Staging Environment Notes

## Purpose
Staging environment mirrors production as closely as possible for final testing before go-live.

## Infrastructure
- **Server**: Separate VPS (e.g., 1 vCPU, 2GB RAM) or subdomain on production server with isolated database
- **Domain**: `staging.yourdomain.com` (or `staging-clothing-store.yourdomain.com`)
- **SSL**: Let's Encrypt via Cloudflare (same as production)
- **Database**: Separate MySQL/MariaDB database (not production data)
- **Queue**: Separate queue worker process
- **Storage**: Local storage (not shared with production)

## Deployment
```bash
# On staging server
./deploy.sh staging
```

## Configuration Differences from Production
| Setting | Production | Staging |
|---------|------------|---------|
| `APP_ENV` | production | staging |
| `APP_DEBUG` | false | true |
| `APP_URL` | https://yourdomain.com | https://staging.yourdomain.com |
| `DB_DATABASE` | clothing_store_prod | clothing_store_staging |
| `META_PIXEL_ID` | Live Pixel ID | Test Pixel ID (or empty) |
| `SESSION_SECURE_COOKIE` | true | true (HTTPS required) |
| `MAIL_MAILER` | smtp/sendgrid | log/mailtrap |
| `QUEUE_CONNECTION` | database | database |

## Test Data
- Use `php artisan migrate:fresh --seed` to reset with demo data
- Create test admin user via seeder or manually
- Test orders with sandbox bKash numbers (use fake TrxIDs)
- Use test phone numbers: 01712345678, 01812345678, etc.

## Testing Checklist (Pre-Go-Live)
- [ ] COD order end-to-end (place → approve → ship → deliver → collect)
- [ ] bKash order (place with fake TrxID → verify → approve)
- [ ] Duplicate TrxID rejection
- [ ] Stock decrement/restore on cancel
- [ ] Delivery charge: Dhaka inside vs outside
- [ ] Coupon apply/block correctly
- [ ] Tracking page works
- [ ] Invoice & packing slip print with Bangla text
- [ ] Order notification appears in admin on mobile
- [ ] Lighthouse mobile ≥ 85 on Home/PDP/Checkout
- [ ] Meta Pixel events fire (check browser console / Facebook Events Manager)
- [ ] UTM params captured in order meta
- [ ] Backup runs and restore tested
- [ ] SSL valid, HTTP→HTTPS redirect

## Monitoring
- UptimeRobot/BetterStack on `https://staging.yourdomain.com/` and `/checkout`
- Log errors to separate channel (not production)
- Queue monitoring: ensure jobs processed

## Access
- Admin: `https://staging.yourdomain.com/admin`
- Basic Auth or IP restrict staging (recommended)
- Share credentials with team via password manager

## Go-Live Promotion
1. Final staging sign-off from owner
2. Run production deploy: `./deploy.sh production`
3. Switch DNS if new server
4. Post-deploy smoke test on production
5. Monitor for 30 minutes

## Rollback Plan
```bash
# On production server
./deploy.sh rollback
```
This switches symlink to previous release and restarts queue workers.