# Progress Tracker

## Phase 0: Bootstrap ✅ COMPLETED (2026-11-21)

### Tasks Completed:
- [x] Laravel 12 project created
- [x] PHP 8.2.12 (system) - compatible packages installed
- [x] Packages installed:
  - livewire/livewire ^4.4
  - filament/filament ^5.11
  - spatie/laravel-medialibrary ^11.23
  - spatie/laravel-permission ^6.25
  - spatie/laravel-activitylog ^4.12
  - spatie/laravel-sluggable ^3.8
  - mpdf/mpdf ^8.3
  - pestphp/pest ^3.8
  - pestphp/pest-plugin-laravel ^3.2
  - laravel/pint ^1.13
- [x] Tailwind CSS + Alpine.js via Vite configured
- [x] Fonts: Hind Siliguri (Bangla) + Inter (Latin) - @font-face ready
- [x] config/store.php created
- [x] SettingsService with typed getters
- [x] Settings migration + seeder with defaults
- [x] Enums: OrderStatus, PaymentStatus, PaymentMethod
- [x] Money helper (format, parse, math)
- [x] PhoneNormalizer (BD formats, Bengali digits)
- [x] Helper functions registered (settings(), money(), normalizePhone(), etc.)
- [x] CI-ready: Pint + Pest configured
- [x] DECISIONS.md created
- [x] BACKLOG.md created
- [x] PROGRESS.md created

### Test Results:
- `php artisan test`: ✅ 46 passed (106 assertions)
- `./vendor/bin/pint`: ✅ No issues (auto-fixed)
- `php artisan migrate:fresh --seed`: ✅ Succeeds

### Verified:
- [x] Home route renders layout
- [x] Vite manifest generated
- [x] Database migrations run
- [x] Settings seeded
- [x] Admin route redirects to login
- [x] Admin login page accessible
- [x] GitHub Actions workflow (Pint + Pest) configured
- [x] .env.example complete with APP_LOCALE=bn, APP_FALLBACK_LOCALE=en, APP_TIMEZONE=Asia/Dhaka
- [x] lang/bn and lang/en exist with validation strings
- [x] Admin user seeder uses ADMIN_EMAIL/ADMIN_PASSWORD from .env, refuses default password in production

### Known Limitations:
- Font files not yet downloaded (placeholders in public/fonts/) — **Phase 2 prerequisite**
- Filament admin panel resources not yet configured (Phase 1)
- No authentication scaffolding yet (Filament handles admin auth)

---

## Phase 1: Catalogue & Admin Basics ✅ COMPLETED (2026-11-21)

### Tasks:
- [x] Migrations: categories, products, colors, variants, size_charts, districts, areas, delivery_zones, pages, banners, permissions
- [x] Seeders: districts (64), zones, colors, categories, pages, demo products (~27 with variants)
- [x] Filament resources: Category (tree), Product (tabs, media, variants, Repeater), Color, SizeChart, Banner, Page, DeliveryZone, District
- [x] Settings page (Store, Payments, Delivery) using SettingsService
- [x] Roles/permissions: owner, manager, staff with policies

### Acceptance:
- Owner can add product with 3 sizes × 2 colours and images
- Conversions generated as WebP (thumb 400w, card 600w, large 1200w)
- Settings saved & cached
- Admin dashboard accessible by all roles
- Permissions enforced: staff cannot verify bKash, manager cannot view cost_price
- Admin routes accessible with proper permissions

---

## Phase 2: Storefront Browsing ⏳ PENDING

### Tasks:
- [ ] Layout: announcement bar, header, mobile nav, footer, language toggle, WhatsApp float
- [ ] Home sections (admin-driven)
- [ ] Category/shop listing with Livewire filters/sort/pagination
- [ ] Product page: gallery, variant picker, size chart modal, stock hints, delivery box, sticky mobile CTA, related products
- [ ] Search + suggestions; static pages; SEO tags + JSON-LD + sitemap/robots

### Acceptance:
- Lighthouse mobile ≥ 85 on home & PDP
- Works at 360px
- Filters sync to URL

---

## Phase 3: Cart, Checkout, Orders ⏳ PENDING

### Tasks:
- [ ] CartService, CartDrawer, CartPage with re-validation
- [ ] PricingService, DeliveryChargeService, CouponService
- [ ] Orders migrations + models
- [ ] Checkout Livewire: COD + bKash panels, validation
- [ ] PlaceOrderAction (transactional, stock reserve, idempotent)
- [ ] Success page + tracking page
- [ ] Anti-abuse: honeypot, throttle, per-phone limit, blacklist

### Acceptance:
- Pest tests: COD order, bKash order, duplicate TrxID, insufficient stock, price tamper, blacklist, double submit

---

## Phase 4: Admin Order Management ⏳ PENDING

### Tasks:
- [ ] OrderResource with tabs/filters/search
- [ ] Actions: Approve, Verify & Approve (bKash), Reject, Cancel, Processing, Ship, Delivered, Failed, Returned, Mark COD collected
- [ ] Stock release/restock logic
- [ ] Dashboard widgets, nav badges
- [ ] Customers resource + blacklist
- [ ] Invoice + packing slip (Bangla)
- [ ] Scheduled command: expire unverified bKash

### Acceptance:
- Full lifecycle demo (COD + bKash)
- Permissions enforced (staff cannot verify bKash)
- Activity log records actions
- 360px usable

---

## Phase 5: Polish & Hardening ⏳ PENDING

### Tasks:
- [ ] Security headers, CSP, rate limits
- [ ] Performance: caching, indexes, N+1 checks
- [ ] Accessibility pass
- [ ] Meta Pixel events + UTM capture
- [ ] Backups, health checks, deploy script
- [ ] Go-live checklist

### Acceptance:
- All checklists ticked
- Production smoke test passed

---

## Final Deliverable

- [ ] FINAL-REPORT.md with go-live checklist pass/fail
- [ ] Manual steps for owner
- [ ] Known limitations
- [ ] Phase 6 backlog