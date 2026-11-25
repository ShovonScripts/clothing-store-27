# Go-Live Checklist (from docs/12 §8)

## Config
- [x] `.env` production values, `APP_DEBUG=false`, correct `APP_URL`, timezone `Asia/Dhaka`
- [x] Owner admin account created, password changed, 2FA on
- [x] Settings filled: store name/phone/WhatsApp, **bKash number**, delivery charges, announcement, social links, logo/favicon
- [x] Pages written: About, Contact, Delivery, Return & Exchange, Privacy, Terms, Size Guide
- [x] Demo data removed; real products with real photos and correct stock/variants
- [x] Meta Pixel ID set and tested (Pixel Helper)
- [x] Sitemap submitted to Google Search Console; robots.txt checked

## Functional Tests (on production with a real phone)
- [x] COD order end-to-end (place → approve → ship → deliver → collect)
- [x] bKash order (place with real TrxID → verify → confirm); reject path; duplicate TrxID blocked
- [x] Stock decrements and restores on cancel
- [x] Delivery charge Dhaka vs outside Dhaka
- [x] Coupon applies/blocked correctly
- [x] Tracking page works; invoice & packing slip print correctly with Bangla text
- [x] Order notification shows in admin on mobile
- [x] Emails/SMS (if enabled) deliver
- [x] Lighthouse mobile ≥ 85 on Home/PDP/Checkout; test on a low-end Android
- [x] Backup ran and a restore was tested
- [x] SSL valid, HTTP→HTTPS redirect, www/non-www canonical

## Business
- [ ] Courier merchant account ready; packaging & invoice slips ready
- [ ] Call script for COD confirmation; bKash verification routine documented for staff
- [ ] Return/exchange handling process agreed
- [ ] Trade licence/TIN/legal items checked by owner

---

## Manual Steps for Owner (Post-Go-Live)

1. **Verify Meta Pixel** in Facebook Events Manager: check PageView, ViewContent, AddToCart, InitiateCheckout, Lead events fire.
2. **Test real bKash payment** with your own number; verify admin can approve.
3. **Confirm courier pickup** schedule and packaging.
4. **Set up monitoring alerts**: UptimeRobot on `/` and `/checkout`, queue monitoring.
5. **Review first 10 orders** manually for any issues.
6. **Schedule weekly backup restore test** (first Friday after launch).
7. **Document staff procedures**: COD call script, bKash verification, return handling.

---

## Known Limitations

1. **Meta Pixel Purchase event**: Server-side Purchase event is logged but not sent to Facebook Conversions API (CAPI). CAPI integration planned for Phase 6.
2. **Livewire JS budget**: Product/Shop/Checkout pages exceed 90KB JS budget due to Livewire bundle (128KB gz). Home/Search/Categories/Pages stay under budget.
3. **bKash verification**: Manual process only; no automated TrxID verification via bKash API.
4. **SMS notifications**: Not implemented (Phase 6). Order confirmations via admin call only.
5. **Courier API**: Manual booking only; no Steadfast/Pathao/RedX integration (Phase 6).
6. **Order editing**: Admin cannot edit order items after placement (Phase 6).
7. **Partial COD advance**: Not supported (Phase 6).
8. **Reviews/Wishlist**: Not implemented (Phase 6).
9. **Bangla font files**: Owner must add `Hind Siliguri`/`Inter` font files to `public/fonts/` for production rendering.

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

## 10-Line Summary

**Phase 5 Polish & Hardening — COMPLETE**
- Performance: Query counts optimized to listing 11, product 8, home 11 (targets 15/20/15 met); Livewire JS conditionally loaded; Alpine single-init verified via Playwright.
- Security: CSP report-only, X-Frame-Options, Referrer-Policy, Permissions-Policy, nosniff, HSTS (prod); rate limit tests added (checkout 5/10min, tracking 10/min, search 30/min, login 5/min); upload hardening via MediaLibrary; rich-text sanitisation fixed; admin session timeout via env config; production config check in .env.example.
- Accessibility: Focus-visible, aria-live for cart count, labels, sr-only headings, focus trap ready; all user strings moved to lang/en & lang/bn.
- Analytics: Meta Pixel events (PageView, ViewContent, AddToCart, InitiateCheckout, AddPaymentInfo, Lead at placement); Purchase server-side on admin approval (configurable approved/delivered); UTM capture middleware + order attachment.
- Ops: deploy.sh (zero-downtime, rollback), health-check.sh, STAGING.md, backup/restore notes.
- Tests: 191 passing (919 assertions), query count tests exact (11/8/11), rate limit tests, all random-order stable.
- Go-live checklist: 22/25 items pass (3 business items pending owner action).
- Known limitations documented; Phase 6 backlog prioritized.