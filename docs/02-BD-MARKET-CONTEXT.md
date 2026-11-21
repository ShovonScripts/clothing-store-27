# 02 — Bangladesh Small Clothing E-Commerce: Market Context

> Written from general market knowledge. Treat numbers as defaults to be confirmed by the owner. The purpose: help the agent make design decisions that fit how BD customers and shops actually behave.

## 1. How small BD clothing shops sell today
- **Facebook page + Messenger/WhatsApp + F-commerce** is the dominant channel. Customers comment "price", get DM, share name/phone/address, shop confirms by phone.
- A **website** adds trust, SEO, catalogue, stock accuracy and order management — but it must feel as simple as the Messenger flow.
- Ads: Facebook/Instagram Meta ads, TikTok; landing from ad → product page. **Meta Pixel (+ Conversions API)** matters for ad optimisation.

## 2. Customer behaviour to design for
| Behaviour | Design implication |
|---|---|
| 90%+ mobile, mid/low-end Android, variable data | Light pages, WebP, no heavy JS, sticky bottom CTA, thumb-friendly |
| Low trust in online shops (fake/low-quality product fear) | Trust strip: real photos, return policy, COD, phone/WhatsApp visible, reviews later |
| Prefer **Cash on Delivery** | COD is default-selected; clear "check product at delivery / return policy" copy |
| bKash is everyday money | bKash instructions with big copy-able number, step list, TrxID field with example |
| People type phone numbers in many formats, mixed Bangla/English digits | Normalise (`+88`, spaces, Bangla digits → ASCII), validate 11-digit BD mobile |
| Addresses are informal ("Mirpur 10, Dhaka") | Single free-text address + district + thana/area dropdowns; don't over-validate |
| They expect a **confirmation call** | Orders start `pending`; admin/CS calls; show "We will call you shortly to confirm" |
| Size confusion → returns | Size chart, model height/size note, "fit" tag, easy exchange policy |
| Price sensitive; likes offers | Old price strikethrough, % off badge, coupons, free delivery threshold |
| Bangla is preferred for reading, English for product names | Bangla default UI; allow English product names; Bangla-capable fonts |
| Many orders are fake/prank or later refused at door | Approval step, per-phone limits, blacklist, optional delivery-charge advance (Phase 2) |

## 3. Delivery landscape
- **Couriers commonly used:** Steadfast, Pathao Courier, RedX, Paperfly, eCourier, plus SA Paribahan/Sundarban for district hand-offs. Most offer merchant panels and **COD collection with weekly/daily settlement** and APIs (confirm current terms).
- **Typical charge structure:** Inside Dhaka cheaper (1–2 days), outside Dhaka higher (2–5 days). Default in docs: **70 / 130 ৳** (editable).
- **Failed delivery** (customer unreachable/refuses) is the biggest cost → approval call + blacklist + clear charges up-front.
- Return parcels: shop typically bears return delivery cost; plan a `failed_delivery`/`returned` status and a place to record courier loss/fee.

## 4. Payment landscape
- **COD**: majority of orders.
- **bKash Send Money (personal)**: customers send to the shop's number and share the **TrxID** (10-character alphanumeric, e.g. `8N7A6D5EE3`). Shop checks bKash app/SMS to confirm amount + TrxID + sender, then confirms order.
- Typical **risks**: fake TrxID, reused TrxID, wrong amount, sending to wrong number, forged screenshots. Mitigation: unique TrxID, amount check, sender-number match, manual check in the bKash app, optional screenshot upload (Phase 2).
- **Regulatory/practical note (verify):** personal bKash numbers have limits and are not meant for large-scale business receipts; the owner should plan to move to a bKash Merchant/Payment Gateway account later. The system is designed so that a future gateway can be added without schema changes (`payments` table with `method`, `reference`, `meta`).

## 5. Product & seasonality notes (men's fashion)
- **Panjabi/Fatua** peak: Eid-ul-Fitr, Eid-ul-Adha, Puja, Pohela Boishakh, wedding season. Stock/promotions spike; support banner scheduling.
- **Winter** (Nov–Feb): hoodies, jackets, sweaters, thermals — current season is relevant.
- **T-shirts/polo/shirts/jeans/pants:** year-round bestsellers; drops of new designs weekly are common.
- Typical price points (indicative, ৳): T-shirt 350–900, Polo 600–1,400, Shirt 800–2,000, Panjabi 1,200–4,500, Jeans 1,200–2,800, Hoodie 900–2,200, Jacket 1,800–5,000.
- Common size labels: M/L/XL/XXL; chest in inches for panjabi; waist 28–40 for pants.
- Fabric words customers search: cotton, pique, drop-shoulder, oversized, slim fit, regular fit, premium, **GSM**.

## 6. Operations reality (solo/small team)
- Owner uses phone more than a laptop → **admin panel must be mobile-friendly** and approving an order must take ≤ 3 taps.
- Needs **printable invoice + courier packing slip** (A5/A4) in Bangla/English.
- Uses WhatsApp/Messenger for support → floating contact buttons + `wa.me` deep links prefilled with product/order info.
- Cash reconciliation: needs a view of "COD collected vs pending from courier".

## 7. Legal/administrative (owner to confirm)
Trade licence/TIN as applicable, clear return policy, accurate product descriptions, privacy policy (collecting names/phones/addresses). Keep terms pages ready. Consider e-CAB membership/Digital Commerce guidelines compliance as the business grows.

## 8. Competitive references (for UX inspiration only)
Look at well-known BD fashion/e-commerce storefronts and Daraz-style product pages for: sticky CTA, delivery-charge messaging, size chart modals, COD badges. Do **not** copy layouts/assets.
