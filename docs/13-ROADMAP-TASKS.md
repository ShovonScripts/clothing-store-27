# 13 — Roadmap, Tasks & Agent Prompts

Work strictly in phases. Each phase ends with a working, tested, demoable state. Tick boxes as you go. Read `AGENTS.md` first.

---
## PHASE 0 — Project bootstrap
- [ ] `composer create-project laravel/laravel`, set PHP 8.3, locale `bn`, timezone `Asia/Dhaka`
- [ ] Install: livewire, filament, spatie (medialibrary, permission, activitylog, sluggable), pest, pint, mpdf
- [ ] Tailwind + Alpine via Vite; fonts self-hosted; base layout with header/footer placeholders
- [ ] `config/store.php`, `SettingsService`, `settings` migration + seeder
- [ ] Enums, `Money` helper, `PhoneNormalizer` (+ unit tests)
- [ ] CI (Pint + Pest), `.env.example`, README, `DECISIONS.md`, `BACKLOG.md`
**Accept:** `php artisan test` green; home route renders layout; `/admin` login works with seeded owner.

## PHASE 1 — Catalogue & admin basics
- [ ] Migrations/models per `04` (categories, products, colors, variants, size_charts, districts, areas, delivery_zones, pages, banners)
- [ ] Seeders (districts, zones, colors, categories, pages, demo products with variants/images)
- [ ] Filament resources: Category (tree), Product (tabs, media, variant manager, quick stock), Color, SizeChart, Banner, Page
- [ ] Settings pages (Store, Payments, Delivery)
- [ ] Roles/permissions seeded (owner/manager/staff)
**Accept:** owner can add a product with 3 sizes × 2 colours and images; conversions generated as WebP; settings saved & cached.

## PHASE 2 — Storefront browsing
- [ ] Layout: announcement bar, header, mobile nav, footer, language toggle, floating WhatsApp
- [ ] Home sections (admin-driven)
- [ ] Category/shop listing with Livewire filters/sort/pagination
- [ ] Product page: gallery, variant picker, size chart modal, stock hints, delivery box, sticky mobile CTA, related products
- [ ] Search + suggestions; static pages; SEO tags + JSON-LD + sitemap/robots
**Accept:** Lighthouse mobile ≥ 85 on home & PDP (seeded data); works at 360 px; filters sync to URL.

## PHASE 3 — Cart & Checkout & Orders (core)
- [ ] `CartService` (session) + `CartDrawer` + `CartPage` with re-validation
- [ ] `PricingService`, `DeliveryChargeService`, `CouponService`
- [ ] Orders/order_items/payments/histories/customers/blacklists/coupons migrations + models
- [ ] `Checkout` Livewire: fields, live delivery charge, COD & bKash panels, validation (BdMobile, BkashTrxId unique)
- [ ] `PlaceOrderAction` (transactional, stock reserve, idempotent) + order number service
- [ ] Success page + tracking page
- [ ] Anti-abuse: honeypot, throttle, per-phone limit, blacklist
**Accept:** Pest tests: COD order, bKash order, duplicate TrxID, insufficient stock, price tamper attempt, blacklist, double submit.

## PHASE 4 — Admin order management (core)
- [ ] `OrderResource`: tabs/filters/search/badges, view page with all sections
- [ ] Actions: Approve, Verify & Approve (bKash), Reject payment, Cancel, Processing, Ship, Delivered, Failed, Returned, Mark COD collected — all via Action classes + `OrderStatusService`
- [ ] Stock release/restock logic; history timeline; admin notes
- [ ] Dashboard widgets; nav badges; new-order notification
- [ ] Customers resource + blacklist actions
- [ ] Print: invoice + packing slip (Bangla font tested), bulk print, CSV export for courier
- [ ] Scheduled `orders:expire-unverified-bkash`
**Accept:** full lifecycle demo (COD + bKash) via UI; permissions enforced (staff cannot verify bKash); activity log records actions; 360 px usable.

## PHASE 5 — Polish & hardening
- [ ] Coupons & banners scheduling UI polish; low-stock alerts
- [ ] Security headers/CSP, rate limits review, Turnstile (optional), image upload hardening
- [ ] Performance pass (caching, indexes, N+1 checks, image sizes)
- [ ] Accessibility pass; Bangla copy review (`14-COPY`)
- [ ] Meta Pixel browser events + UTM capture
- [ ] Backups, health checks, deployment script, staging dry-run
- [ ] Complete go-live checklist (`12`)
**Accept:** All checklists ticked; seed removed; production smoke test passed.

## PHASE 6 — Post-launch (backlog, in priority order)
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
## Ready-to-paste prompts for the coding agent

### Prompt A — kick-off
> Read `AGENTS.md`, `00-README.md`, `01-PRODUCT-REQUIREMENTS.md`, `03-TECH-STACK.md`, `04-DATABASE-SCHEMA.md`. Execute **Phase 0** from `13-ROADMAP-TASKS.md`. Create `DECISIONS.md` and `BACKLOG.md`. Stop when Phase 0 acceptance criteria pass and summarise what exists.

### Prompt B — catalogue
> Execute **Phase 1** following `04-DATABASE-SCHEMA.md` and `07-ADMIN-PANEL.md` (Products/Categories sections). Seed ~24 realistic men's fashion demo products (T-shirt, polo, shirt, panjabi, jeans, pants, hoodie, jacket, accessories) with sizes/colours/stock and placeholder images. Add Pest tests for variant pricing and stock display.

### Prompt C — storefront
> Execute **Phase 2** following `05-USER-FLOWS-AND-FEATURES.md` and `09-UI-UX-DESIGN.md`. Mobile-first at 360 px, Bangla default with English toggle, performance budget from `11`. Provide screenshots or describe layout decisions in `DECISIONS.md`.

### Prompt D — checkout & orders
> Execute **Phase 3** following `05`, `06`, `08`. Only COD and bKash Send Money (manual TrxID). All totals server-side, stock reserved transactionally, TrxID unique. Write the Pest tests listed in `06` §11 and in Phase 3 acceptance.

### Prompt E — admin order management
> Execute **Phase 4** following `07` and `06` §3–4. Order status changes only via `OrderStatusService`/Action classes. Staff role must not be able to verify bKash payments. Implement invoice and packing-slip print views with Bangla support.

### Prompt F — hardening & launch
> Execute **Phase 5**: follow `11` and `12`. Produce a final report listing every go-live checklist item with pass/fail and any manual steps for the owner.

---
## Test matrix (summary)
| Area | Cases |
|---|---|
| Phone | `+8801712345678`, `8801712345678`, `০১৭১২৩৪৫৬৭৮`, spaces/dashes, invalid prefix |
| Checkout | empty cart, sold-out variant, qty > stock, price tamper, coupon expired/min order/per-phone, orders paused |
| bKash | valid; duplicate TrxID (case/space variants); amount < total; bad sender number; missing fields |
| COD | place → approve → ship → deliver → collect; cancel releases stock |
| Admin | permissions per role; status transition guard (invalid jumps rejected); history/activity recorded |
| Expiry | unverified bKash > N h cancelled; verified/COD untouched |
| Delivery | Dhaka inside/outside areas; free-delivery threshold |
| Tracking | wrong phone rejected; throttling |
| Security | CSRF, authorisation on admin routes, blacklisted phone blocked |
