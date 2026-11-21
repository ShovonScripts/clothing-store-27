# 05 — Storefront Features & User Flows

## 1. Site map
```
/                         Home
/shop                     All products
/category/{slug}          Category listing (filters via query string)
/product/{slug}           Product detail
/search?q=                Search results
/cart                     Cart page (also a drawer)
/checkout                 One-page checkout
/order/success/{order_number}   Thank-you (signed URL or session-guarded)
/track-order              Track by order number + phone
/page/{slug}              Static pages
/lang/{bn|en}             Locale switch
/admin                    Filament admin
/sitemap.xml, /robots.txt
```

## 2. Global layout
- **Announcement bar** (admin text, e.g. "Free delivery over ৳2,000 • Cash on Delivery all over Bangladesh").
- **Header:** logo, search icon, cart icon with count, hamburger (mobile), language toggle. Desktop: category mega-menu.
- **Mobile bottom nav (optional):** Home, Categories, Search, Cart, WhatsApp.
- **Footer:** about, contact, social, policies, payment badges (COD, bKash), delivery note.
- **Floating contact button:** WhatsApp (prefilled message) + Messenger. Must not cover sticky CTA on product page (stack above it).

## 3. Home page sections (all admin-driven)
1. Hero banner carousel (CSS scroll-snap, mobile/desktop images, 1–3 slides)
2. Category tiles (6–8)
3. New Arrivals (8 products, horizontal scroll on mobile)
4. Trust strip: Cash on Delivery • Easy Exchange • Fast Delivery • Genuine Product
5. Featured / Best Sellers (grid)
6. Offer banner strip
7. Social proof (later: reviews / Instagram grid)
8. Newsletter/WhatsApp subscribe (optional)

## 4. Product listing (category/shop/search)
- Grid: 2 columns mobile, 3–4 desktop. Card: image (hover second image on desktop), name, price, old price, `-20%` badge, "Sold out" / "New" badge. Quick-add is NOT in MVP (variants needed) — card links to PDP.
- Filters (Livewire, URL-synced): size, colour, price range, in-stock only, sort (Newest, Price low→high, high→low, Best selling). Mobile: bottom-sheet filter.
- Pagination: "Load more" button (Livewire) or simple pagination; page size 12/24.

## 5. Product detail page (PDP)
- Gallery: swipeable (scroll-snap) with thumbnails; pinch/tap to zoom (simple fullscreen modal with Alpine).
- Title, price (with old price/discount), short description, fabric/fit chips.
- **Colour swatches** then **size buttons**. Disabled when out of stock. Selecting colour updates available sizes and (if linked) the image.
- Stock hint ("Only 3 left").
- Quantity stepper.
- **Size chart** button → modal with table from `size_charts`.
- **Buttons:** `Add to Cart` and `অর্ডার করুন / Buy Now` (adds then goes to checkout). Sticky bottom bar on mobile.
- Delivery box: charge per zone, estimated days, COD available, bKash available, return policy link.
- Accordion: Description, Fabric & Care, Delivery & Returns.
- WhatsApp "Ask about this product" button (prefilled with product name + URL).
- Related products (same category).
- JSON-LD `Product` schema (price in BDT, availability).
- Track `ViewContent` (Meta Pixel) and `AddToCart`.

## 6. Cart
- Drawer after add-to-cart; full page at `/cart`.
- Lines: image, name, variant label, unit price, qty stepper (max = stock), remove.
- Re-validate on every render; show inline warnings (price changed / out of stock / reduced qty).
- Coupon field → applies discount, shows savings.
- Summary: subtotal, discount, delivery (shown as "calculated at checkout" or estimated via district selector), total.
- CTA: **Proceed to Checkout**.

## 7. Checkout (one page, Livewire `Checkout` component)

