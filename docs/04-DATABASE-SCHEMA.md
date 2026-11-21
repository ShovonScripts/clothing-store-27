# 04 — Database Schema

Conventions: MySQL, `id` = `bigIncrements`, `timestamps()` on all tables, `softDeletes()` where noted (SD). Money columns = `unsignedInteger` (taka). Booleans = `tinyInteger(1)`. Enums stored as `string(32)` and cast to PHP backed enums. Charset `utf8mb4`.

## 1. Entity overview
```
users(admins/staff)
categories 1─∞ products 1─∞ product_variants
products 1─∞ media (spatie)         products ∞─∞ tags
size_charts 1─∞ products
customers 1─∞ orders 1─∞ order_items ─> product_variants
orders 1─1 payments (1─∞ allowed for future)
orders 1─∞ order_status_histories
orders 1─0..1 shipments
coupons 1─∞ orders ; coupon_usages
delivery_zones, districts ─ areas
banners, pages, settings, blacklists, activity_log
```

## 2. Tables

### users (staff/admin only)
`id, name, email unique, phone nullable, password, is_active default 1, last_login_at nullable, remember_token, timestamps`
Roles via spatie: `owner`, `manager`, `staff`.

### categories (SD)
`id, parent_id nullable FK→categories, name, name_bn nullable, slug unique, description nullable, image_path nullable, sort_order int default 0, is_active, show_on_home, meta_title nullable, meta_description nullable`
Index: `(parent_id, sort_order)`.

### products (SD)
`id, category_id FK, name, name_bn nullable, slug unique, sku nullable unique, short_description nullable(text), description nullable(longText), fabric nullable, fit nullable (slim|regular|oversized|relaxed), care nullable, price unsignedInt, compare_at_price unsignedInt nullable, cost_price unsignedInt nullable (admin only), is_active default 1, is_featured default 0, is_new default 0, size_chart_id nullable FK, meta_title nullable, meta_description nullable, sold_count unsignedInt default 0, views unsignedInt default 0, published_at nullable`
Indexes: `(is_active, category_id)`, `(is_featured)`, FULLTEXT `(name, name_bn, sku)`.
Media collections (spatie): `gallery` (ordered), conversions `thumb 400w`, `card 600w`, `large 1200w`, all WebP.

### colors
`id, name, name_bn nullable, hex(7) nullable, sort_order`
Seed: Black, White, Navy, Olive, Grey, Maroon, Sky Blue, Beige, Brown, Cream, Charcoal.

### product_variants (SD)
`id, product_id FK cascade, sku unique, size string(16) (e.g. M, L, XL, 32, 40, Free), color_id nullable FK, price unsignedInt nullable (override), stock int default 0, low_stock_threshold nullable, is_active default 1, sort_order`
Unique: `(product_id, size, color_id)`. Index: `(product_id, is_active)`.
Effective price = `variant.price ?? product.price`.
Optional: `variant_image_media_id nullable` to link a gallery image to a colour (show that image when colour selected).

### tags / product_tag (optional, phase 2)

### size_charts
`id, name (e.g. "T-Shirt / Polo (inches)"), table_json (columns + rows), note nullable`
Example `table_json`: `{"columns":["Size","Chest","Length","Shoulder"],"rows":[["M","38","27","17"],["L","40","28","18"]]}`

### customers (SD)
`id, name, phone(11) unique, email nullable, default_address nullable, district_id nullable, area nullable, total_orders unsignedInt default 0, delivered_orders default 0, cancelled_orders default 0, total_spent unsignedInt default 0, is_blacklisted default 0, blacklist_reason nullable, notes nullable`
Index: `phone` unique, `is_blacklisted`.

### districts
`id, name, name_bn, zone ENUM('inside_dhaka','outside_dhaka') — or delivery_zone_id FK, is_active, sort_order`
Seed all 64 districts. Dhaka district split: "Dhaka City" (inside_dhaka) and "Dhaka (outside city: Savar, Keraniganj, Narayanganj, Gazipur…)" — **owner decides**; default: Dhaka district = inside_dhaka for city areas via an `areas` list below.

