# 10 — Delivery, Courier & Returns

> Courier rates, API fields and settlement terms change. Defaults below are placeholders; owner must confirm current merchant terms with the chosen courier.

## 1. Delivery zones & charges (editable in admin)

| Zone key | Applies to | Default charge | Estimate |
|---|---|---|---|
| `inside_dhaka` | Dhaka city areas (areas flagged `is_inside_city`) | ৳70 | 1–2 days |
| `outside_dhaka` | All other districts and Dhaka-district areas outside city (Savar, Keraniganj, Gazipur etc. — owner decides) | ৳130 | 2–5 days |

- Optional: free delivery over a threshold (`free_delivery_over`, 0 = off).
- Optional later: per-district override, weight-based rules, express delivery.
- Charge is computed server-side from district/area chosen at checkout and **snapshotted** on the order.

### Selecting district/area at checkout
1. District select (64, with Bangla names, searchable).
2. If district = Dhaka → show Area/Thana select (inside-city list + "Other").
3. Zone resolved: Dhaka + inside-city area → `inside_dhaka`; else `outside_dhaka`.

## 2. MVP courier workflow (manual)
1. Order `confirmed` → admin prints **packing/courier slip**.
2. Pack parcel; (optional) bulk-export CSV for the courier's merchant panel bulk upload (columns: name, phone, address, district, COD amount, note, merchant order id).
3. Book parcel in courier merchant panel/app (manual).
4. In admin: **Ship** action → choose courier (Steadfast / Pathao / RedX / Paperfly / eCourier / Other), enter **consignment/tracking ID**, optional tracking URL → status `shipped`, `shipped_at` set.
5. Customer sees courier + tracking ID on tracking page.
6. Courier outcome:
   - Delivered → admin marks `delivered` → (COD) **Mark COD collected** when money is received/settled.
   - Customer unreachable/refused → `failed_delivery` (record reason) → re-attempt or `returned`.
7. Returned parcel received at store → **Return** action: choose restock (yes/no per item) → stock adjusted; note any loss.

## 3. Courier tracking URL templates (configurable, verify)
Store as settings: `courier_tracking_templates` JSON, e.g.
```
steadfast: https://steadfast.com.bd/t/{consignment}     (verify)
pathao:    https://merchant.pathao.com/tracking?consignment_id={consignment}&phone={phone}  (verify)
redx:      https://redx.com.bd/track-global-parcel/?trackingId={consignment}  (verify)
```
If a template is empty, just display the ID.

## 4. Phase 2: Courier API integration
- Implement `CourierGateway` interface: `book(Order): BookingResult`, `track(string $consignment): TrackingResult`, `cancel(...)`.
- Adapters: `SteadfastGateway`, `PathaoGateway`, `RedxGateway` (start with the one owner uses). Credentials in `.env` / settings (encrypted).
- Admin action **Book with {Courier}** → calls API → stores `shipments` row + consignment ID + status.
- Scheduled job polls tracking status every 30–60 min for `shipped` orders; webhook endpoint (signed) if courier supports it.
- Auto-update order status from courier status map (delivered, returned, hold).
- Idempotency: don't double-book; store raw response JSON.
- Fallback: always allow manual consignment entry.

## 4b. Courier fee & COD settlement tracking (Phase 2)
Columns/records: courier delivery fee, COD fee, return fee, settlement date/reference. Report: "Cash expected from courier" vs "received".

## 5. Return & exchange policy (default; owner edits in `pages`)
- Exchange for size/defect within **3 days** of delivery if unworn with tags (default).
- Customer contacts via WhatsApp/phone with order number; admin creates a note and arranges pickup/return.
- Return shipping cost: borne by store for defects/wrong item; by customer for size/choice (default; editable).
- Refund for prepaid bKash orders: via bKash Send Money, recorded as `refunded`.
- COD refusal at door: parcel returns; customer flagged after repeated refusals.

## 6. Packaging
Branded poly mailer/box, invoice slip inside, tissue/sticker optional. Include order number outside for matching. Photograph/video parcel packing for dispute evidence (operational tip).

## 7. Delivery messaging on site
- PDP: "Delivery: ৳70 inside Dhaka (1–2 days), ৳130 outside Dhaka (2–5 days)" (pulled from settings).
- Checkout: delivery line updates after district chosen.
- Footer + `/page/delivery-info` with the table, courier names and working days/holiday notice.

## 8. Operational KPIs
Time from order placed → approved; approved → shipped; delivery success %; failed-delivery rate by district; return rate by product/size (informs size chart quality).