### Fields
| Field | Rules |
|---|---|
| Full name | required, 2–80 chars |
| Phone | required, BD mobile, normalised to `01XXXXXXXXX` |
| Email | optional |
| District | required (select, searchable) |
| Area/Thana | optional select (Dhaka) or free text |
| Full address | required, 10–250 chars |
| Order note | optional, 250 chars |
| Payment method | radio: **COD** (default) / **bKash Send Money** |
| Coupon | optional |

### Delivery charge
Recomputed live when district/area changes: `inside_dhaka` or `outside_dhaka` (+ free-delivery threshold). Show breakdown in order summary.

### Payment: COD
Info text: "Pay in cash when you receive the parcel. We will call you to confirm your order."

### Payment: bKash Send Money
Shown panel (see `06` for details):
1. "Send **৳{total}** via **Send Money** to **{bkash_number}**" + Copy button.
2. After sending, enter **Transaction ID (TrxID)** from your bKash SMS/app.
3. Enter the **bKash number you sent from**.
4. (Optional) amount sent prefilled = total.
Validation: TrxID `^[A-Z0-9]{8,12}$` (uppercased/trimmed), sender number BD mobile, amount ≥ total, TrxID not already used.

### Submit
`Place Order` button → `PlaceOrderAction` → redirect to success page. Button disabled + spinner after click (prevents double submit; server idempotency token too).

### Right/summary column
Items list, subtotal, discount, delivery, **total**, trust badges.

## 8. Order success page
- "Thank you, {name}! Your order **#{number}** has been placed."
- COD: "Our team will call you within {X} hours to confirm."
- bKash: "We received your TrxID **{id}**. We'll verify the payment and confirm your order shortly."
- Summary of items, address, total.
- Buttons: Track order, Continue shopping, WhatsApp us.
- Fire Meta Pixel `Purchase` (or `Lead`/custom — see `11`) once (guard with session flag).

## 9. Order tracking
`/track-order` form: order number + phone. Shows timeline: Placed → Confirmed → Processing → Shipped (courier + consignment link) → Delivered; cancelled state shown clearly. Rate-limited (10/min/IP). Never reveal full address; mask phone.

## 10. Search
Header search overlay with live suggestions (Livewire, debounce 300 ms, max 6 results) + results page. MySQL FULLTEXT/LIKE on name, SKU, category.

## 11. Order lifecycle (what each party sees)

```
Customer places order
  ├─ COD  → order: pending / payment: unpaid
  └─ bKash→ order: pending / payment: awaiting_verification
        │
Admin reviews (call/WhatsApp optional)
  ├─ Approve (COD)            → confirmed
  ├─ Verify bKash & Approve   → payment: paid, order: confirmed
  ├─ Reject bKash payment     → payment: rejected, order: cancelled, stock released
  └─ Cancel                   → cancelled, stock released
        │
Processing (packing) → Shipped (courier + consignment ID) → Delivered
        │                                   │
        │                                   ├─ COD: admin marks "COD collected" → payment: paid
        │                                   └─ Failed delivery / Returned → restock decision
Auto: bKash order unverified > N hours → cancelled (stock released)
```

## 12. Notifications
- **Admin:** new order → Filament database notification + badge count in nav; optional browser notification (Filament) and email.
- **Customer (MVP):** on-screen only + WhatsApp click-to-chat from admin. **Phase 2:** SMS on placed/confirmed/shipped using a BD SMS gateway behind `SmsGateway` interface.

## 13. Edge cases to handle
- Variant sells out while in cart or during checkout → friendly error, adjust qty.
- Price changed between cart and order → use current DB price, show notice.
- Same TrxID submitted twice → validation error: "This TrxID has already been used."
- Customer on blacklist → generic message "Unable to place order. Please contact us." (don't reveal reason).
- Orders paused (`maintenance_orders_paused`) → banner + disabled checkout.
- Outside-Dhaka COD orders over a threshold (optional setting) → may require bKash/advance (Phase 2 rule).
- Browser back after success page → don't resubmit; show success again via session/signed URL.
