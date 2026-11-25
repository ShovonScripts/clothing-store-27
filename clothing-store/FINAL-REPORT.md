# FINAL-REPORT.md — Phase 5 Polish & Hardening

## Go-Live Checklist (from docs/12 §8)

### Legend
- **Verified in dev (evidence)**: Working in development with automated test evidence
- **Needs production/real device**: Cannot be verified in dev; requires real device/production environment
- **Needs owner**: Requires owner action (credentials, business setup, legal)

---

### Config

| Item | Verified in dev (evidence) | Needs production/real device | Needs owner |
|------|---------------------------|------------------------------|-------------|
| `.env` production values, `APP_DEBUG=false`, correct `APP_URL`, timezone `Asia/Dhaka` | ✅ Unit test: env values loaded | ❌ Requires real server | ❌ Owner sets values |
| Owner admin account created, password changed, 2FA on | ✅ Seeder creates user from env | ❌ Requires real admin login | ❌ Owner sets ADMIN_EMAIL/PASSWORD |
| Settings filled: store name/phone/WhatsApp, **bKash number**, delivery charges, announcement, social links, logo/favicon | ✅ SettingsSeeder + SettingsService tests | ❌ Real bKash number required | ❌ Owner provides |
| Pages written: About, Contact, Delivery, Return & Exchange, Privacy, Terms, Size Guide | ✅ PageSeeder creates stubs | ❌ Real content required | ❌ Owner writes content |
| Demo data removed; real products with real photos and correct stock/variants | ✅ ProductionSeeder creates 0 products | ❌ Real products required | ❌ Owner adds products |
| Meta Pixel ID set and tested (Pixel Helper) | ✅ MetaPixelService + layout integration | ❌ Real Pixel ID | ❌ Owner provides Pixel ID |
| Sitemap submitted to Google Search Console; robots.txt checked | ✅ SitemapController + RobotsController tests | ❌ Requires live domain | ❌ Owner submits |

---

### Functional Tests (on production with a real phone)

| Item | Verified in dev (evidence) | Needs production/real device | Needs owner |
|------|---------------------------|------------------------------|-------------|
| COD order end-to-end (place → approve → ship → deliver → collect) | ✅ OrderActionsTest, OrderLifecycleTest | ❌ Real phone, real courier | ❌ Owner tests |
| bKash order (place with real TrxID → verify → confirm); reject path; duplicate TrxID blocked | ✅ OrderActionsTest (duplicate TrxID test) | ❌ Real bKash payment | ❌ Owner provides bKash number |
| Stock decrements and restores on cancel | ✅ StockService tests, OrderActionsTest | ❌ Real inventory | ❌ Owner manages stock |
| Delivery charge Dhaka vs outside Dhaka | ✅ DeliveryChargeService tests, PricingService tests | ❌ Real address | ❌ Owner sets zones |
| Coupon applies/blocked correctly | ✅ CouponService tests, Coupon usage tests | ❌ Real coupons | ❌ Owner creates coupons |
| Tracking page works; invoice & packing slip print correctly with Bangla text | ✅ TrackOrder component test, invoice/packing-slip views | ❌ Real order, Bangla font | ❌ Owner adds fonts |
| Order notification shows in admin on mobile | ✅ NotifyAdminsOfNewOrder listener test | ❌ Real mobile device | ❌ Owner checks |
| Emails/SMS (if enabled) deliver | ⚠️ Mail::fake() tests only | ❌ Real SMTP/SMS gateway | ❌ Owner configures |
| Lighthouse mobile ≥ 85 on Home/PDP/Checkout; test on a low-end Android | ⚠️ Home/Search/Categories pass budget; Product/Shop/Checkout exceed 90KB JS | ❌ **Never run on real device**; Lighthouse never executed | ❌ Owner runs Lighthouse |
| Backup ran and a restore was tested | ⚠️ Backup script exists | ❌ Never run on production server | ❌ Owner runs backup & restore test |
| SSL valid, HTTP→HTTPS redirect, www/non-www canonical | ⚠️ SecurityHeaders middleware tests | ❌ Requires real domain/SSL | ❌ Owner provisions SSL |

---

### Business

| Item | Verified in dev (evidence) | Needs production/real device | Needs owner |
|------|---------------------------|------------------------------|-------------|
| Courier merchant account ready; packaging & invoice slips ready | ❌ N/A | ❌ Business setup | ❌ Owner |
| Call script for COD confirmation; bKash verification routine documented for staff | ❌ N/A | ❌ Business process | ❌ Owner |
| Return/exchange handling process agreed | ❌ N/A | ❌ Business process | ❌ Owner |
| Trade licence/TIN/legal items checked by owner | ❌ N/A | ❌ Legal compliance | ❌ Owner |

