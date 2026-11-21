# 07 — Admin Panel (Filament 5)

> **Note:** Originally specified Filament 4, but upgraded to Filament 5 for Laravel 12 compatibility. The UI components, table/filters/actions APIs are largely compatible with minor syntax differences.

Panel path: `/admin`. Guard: `web` with `users` table. Mobile-friendly usage is a hard requirement (owner approves orders from a phone).

## 1. Roles & permissions (spatie/permission)

| Permission group | Owner | Manager | Staff |
|---|:-:|:-:|:-:|
| Dashboard | ✅ | ✅ | ✅ (limited) |
| Orders view/update status | ✅ | ✅ | ✅ |
| Approve/Reject orders | ✅ | ✅ | ✅ (setting-controlled) |
| **Verify/Reject bKash payment** (`payments.verify`) | ✅ | ✅ | ❌ default |
| Mark COD collected | ✅ | ✅ | ✅ |
| Products/Categories/Stock | ✅ | ✅ | stock only |
| Cost price & profit visibility | ✅ | ❌ | ❌ |
| Customers/Blacklist | ✅ | ✅ | view |
| Coupons/Banners/Pages | ✅ | ✅ | ❌ |
| Settings, Staff, Activity log | ✅ | ❌ | ❌ |
| Delete (soft) / Restore | ✅ | ❌ | ❌ |

Seed an owner account from `.env` (`ADMIN_EMAIL`, `ADMIN_PASSWORD`) — force password change on first login. Enable 2FA (Filament MFA) for owner.

## 2. Navigation
```
Dashboard
Orders            (badge: pending count)
  ├ All Orders
  ├ Pending Approval
  ├ bKash Verification  (badge)
  ├ To Pack / Processing
  ├ Shipped
  └ Returns / Failed
Catalog
  ├ Products
  ├ Categories
  ├ Colors
  ├ Size Charts
Customers
Marketing
  ├ Coupons
  ├ Banners
Content
  ├ Pages
Settings
  ├ Store Settings
  ├ Payment Settings
  ├ Delivery Zones & Districts
  ├ Blacklist
Users & Roles
Reports
Activity Log
```

## 3. Dashboard widgets
- Stat cards: **Pending approvals**, **bKash awaiting verification**, **Today's orders**, **Today's revenue (confirmed+)**, **Month revenue**.
- Line chart: orders & revenue last 30 days.
- Donut: payment method split.
- Table: latest 10 orders.
- Low-stock list (variants ≤ threshold).
- Alert: bKash orders older than 12 h unverified; COD delivered > 7 days not collected.

## 4. Orders resource (most important)

### List table columns
Order #, Date/time (relative), Customer (name + phone), District, Items count, **Total**, Payment (badge: COD / bKash + status), Status (badge), Courier/Consignment, Actions.
- Colour badges: pending=amber, confirmed=blue, processing=indigo, shipped=violet, delivered=green, cancelled=red, returned/failed=gray/orange.
- Filters: status, payment method, payment status, district/zone, date range, courier, customer blacklisted, source.
- Global search: order number, phone, name, TrxID, consignment ID.
- Default sort: newest first; default tab "Pending Approval".
- Row quick actions (mobile-friendly buttons): **Approve**, **Call** (`tel:`), **WhatsApp**, **View**.
- Bulk actions: Approve selected (COD only), Mark processing, Print invoices, Print packing slips, Export CSV (for courier bulk upload), Cancel selected (with reason).

### Order detail (View page)
Sections:
1. **Header:** order #, created time, status & payment badges, risk badge (customer history), action buttons.
2. **Customer & delivery:** name, phone (tap to call/WhatsApp), address, district/area, note; customer order stats (total/delivered/cancelled); "Blacklist" button.
3. **Items table:** image, name, variant, qty, price, line total; totals box.
4. **Payment card:** (see `06` §3) bKash verification card or COD info.
5. **Fulfilment:** courier select, consignment ID, tracking URL, shipped/delivered timestamps.
6. **Timeline:** `order_status_histories` + activity log entries.
7. **Admin notes:** internal textarea (append-only log with author).
8. **Print:** Invoice, Packing slip / courier slip.

