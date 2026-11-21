# 06 — Payments: COD + bKash Send Money (manual verification)

> Only these two methods exist. There is **no gateway integration** and **no automatic payment confirmation**.

## 1. Payment methods

| Method | `payment_method` | Initial `payment_status` | Who confirms |
|---|---|---|---|
| Cash on Delivery | `cod` | `unpaid` | Admin approves order; courier collects cash; admin marks collected |
| bKash Send Money | `bkash_send_money` | `awaiting_verification` | Admin checks bKash app/SMS, then verifies |

Both can be toggled in settings (`cod_enabled`, `bkash_enabled`). At least one must stay enabled.

## 2. bKash manual flow (customer side)

Checkout panel when "bKash Send Money" is selected:

```
┌───────────────────────────────────────────────┐
│ bKash দিয়ে পেমেন্ট করুন                         │
│ 1. bKash অ্যাপে "Send Money" সিলেক্ট করুন        │
│ 2. নম্বর: 01XXXXXXXXX  [Copy]                    │
│ 3. পরিমাণ: ৳1,370  (এই অর্ডারের মোট)              │
│ 4. পেমেন্ট শেষে SMS/অ্যাপ থেকে TrxID কপি করুন     │
│                                               │
│ Your bKash number (sender)  [01_________]      │
│ Transaction ID (TrxID)      [8N7A6D5EE3  ]     │
│ Amount sent (৳)             [1370        ]     │
│ Note: send exact amount. Wrong/duplicate TrxID │
│ will cancel the order.                         │
└───────────────────────────────────────────────┘
```

Rules:
- The displayed receiving number is read from `settings.bkash_number` and **snapshotted** into `payments.bkash_receiver_number`.
- Exact amount = order total (including delivery). Customer-typed amount saved to `bkash_amount_sent`.
- TrxID normalisation: trim, uppercase, remove spaces. Validation regex `^[A-Z0-9]{8,12}$` (bKash IDs are typically 10 chars; keep range loose so format changes do not break checkout).
- **Unique:** reject if another payment already has the same `bkash_trx_id` (DB unique index + validation message).
- Sender number: BD mobile format, normalised.
- Amount sent must be ≥ order total (if less → validation error; if more → allow, admin sees overpayment flag).
- Optional Phase 2: screenshot upload (`bkash_proof_path`, ≤ 2 MB image).

## 3. Admin verification flow

Order detail page shows a **Payment Verification card** for bKash orders:

| Field | Source |
|---|---|
| Expected amount | `payments.amount` |
| Amount customer says sent | `bkash_amount_sent` (red if < expected) |
| TrxID | `bkash_trx_id` + Copy |
| Sender number | `bkash_sender_number` + `tel:` / WhatsApp link |
| Receiver number shown | `bkash_receiver_number` |
| Time placed | `orders.created_at` (+ "x hours ago") |
| Duplicate check | auto-lookup: other orders with same sender number / TrxID similarity |

Admin actions:
1. Open bKash app → transaction history → find TrxID → confirm **amount, sender, type = Send Money, date/time**.
2. **✅ Verify & Approve** → `VerifyBkashPaymentAction`:
   - `payments.status = paid`, `verified_by`, `verified_at`
   - `orders.payment_status = paid`, `orders.status = confirmed`, `approved_by/at`
   - history rows + activity log + event `OrderApproved`
3. **❌ Reject payment** (requires reason: "TrxID not found", "Amount mismatch", "Wrong number", other) → `RejectBkashPaymentAction`:
   - `payments.status = rejected`, `rejection_reason`
   - `orders.status = cancelled`, stock released
   - Shows ready-to-send WhatsApp/SMS text so admin can ask customer to reorder or send correct TrxID.
4. **↩ Ask customer to resend TrxID** (optional MVP+): sets payment to `awaiting_verification` with note; customer can update via tracking page (token link). Keep for Phase 2 if complex.

Bulk "Verify selected" is **not allowed** (each must be checked deliberately).

## 4. COD flow
1. Order placed → `pending / unpaid`.
2. Admin (or CS) calls the customer → **Approve** → `confirmed`. If unreachable after attempts → **Cancel** with reason ("unreachable", "customer cancelled", "fake order").
3. Courier delivers and collects `cod_amount`.
4. Admin sets `delivered` then **Mark COD collected** → `payments.status=paid`, `collected_at=now`.
5. On `failed_delivery`/`returned` → payment stays `unpaid`; stock restock decision.

## 5. Order total & rounding
`total = subtotal − discount + delivery_charge`, integers only. COD amount = total. bKash expected amount = total.

## 6. Anti-fraud & abuse controls

| Risk | Control |
|---|---|
| Fake/reused TrxID | Unique DB index; admin manual check in bKash app; auto-expire after `bkash_verify_hours` |
| Wrong amount | Validate ≥ total at checkout; admin sees mismatch highlight |
| Prank/fake COD orders | Approval call; per-phone daily limit; honeypot field; rate limit (5 checkout submits/10 min/IP); blacklist |
| Repeat bad customers | `customers.cancelled_orders` counter; "high risk" badge when ≥ 2 failed/cancelled; admin one-click **Blacklist phone** |
| Duplicate orders (double click) | Checkout idempotency token + disabled button |
| Bots | Honeypot, throttle, optional Cloudflare Turnstile (free) on checkout |
| Staff misuse | Role permissions; activity log of every verify/reject with user & time |

## 7. Edge cases
- **Customer pays but TrxID wrong:** admin rejects with reason; message template to customer; customer can place new order or contact support. (Phase 2: "resend TrxID" link.)
- **Customer pays after order cancelled/expired:** admin handles manually (refund via bKash Send Money); record in `admin_note`; mark payment `refunded` if done.
- **Overpayment:** flag `amount_sent > total`; admin decides refund of difference.
- **Order edited after placing (add/remove items):** MVP: not editable; cancel & recreate. Phase 2: admin edit with recalculated total and bKash difference handling.
- **Partial COD advance (Phase 2 toggle):** for outside-Dhaka COD, require delivery-charge advance by bKash. Implement via `payments` rows with `type=advance` — schema already allows multiple payments per order; design UI later.
- **Number change:** if owner changes `bkash_number`, past orders keep their snapshot.

## 8. Settings page fields (Filament)
`bkash_number`, `bkash_instructions_bn/en`, `bkash_verify_hours`, `cod_enabled`, `bkash_enabled`, `max_pending_orders_per_phone_per_day`, `block_cod_outside_dhaka_over` (Phase 2, 0=off).

## 9. Reporting
- Payment split chart (COD vs bKash).
- **bKash awaiting verification** queue with age.
- COD pending collection list (delivered but not marked collected; shipped > 7 days).
- Daily cash summary: bKash verified total, COD collected total.

## 10. Future gateway readiness
Keep a `PaymentMethod` enum + `payments.meta` json so bKash Payment Gateway/Nagad/SSLCommerz can be added later as new methods without refactoring orders. The owner should evaluate moving off a personal bKash number to a merchant solution as volume grows (check current bKash terms).

## 11. Required tests
- Place bKash order with valid fields → order `pending`, payment `awaiting_verification`.
- Duplicate TrxID rejected (also case/space variants).
- Amount < total rejected.
- Verify action sets paid + confirmed, writes history/activity.
- Reject action cancels order and restores stock.
- Auto-expire command cancels only unverified bKash orders older than threshold.
- COD order can be approved without payment; `Mark COD collected` only after delivered.
- Non-privileged staff cannot verify if permission `payments.verify` is absent.
