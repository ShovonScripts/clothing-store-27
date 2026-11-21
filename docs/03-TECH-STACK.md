# 03 — Tech Stack Decision & Project Structure

## 1. Decision

**Laravel 12 + Blade + Livewire 3 + Alpine.js + Tailwind CSS + Filament 4 (admin) + MySQL.**

### Why this wins for this project
| Requirement | Why this stack fits |
|---|---|
| Fast on cheap phones | Server-rendered HTML; tiny JS (Alpine ≈ 15 KB, Livewire only where needed) |
| SEO for product/category pages | Real HTML on first load, no hydration issues |
| Solo/small team | One codebase, one language, fewer deployment pieces |
| Admin approval workflow | Filament gives tables, filters, actions, bulk actions, roles, dashboards fast |
| Cheap hosting | Runs on a small VPS or decent shared hosting with PHP 8.3 |
| Cart/checkout interactivity | Livewire components give reactive UI without a SPA |

### Rejected alternatives
- **Next.js/React storefront + Laravel API:** two apps, two deploys, API auth, SEO/hydration complexity, heavier on low-end devices. Overkill for MVP.
- **Vue + Inertia:** good, but bigger JS bundle and little benefit here.
- **Pure Blade + vanilla JS only:** doable, but cart/filter UX is more code than Livewire.
- **WooCommerce/Shopify:** owner wants custom bKash-manual-approval flow and full control.

## 2. Packages

**Required**
- `laravel/framework ^12`
- `livewire/livewire ^3`
- `filament/filament ^4` (admin only, at `/admin`)
- `spatie/laravel-medialibrary` (images + conversions: thumb 400w, card 600w, large 1200w, WebP)
- `spatie/laravel-permission` (roles: owner, manager, staff) — or Filament Shield
- `spatie/laravel-activitylog` (who approved/rejected what)
- `spatie/laravel-sluggable`
- `intervention/image` (via Media Library driver) — GD or Imagick
- `mpdf/mpdf` (**invoice PDF with Bangla text** — dompdf has poor Bangla shaping; use mPDF with Noto Sans Bengali / Kalpurush, or generate printable HTML + browser print)
- `pestphp/pest` + `pest-plugin-laravel` + `pest-plugin-livewire`
- `laravel/pint`

**Optional / later**
- `spatie/laravel-sitemap`, `spatie/laravel-backup`, `spatie/laravel-health`, `laravel/horizon` (only if Redis), `league/csv`, `barryvdh/laravel-debugbar` (dev)

**Frontend**
- Tailwind CSS 4 (or 3.4) via Vite, `alpinejs` (+ `@alpinejs/collapse`, `@alpinejs/focus`), no other JS libs.
- Fonts: **Hind Siliguri** or **Noto Sans Bengali** (Bangla) + a clean sans (Inter / Plus Jakarta Sans) for Latin. Self-host, `font-display: swap`, subset where possible.
- Icons: inline SVG (Lucide/Heroicons) — no icon font.

## 3. Environment
PHP 8.3, MySQL 8 (or MariaDB 10.6+), Node 20+, Composer 2. Extensions: `intl, mbstring, gd or imagick, pdo_mysql, bcmath, fileinfo, zip, exif`.

`.env` essentials:
```
APP_NAME="{BRAND}"
APP_URL=https://example.com
APP_LOCALE=bn
APP_FALLBACK_LOCALE=en
APP_TIMEZONE=Asia/Dhaka
DB_CONNECTION=mysql
QUEUE_CONNECTION=database
CACHE_STORE=database   # or file; redis later
SESSION_DRIVER=database
FILESYSTEM_DISK=public
MAIL_MAILER=log        # email optional in MVP
META_PIXEL_ID=
META_CAPI_TOKEN=
```

## 4. Folder structure (target)
```
app/
  Actions/Orders/        PlaceOrderAction, ApproveOrderAction, RejectOrderAction, CancelOrderAction
  Actions/Payments/      VerifyBkashPaymentAction, RejectBkashPaymentAction, MarkCodCollectedAction
  Enums/                 OrderStatus, PaymentStatus, PaymentMethod
  Services/              CartService, StockService, PricingService, OrderStatusService,
                         PhoneNormalizer, DeliveryChargeService, CouponService, SettingsService
  Models/
  Livewire/Storefront/   ProductFilters, VariantPicker, CartDrawer, CartPage, Checkout, TrackOrder, Search
  Http/Controllers/Storefront/  HomeController, CategoryController, ProductController, PageController, OrderController
  Http/Middleware/       SetLocale, BlockBlacklisted
  Filament/Resources/    OrderResource, ProductResource, CategoryResource, CustomerResource, CouponResource, ...
  Filament/Pages/        Settings, Dashboard
  Filament/Widgets/
  Jobs/                  SendOrderSms, SendMetaCapiEvent, ConvertImages
  Console/Commands/      ExpireUnverifiedBkashOrders, ReleaseStaleReservations
  Support/Money.php      format(int $taka): string
config/store.php         brand name, currency symbol, thresholds
database/migrations, factories, seeders
resources/views/
  layouts/ components/ storefront/(home, category, product, cart, checkout, order-success, track, pages)
  invoices/ (pdf/print templates)
lang/bn, lang/en
tests/Feature, tests/Unit
docs/                    (this documentation pack)
```

## 5. Key architectural choices
1. **Cart storage:** session-backed (guest) cart as array of `variant_id => qty` in `CartService`; no DB cart in MVP. Re-validate against DB on every render (price, stock, active).
2. **Order placement:** single `PlaceOrderAction` inside `DB::transaction`: lock variant rows → validate stock → compute totals → create customer (by phone) → create order/items → create `payments` row → reserve stock → dispatch events. Idempotency: a hidden `checkout_token` stored in session prevents double submit.
3. **Order numbers:** `YYMMDD` + 4-digit daily sequence, e.g. `2611210007`; display as `#2611210007`. Unique index.
4. **Settings:** key/value table cached (`SettingsService`) with typed getters. Edited in Filament `Settings` page.
5. **Localisation:** `SetLocale` middleware using session/cookie; `bn` default. Product text fields store `name`, `name_bn` (nullable); fall back gracefully.
6. **Images:** upload original ≤ 5 MB; auto-convert to WebP; `loading="lazy"`, explicit width/height to avoid CLS; first product image preloaded.
7. **Authorisation:** Filament panel guarded by role; policies on all admin models; storefront has no customer login in MVP.
8. **Events → listeners (queued):** `OrderPlaced` (admin notification, SMS later, Pixel CAPI), `OrderApproved`, `OrderShipped`, `OrderCancelled` (restock).
9. **Soft deletes** on products, categories, orders, customers. No hard deletes of orders.

## 6. Testing strategy
- Feature tests for: place COD order, place bKash order (valid/invalid/duplicate TrxID), stock reservation/release, approve/reject, status transitions, delivery charge calc, coupon rules, tracking lookup, blacklist.
- Unit tests: PhoneNormalizer, PricingService, Money.
- Livewire tests: Checkout component validation, VariantPicker stock states.
- CI: GitHub Actions running Pint + Pest on push.