### Header actions (state-aware)
| Current state | Actions shown |
|---|---|
| pending + COD | **Approve**, Cancel |
| pending + bKash awaiting | **Verify & Approve**, **Reject payment**, Cancel |
| confirmed | Move to Processing, Ship (needs courier+consignment), Cancel |
| processing | Ship, Cancel |
| shipped | Mark Delivered, Mark Failed, Mark Returned |
| delivered (COD unpaid) | **Mark COD collected**, Return |
| failed_delivery | Re-ship, Return (restock?), Cancel |
Every action opens a small confirm modal (reason/notes where relevant) and runs the Action class.

### Rules
- No direct editing of `status` via form fields; transitions only through actions → `OrderStatusService`.
- Orders cannot be hard-deleted.
- Show stock warning if approving an order whose items were since changed (shouldn't occur due to reservation, but log).

## 5. Products resource
- Form tabs: **General** (name, bn name, slug auto, category, price, compare-at, cost, short/long description, fabric, fit, care), **Images** (Spatie media multi-upload, drag reorder, alt text), **Variants** (repeater/relation manager: size, colour, SKU auto, stock, price override, active), **SEO** (title, description), **Display** (active, featured, new, size chart).
- Table: thumb, name, category, price, total stock (sum variants), active toggle, featured toggle.
- Actions: Duplicate product (copies variants with stock 0), Bulk activate/deactivate, **Quick stock editor** (inline table of variants → stock), Export/Import CSV (Phase 2).
- Variant generator: choose sizes[] × colours[] → creates variants.
- Warn on negative/low stock; prevent deleting products that have orders (soft delete only).

## 6. Categories
Tree (parent/child), image, sort order drag, active, show on home.

## 7. Customers
Table: name, phone, orders, delivered, cancelled, total spent, risk badge, blacklisted toggle. View page: order history, notes. Action: **Blacklist** (adds to `blacklists`, sets flag), **Unblacklist**.

## 8. Coupons, Banners, Pages
Standard CRUD with validation per `04`. Banners: preview desktop/mobile image, schedule. Pages: rich text editor (bn/en).

## 9. Settings pages
- **Store:** name, phone, WhatsApp, social links, address, announcement bar, logo/favicon.
- **Payments:** bKash number, instructions, verify hours, enable/disable methods.
- **Delivery:** zone charges, free-delivery threshold, district/area mapping.
- **Orders:** per-phone daily limit, min order amount, pause orders switch, who can approve.
- **Marketing:** Meta Pixel ID, CAPI token, Google Analytics ID, TikTok Pixel.
- **Invoice:** logo, footer note, terms text.

## 10. Printing
- **Invoice** (A4 or A5): logo, order #, date, customer, items, totals, payment method/status, store contact, footer note. Bangla-capable font. Render as Blade print view (`@media print`) → "Print" button; optional PDF via mPDF.
- **Packing/courier slip** (thermal-friendly ~80 mm and A5): order #, customer name/phone/address, **COD amount to collect (or "PAID")**, items list with size/colour, courier & consignment barcode (Code128 via `picqer/php-barcode-generator`, optional).
- Bulk print: multiple orders → one print page, page-break per slip.

## 11. Notifications (admin)
New order → Filament database notification + sound-less toast + nav badge. Optional email to owner (`MAIL_MAILER` in prod). Optional Telegram/WhatsApp admin alert (Phase 2).

## 12. Reports (Phase 2 but plan the queries now)
Sales by day/week/month, by category, by product (units & revenue), payment split, delivery success rate, cancellation reasons, coupon usage, customer repeat rate, profit (owner only, needs cost price).

## 13. Admin UX acceptance criteria
- Approve a COD order in ≤ 3 taps from the list on a 360 px screen.
- Verify a bKash order shows TrxID/sender/amount at the top without scrolling.
- Search by phone or TrxID returns in < 1 s.
- Every destructive action requires confirmation and records who/when/why.