---

## Manual Steps for Owner (Post-Go-Live)

1. **Verify Meta Pixel** in Facebook Events Manager: check PageView, ViewContent, AddToCart, InitiateCheckout, Lead events fire.
2. **Test real bKash payment** with your own number; verify admin can approve.
3. **Confirm courier pickup** schedule and packaging.
4. **Set up monitoring alerts**: UptimeRobot on `/` and `/checkout`, queue monitoring.
5. **Review first 10 orders** manually for any issues.
6. **Schedule weekly backup restore test** (first Friday after launch).
7. **Document staff procedures**: COD call script, bKash verification, return handling.
8. **Add Bangla font files** (`Hind Siliguri`/`Inter`) to `public/fonts/` for production rendering.

---

## Known Limitations

1. **Meta Pixel Purchase event**: Server-side Purchase event is logged but not sent to Facebook Conversions API (CAPI). CAPI integration planned for Phase 6.
2. **Livewire JS budget**: Product/Shop/Checkout pages exceed 90KB JS budget due to Livewire bundle (610KB raw / ~129KB gz est.). Home/Search/Categories/Pages stay under budget (~80KB gz).
3. **bKash verification**: Manual process only; no automated TrxID verification via bKash API.
4. **SMS notifications**: Not implemented (Phase 6). Order confirmations via admin call only.
5. **Courier API**: Manual booking only; no Steadfast/Pathao/RedX integration (Phase 6).
6. **Order editing**: Admin cannot edit order items after placement (Phase 6).
7. **Partial COD advance**: Not supported (Phase 6).
8. **Reviews/Wishlist**: Not implemented (Phase 6).
9. **Bangla font files**: Owner must add `Hind Siliguri`/`Inter` font files to `public/fonts/` for production rendering.
10. **Lighthouse never executed**: No Lighthouse CI or real-device audit performed; mobile performance on real devices unverified.
11. **Backup restore never tested**: Backup script exists but never executed/restored on production-like server.

---

## Phase 6 Backlog (Priority Order)

1. SMS notifications (placed/confirmed/shipped) via local gateway behind `SmsGateway`
2. Courier API (Steadfast/Pathao/RedX): booking, tracking sync, fees & COD settlement reports
3. Meta Conversions API (server events on approval)
4. bKash TrxID resend flow + optional screenshot upload
5. Partial COD advance toggle (outside Dhaka)
6. Reviews & ratings, wishlist, recently viewed
7. Admin order editing (add/remove items), exchange workflow
8. CSV import/export for products; barcode labels
9. Reports & profit (cost price), customer segments, abandoned-cart list
10. Blog, bundles/combos, back-in-stock notify, bKash PGW/merchant migration

---

## JS Budget — Honest Measurements (Playwright, `performance.getEntriesByType('resource')`, dev server **without gzip**; production gzip estimated)

| Page | JS Resources | Raw Transfer (bytes) | Est. gzipped | Budget (90KB gz) | Status |
|------|-------------|---------------------|--------------|------------------|--------|
| Home | 2 (alpine + module) | 80,493 | ~30 KB | 90 KB | ✅ PASS |
| Search | 2 (alpine + module) | 80,493 | ~30 KB | 90 KB | ✅ PASS |
| Categories | 2 (alpine + module) | 80,493 | ~30 KB | 90 KB | ✅ PASS |
| Static Pages | 0 | 0 | 0 | 90 KB | ✅ PASS |
| Shop | 3 (app + module + livewire) | 693,923 | ~129 KB | 90 KB | ❌ FAIL |
| Product | 3 (app + module + livewire) | 693,923 | ~129 KB | 90 KB | ❌ FAIL |
| Checkout | 3 (app + module + livewire) | 693,923 | ~129 KB | 90 KB | ❌ FAIL |

**Cheapest options to get under budget (not implemented):**
1. **Alpine-only variant picker** (~15 KB gz): Replace Livewire VariantPicker with Alpine component; saves ~129 KB on Product page. Cost: ~2 days dev.
2. **Lazy-load Livewire on interaction** (~50% JS reduction): Load Livewire bundle only when user clicks "Add to Cart". Cost: ~1 day dev.
3. **Remove Livewire from Shop** (~129 KB): Convert filters to Alpine + fetch API. Cost: ~2 days dev.

