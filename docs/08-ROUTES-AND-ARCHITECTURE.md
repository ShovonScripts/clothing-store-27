# 08 — Routes, Components, Services, Jobs

## 1. Web routes (`routes/web.php`)

| Method | URI | Handler | Name | Notes |
|---|---|---|---|---|
| GET | `/` | `HomeController@index` | `home` | cached sections (5 min) |
| GET | `/shop` | `CategoryController@shop` | `shop` | Livewire `ProductFilters` |
| GET | `/category/{category:slug}` | `CategoryController@show` | `category.show` | includes child categories |
| GET | `/product/{product:slug}` | `ProductController@show` | `product.show` | only `is_active` |
| GET | `/search` | `SearchController@index` | `search` | `?q=` |
| GET | `/cart` | `CartController@index` | `cart` | Livewire `CartPage` |
| GET | `/checkout` | `CheckoutController@index` | `checkout` | redirect to cart if empty; Livewire `Checkout` |
| GET | `/order/success/{order_number}` | `OrderController@success` | `order.success` | guarded by session flag or signed URL |
| GET/POST | `/track-order` | `OrderController@track` | `order.track` | throttle 10/min |
| GET | `/page/{page:slug}` | `PageController@show` | `page.show` | |
| GET | `/lang/{locale}` | `LocaleController@switch` | `lang` | `bn|en` |
| GET | `/sitemap.xml` | `SitemapController` | | cached |
| GET | `/robots.txt` | static/route | | disallow `/admin`, `/cart`, `/checkout`, `/track-order` |

Livewire handles cart add/remove/update via component actions (no custom JSON API in MVP).

Optional tiny JSON endpoints (rate-limited) if needed: `POST /api/cart/add`, `GET /api/search-suggest?q=`, `GET /api/delivery-charge?district_id=&area_id=`.

## 2. Middleware
- `SetLocale` — session/cookie → `app()->setLocale()`.
- `BlockBlacklisted` — on checkout POST/Livewire place-order: checks phone/IP blacklist (also re-checked inside action).
- `throttle:checkout` named limiter: 5 submits / 10 min / IP.
- `SecurityHeaders` — CSP (report-only first), `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`.
- `TrustProxies` configured for Cloudflare.

## 3. Livewire components (`app/Livewire/Storefront`)
| Component | Responsibility |
|---|---|
| `ProductFilters` | listing, filters, sort, pagination, query-string sync |
| `VariantPicker` | colour/size selection, stock state, qty, add-to-cart/buy-now |
| `CartDrawer` | mini cart, updates via events (`cart-updated`) |
| `CartPage` | full cart, coupon, qty updates |
| `Checkout` | form, delivery calc, payment panel, validation, submit → `PlaceOrderAction` |
| `SearchBox` | debounced suggestions |
| `TrackOrder` | lookup form + result timeline |

## 4. Services & actions

```php
// Services
CartService         add(variantId, qty), update, remove, clear, lines(): Collection, validate()
PricingService      effectivePrice(variant), totals(lines, coupon, zone): TotalsDto
DeliveryChargeService  forAddress(districtId, ?areaId, subtotal): DeliveryQuote
CouponService       validate(code, subtotal, phone): CouponResult
StockService        reserve(lines), release(order), adjust(variant, delta, reason)
OrderStatusService  transition(Order, OrderStatus $to, ?User $by, ?string $note)
PhoneNormalizer     normalize(string): ?string  // handles +88, 88, spaces, Bangla digits
SettingsService     get(key, default), set(key, value), all() (cached)
BlacklistService    isBlocked(phone, ip, address)
OrderNumberService  next(): string

// Actions (each in DB::transaction, each logs activity)
PlaceOrderAction(CheckoutData): Order
ApproveOrderAction(Order, User)                 // COD approve → confirmed
VerifyBkashPaymentAction(Order, User)           // paid + confirmed
RejectBkashPaymentAction(Order, User, reason)   // cancelled + release stock
CancelOrderAction(Order, User, reason)
ShipOrderAction(Order, User, courier, consignmentId)
MarkDeliveredAction(Order, User)
MarkCodCollectedAction(Order, User)
ReturnOrderAction(Order, User, restock: bool)
```

`PlaceOrderAction` sequence:
1. Re-normalise phone; check blacklist + daily pending limit; orders-paused flag.
2. Load cart lines; `lockForUpdate` variants; verify active & stock ≥ qty.
3. Compute totals (server-side) incl. coupon + delivery.
4. If bKash: validate TrxID uniqueness, amount ≥ total.
5. `Customer::firstOrCreate` by phone; update latest name/address.
6. Create `Order` (+ items snapshot), `Payment`.
7. `StockService::reserve`.
8. Create first `order_status_histories` row.
9. Clear cart; dispatch `OrderPlaced` after commit.
10. Return order.

## 5. Events / Listeners / Jobs
| Event | Listeners (queued) |
|---|---|
| `OrderPlaced` | `NotifyAdmins` (Filament DB notification), `SendOrderPlacedSms` (Phase 2), `SendMetaCapiEvent` (Phase 2) |
| `OrderApproved` | `SendOrderConfirmedSms` |
| `OrderShipped` | `SendShippedSms` (with courier + tracking) |
| `OrderCancelled` | `ReleaseStockIfNeeded` (idempotent), `SendCancelledSms` |

Scheduled (`routes/console.php`):
```php
Schedule::command('orders:expire-unverified-bkash')->everyTenMinutes();
Schedule::command('orders:flag-stale-cod')->dailyAt('09:00');   // delivered but not collected
Schedule::command('model:prune')->daily();
Schedule::command('sitemap:generate')->daily();
Schedule::command('backup:run')->dailyAt('03:00');   // if spatie/backup
```
Cron: `* * * * * php /var/www/app/artisan schedule:run`. Queue worker via Supervisor/systemd: `php artisan queue:work --tries=3 --max-time=3600`.

## 6. Policies & Gates
`OrderPolicy`, `ProductPolicy`, etc. Custom gate/permission `payments.verify`, `orders.approve`, `orders.cancel`, `settings.manage`, `costs.view`.

## 7. DTOs / Validation
- `CheckoutData` (readonly class) built from Livewire form.
- Validation rules centralised in `CheckoutRules` to be shared by Livewire and tests.
- Phone rule: custom `BdMobile` rule (`/^01[3-9]\d{8}$/` after normalisation).
- TrxID rule: `BkashTrxId` rule (`/^[A-Z0-9]{8,12}$/` after uppercase/trim) + `unique:payments,bkash_trx_id`.

## 8. Caching
- Home sections, category tree, settings → cache 5–30 min; bust on admin save (model observers).
- `Cache::remember` on menu categories; HTTP cache headers for static assets (1 year, hashed filenames).
- No full-page cache on cart/checkout.

## 9. Logging & monitoring
Laravel log daily channel; activity log for business actions; `spatie/laravel-health` checks (DB, queue heartbeat, disk) (Phase 2); Sentry/Flare optional.

## 10. Analytics events (client + server)
Meta Pixel: `PageView`, `ViewContent`, `AddToCart`, `InitiateCheckout`, `AddPaymentInfo` (when bKash TrxID entered or COD chosen), `Purchase` (on order placed — note orders are unapproved; consider sending `Purchase` only on admin approval via CAPI to avoid polluting ad optimisation with fake orders — **recommended: browser `Lead`/`InitiateCheckout` at placement, server `Purchase` on approval**).
