# 01 — Product Requirements

## 1. Vision
A clean, trustworthy, fast online store for men's clothing in Bangladesh that a **solo owner/small team can run from a phone**: add products, approve orders, hand parcels to a courier.

## 2. Goals
1. Customer can browse and order in **< 2 minutes on a phone**, without creating an account.
2. Owner can **approve/reject orders and verify bKash TrxIDs in a few taps**.
3. Lower fake/failed orders (a major BD problem) through approval, phone confirmation, duplicate-TrxID checks, and blacklist.
4. Pages load fast on 3G/4G and rank on Google for category/product searches.

## 3. Non-goals (MVP)
Online card/gateway payments, marketplace/multi-vendor, mobile app, loyalty points, multi-warehouse, multi-currency, product reviews with photos, live chat (use WhatsApp/Messenger links instead).

## 4. Personas
| Persona | Description | Needs |
|---|---|---|
| **Rafi, 22, student/early job, Dhaka/Chattogram** | Finds ads on Facebook/TikTok, Android phone, mobile data | Fast pages, clear price, size help, COD, trust signals |
| **Mahin, 30, outside Dhaka (district town)** | Orders via Facebook shop usually | COD or bKash, delivery charge clarity, confirmation call, return policy |
| **Owner/Admin** | Runs shop, packs orders | Simple order queue, bKash verification, printable invoices, stock accuracy |
| **Staff (packer/CS)** | Calls customers, packs | Limited admin role: confirm orders, update status, no price/settings access |

## 5. Product catalogue model
Men's fashion. Initial categories (editable): **T-Shirts, Polo Shirts, Shirts (Casual/Formal), Panjabi, Fatua, Pants & Trousers, Jeans, Hoodies & Sweatshirts, Jackets & Winter, Innerwear & Basics, Accessories (belt, wallet, cap, socks), Footwear (optional)**.

- A product has: name (bn/en optional), slug, category, description, fabric/material, fit, care, base price, compare-at (old) price, brand tag, images (multiple, ordered), size chart (per category or per product), tags, SEO fields, active/featured flags.
- **Variants:** Size × Colour. Each variant has SKU, stock, optional price override. Sizes: `S, M, L, XL, XXL, 3XL` for tops; numeric `28–40` for bottoms; `Free` for accessories. Panjabi sizes often `38, 40, 42, 44` (chest) — support free-text size labels.
- Stock tracked per variant. Show "Only 3 left" when stock ≤ threshold (setting, default 5). Sold-out variants are visible but disabled.

## 6. Core business rules
1. **Order creation** snapshots prices; totals computed server-side: `subtotal − discount + delivery_charge = total`.
2. **Stock is reserved at order placement** (decremented). If order is cancelled/rejected/auto-expired, stock is restored.
3. **Statuses** (see `05`): `pending → confirmed → processing → shipped → delivered`, plus `cancelled`, `returned`, `failed_delivery`.
4. **bKash orders:** `payment_status = awaiting_verification` until admin clicks **Verify & Approve** (→ `paid`) or **Reject payment** (→ order `cancelled`, stock released, optional SMS/WhatsApp message to customer to resend).
5. **COD orders:** `payment_status = unpaid` until courier delivers and admin marks **COD collected** (→ `paid`).
6. **Unverified bKash orders auto-expire** after `bkash_verify_hours` (default 24h) → cancelled, stock released (scheduled command).
7. **Coupons:** fixed or percent, min order, expiry, usage limit, per-phone limit. One coupon per order.
8. **Delivery charge:** by zone (Inside Dhaka / Outside Dhaka by default; per-district optional), editable; optional free-delivery threshold.
9. **Minimum order:** none by default (setting available).
10. **Duplicate/spam control:** max N pending orders per phone per day (default 3), honeypot + rate limit, blacklist by phone/IP/address keyword.
11. **Customer identity = phone number.** Customer record auto-created/linked by normalised phone on order.
12. **Return/exchange:** handled manually by admin: mark order `returned`, choose restock yes/no per item.

## 7. MVP feature list (Phase 1–3)

### Storefront
- Home (announcement bar, hero banners, category tiles, New Arrivals, Best Sellers, trust strip, Facebook/WhatsApp links)
- Category / Collection listing with filters (size, colour, price range, sort) — Livewire, URL-synced
- Product page: gallery, size/colour picker, stock hint, size chart modal, delivery info, COD/bKash badges, related products, WhatsApp "Ask about this product" button, sticky Add-to-cart/Buy-Now bar on mobile
- Cart (drawer + page), coupon input
- **One-page checkout** (name, phone, address, district/area, note, payment method, bKash TrxID fields when chosen)
- Order success page (order number, what happens next, bKash verification message)
- Order tracking (order number + phone)
- Search (name/SKU/category, simple LIKE/FULLTEXT)
- Static pages: About, Contact, Delivery info, Return & Exchange, Privacy, Terms, Size Guide
- Bangla/English toggle

### Admin
- Dashboard (today's orders, pending approvals, bKash awaiting verification, revenue, low stock)
- Orders: list/filters/search, detail view, approve/reject, verify bKash, change status, add courier consignment ID, notes, print invoice/packing slip, bulk actions
- Products/variants/images/categories/size charts
- Customers (by phone) with order history, blacklist flag
- Coupons, Banners, Pages, Settings, Delivery zones
- Staff & roles, activity log
- Reports: sales by day/category, top products, payment method split, delivery success

## 8. Phase 2 (after launch)
Courier API (Steadfast/Pathao/RedX) booking + tracking, SMS (order placed/confirmed/shipped), Facebook Conversions API, reviews, wishlist, optional **partial advance for COD** (delivery-charge advance via bKash) as a toggle, abandoned-cart WhatsApp list, product CSV import, simple blog.

## 9. Success metrics
Checkout completion rate, order approval time, fake-order rate, delivery success %, repeat purchase %, Lighthouse mobile ≥ 85, LCP < 2.5 s on 4G.