### areas (optional but recommended for Dhaka)
`id, district_id FK, name, name_bn, is_inside_city default 0` — used to decide Inside-Dhaka charge for Dhaka-district addresses. Seed Dhaka thanas (Mirpur, Uttara, Dhanmondi, Mohammadpur, Gulshan, Badda, Jatrabari, Motijheel, etc.) as `is_inside_city=1`; Savar/Keraniganj/Gazipur etc. as 0.

### delivery_zones
`id, key unique ('inside_dhaka','outside_dhaka', ...), name, charge unsignedInt, free_over unsignedInt nullable, est_days_min, est_days_max, is_active`
Seed: inside_dhaka 70 (1–2 days), outside_dhaka 130 (2–5 days).

### orders (SD)
```
id
order_number string(20) unique          -- YYMMDD + 4-digit seq
customer_id FK
-- snapshot of delivery info (never changes if customer edits later)
customer_name, customer_phone(11), customer_email nullable
shipping_address text
district_id FK, area string nullable
delivery_zone_key string(32)
customer_note text nullable
-- money (taka ints)
subtotal unsignedInt
discount unsignedInt default 0
delivery_charge unsignedInt
total unsignedInt
coupon_id nullable FK, coupon_code string nullable
-- state
status string(32) default 'pending'          -- OrderStatus enum
payment_method string(32)                    -- cod | bkash_send_money
payment_status string(32)                    -- PaymentStatus enum
-- approval
approved_by nullable FK users, approved_at nullable
cancelled_by nullable FK users, cancelled_at nullable, cancel_reason nullable
-- logistics
courier string nullable, consignment_id string nullable, tracking_url nullable
shipped_at nullable, delivered_at nullable
cod_amount unsignedInt default 0           -- total if COD else 0
-- meta
ip_address string(45) nullable, user_agent text nullable
source string nullable                     -- utm_source / fb / tiktok / direct
admin_note text nullable
timestamps, deleted_at
```
Indexes: `(status, created_at)`, `(payment_status)`, `(customer_phone)`, `(created_at)`, `(customer_id)`.

### order_items
`id, order_id FK cascade, product_id FK, variant_id FK, product_name (snapshot), variant_label (e.g. "L / Black"), sku, unit_price unsignedInt, quantity unsignedSmallInt, line_total unsignedInt, image_url nullable (snapshot thumb)`

### payments
```
id, order_id FK
method string(32)                      -- cod | bkash_send_money
amount unsignedInt                     -- expected amount
status string(32)                      -- PaymentStatus
-- bKash manual fields
bkash_trx_id string(32) nullable UNIQUE   -- uppercase normalised
bkash_sender_number string(11) nullable
bkash_amount_sent unsignedInt nullable    -- what customer typed
bkash_receiver_number string(11) nullable -- snapshot of store number shown at checkout
bkash_proof_path string nullable          -- optional screenshot (phase 2)
verified_by nullable FK users, verified_at nullable
rejection_reason string nullable
collected_at nullable (COD)               -- when cash collected & recorded
meta json nullable
timestamps
```
Unique index on `bkash_trx_id` (MySQL allows multiple NULLs). Index `(status)`.

### order_status_histories
`id, order_id FK cascade, from_status nullable, to_status, note nullable, changed_by nullable FK users, created_at`
(also written for payment status changes with `type` column: `order|payment`.)

### shipments (optional now; needed for courier API phase)
`id, order_id FK, courier string, consignment_id, tracking_code nullable, status nullable, cod_amount, delivery_fee nullable, raw_response json nullable, booked_at nullable`
In MVP, the same info lives on `orders.courier/consignment_id` — keep both columns; sync later.

### coupons
`id, code unique (uppercase), type ('fixed'|'percent'), value unsignedInt, max_discount unsignedInt nullable, min_order unsignedInt default 0, starts_at nullable, expires_at nullable, usage_limit unsignedInt nullable, per_phone_limit unsignedInt default 1, used_count unsignedInt default 0, is_active`
### coupon_usages
`id, coupon_id, order_id, phone, created_at`