**Measurement method**: Playwright `performance.getEntriesByType('resource')` on `php artisan serve` (no gzip). Production gzip estimated via `gzip -c` on raw files. Real gzip requires Nginx/Apache with compression enabled.

---

## Shell Scripts — Validation

| Script | `bash -n` | `shellcheck` | Notes |
|--------|-----------|--------------|-------|
| `deploy.sh` | ✅ PASS | ⚠️ Not installed | Syntax OK; shellcheck not available in environment |
| `health-check.sh` | ✅ PASS | ⚠️ Not installed | Syntax OK; shellcheck not installed in environment |

**Scripts were NOT run against a server**; only syntax validation performed.

---

## Focus Trap — Implementation

| Component | Method | Verified |
|-----------|--------|----------|
| Cart Drawer | `x-trap.inert="true"` (Alpine.js `@alpinejs/focus`) | Manual test: Tab cycles within drawer, Escape closes |
| Checkout Modal | N/A (full page) | N/A |
| Product Gallery | N/A (no modal) | N/A |
| Size Chart Modal | ⚠️ Not implemented | ❌ Not done |

---

## Upload Limits & Session Config — Evidence

| Setting | Config | Test |
|---------|--------|------|
| Max upload size | `config/media-library.php`: `max_file_size = 5MB` | MediaLibraryTest: `test_product_rejects_large_files` ✅ |
| Allowed extensions | `config/media-library.php`: `allowed_extensions = ['jpg','jpeg','png','webp']` | MediaLibraryTest: `test_product_rejects_non_image_files` ✅ |
| Session lifetime | `config/session.php`: `lifetime = 120` (env `SESSION_LIFETIME`) | Config check ✅ |
| Admin idle timeout | `config/session.php`: `admin_idle_timeout = 30` (env `SESSION_ADMIN_IDLE_TIMEOUT`) | AdminIdleTimeout middleware registered ✅ |
| Secure cookies | `config/session.php`: `secure = true` (env `SESSION_SECURE_COOKIE`) | .env.example updated ✅ |

---

## Final Test Suite — Full Run

```
Tests:    191 passed (919 assertions)
Duration: ~48s
```

Random order stability (3 runs):
```
Run 1: 191 passed (919 assertions)
Run 2: 191 passed (919 assertions)
Run 3: 191 passed (919 assertions)
```

Query count tests (exact, stable):
- Category listing: **11** queries (target ≤15)
- Product detail: **8** queries (target ≤20)
- Home page: **11** queries (target ≤15)

Rate limit tests: 4 passed (checkout 5/10min, tracking 10/min, search 30/min, login 5/min)

Code style: `./vendor/bin/pint` — clean

---

## 10-Line Summary

**Phase 5 Polish & Hardening — COMPLETE (development only; go-live pending owner action)**

- Performance: Query counts exact (listing 11, product 8, home 11); Livewire JS conditionally loaded on 4/7 pages; Alpine single-init verified via Playwright (1 init/page).
- Security: CSP report-only, X-Frame-Options DENY, Referrer-Policy, Permissions-Policy, nosniff, HSTS (prod); rate limit tests (checkout 5/10m, tracking 10/m, search 30/m, login 5/m); MediaLibrary upload hardened (5MB, images only); rich-text sanitiser null-safe; admin idle timeout 30m; production config in .env.example.
- Accessibility: focus-visible, aria-live cart count, sr-only labels, focus trap on cart drawer (`x-trap`); all user strings in lang/en & lang/bn.
- Analytics: Meta Pixel events (PageView, ViewContent, AddToCart, InitiateCheckout, AddPaymentInfo, Lead at placement); Purchase server-side on admin approval (configurable approved/delivered); UTM capture middleware + order attachment.
- Ops: deploy.sh (zero-downtime, rollback), health-check.sh, STAGING.md; shell scripts syntax-validated; backup/restore notes.
- Tests: 191 passing (919 assertions), exact query counts (11/8/11), rate limit tests, 3× random-order stable; Pint clean.
- Go-live checklist: 7/25 items verified in dev (18 need production/real device, 12 need owner); Lighthouse never run; backup restore never tested; SSL/courier/legal need owner.
- Known limitations: Livewire JS over budget on 3 pages (Product/Shop/Checkout ~129KB gz est. vs 90KB target); bKash manual only; no SMS/CAPI/courier API; fonts missing; Lighthouse never run; backup restore never tested.
- Phase 6 backlog prioritized (SMS, Courier API, CAPI, bKash resend, Partial COD, Reviews, Order editing, CSV import, Reports, Blog).
- Deployment scripts syntax-validated; shellcheck not available; scripts NOT executed against server.