### banners
`id, title nullable, image_desktop path, image_mobile path, link_url nullable, position ('hero'|'strip'|'popup'), sort_order, starts_at nullable, ends_at nullable, is_active`

### pages
`id, slug unique, title, title_bn nullable, body longText, body_bn nullable, is_active, meta_title, meta_description`
Seed: about, contact, delivery-info, return-exchange, privacy-policy, terms, size-guide.

### settings (key/value)
`id, key unique, value text nullable, type ('string'|'int'|'bool'|'json'), group`
Seed keys:
| key | default |
|---|---|
| `store_name` | `{BRAND}` |
| `store_phone` | `01XXXXXXXXX` |
| `whatsapp_number` | `8801XXXXXXXXX` |
| `facebook_url`, `instagram_url`, `tiktok_url`, `messenger_url` | empty |
| `store_address` | empty |
| `bkash_number` | `01XXXXXXXXX` |
| `bkash_instructions_bn / _en` | see `14-COPY` |
| `bkash_verify_hours` | `24` |
| `cod_enabled` / `bkash_enabled` | `1` / `1` |
| `delivery_inside_dhaka` / `delivery_outside_dhaka` | `70` / `130` |
| `free_delivery_over` | `0` (off) |
| `low_stock_threshold` | `5` |
| `max_pending_orders_per_phone_per_day` | `3` |
| `min_order_amount` | `0` |
| `announcement_text` / `announcement_enabled` | |
| `meta_pixel_id` | |
| `invoice_footer_note` | |
| `maintenance_orders_paused` | `0` (pause order intake) |

### blacklists
`id, type ('phone'|'ip'|'address_keyword'), value, reason nullable, created_by, created_at` — unique `(type, value)`.

### activity_log (spatie)
Logs: order approve/reject, payment verify/reject, product price/stock edit, settings changes, status changes, coupon edits.

### jobs, failed_jobs, sessions, cache, password_reset_tokens — Laravel defaults.

## 3. Enums

```php
enum OrderStatus: string {
  case Pending='pending';             // placed, awaiting admin approval
  case Confirmed='confirmed';         // approved by admin (payment verified for bKash)
  case Processing='processing';       // packing
  case Shipped='shipped';             // handed to courier
  case Delivered='delivered';
  case Cancelled='cancelled';
  case FailedDelivery='failed_delivery';
  case Returned='returned';
}
enum PaymentMethod: string { case Cod='cod'; case BkashSendMoney='bkash_send_money'; }
enum PaymentStatus: string {
  case Unpaid='unpaid';                          // COD before delivery
  case AwaitingVerification='awaiting_verification'; // bKash typed, admin must check
  case Paid='paid';                              // bKash verified OR COD collected
  case Rejected='rejected';                      // bKash could not be verified
  case Refunded='refunded';
}
```

## 4. Allowed order transitions
```
pending        → confirmed | cancelled
confirmed      → processing | shipped | cancelled
processing     → shipped | cancelled
shipped        → delivered | failed_delivery | returned
failed_delivery→ shipped (re-attempt) | returned | cancelled
delivered      → returned (within return window)
cancelled, returned → (terminal)
```
Side effects: `cancelled` / `returned (restock=yes)` → release stock. `delivered` + COD → prompt "Mark COD collected". `confirmed` for bKash requires `payment_status = paid`.

## 5. Seeders
`SettingsSeeder`, `DistrictSeeder (64 districts + Dhaka areas)`, `DeliveryZoneSeeder`, `ColorSeeder`, `CategorySeeder`, `PageSeeder`, `RolesAndAdminSeeder` (owner user from env), `DemoProductSeeder` (≈ 24 products across categories with variants, using placeholder images), `DemoOrderSeeder` (dev only).

## 6. Integrity & performance notes
- All stock updates inside transaction with `lockForUpdate()` on variant rows.
- `orders.order_number` generation uses a DB sequence per day (`SELECT … FOR UPDATE` on a small `daily_counters` table, or retry on unique violation).
- Add `daily_counters(date unique, last_seq)` for order number sequence.
- Index search fields; paginate everywhere (`simplePaginate` on storefront lists).
