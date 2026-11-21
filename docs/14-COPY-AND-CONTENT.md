# 14 — Copy, Microcopy & Content Templates (Bangla + English)

> Owner/native speaker should review Bangla before launch. Keys go into `lang/bn/*.php` and `lang/en/*.php`.

## 1. Global / navigation
| Key | বাংলা | English |
|---|---|---|
| nav.home | হোম | Home |
| nav.shop | শপ | Shop |
| nav.categories | ক্যাটাগরি | Categories |
| nav.cart | কার্ট | Cart |
| nav.track | অর্ডার ট্র্যাক করুন | Track Order |
| search.placeholder | পণ্য খুঁজুন… | Search products… |
| announce.default | সারা বাংলাদেশে ক্যাশ অন ডেলিভারি | Cash on Delivery all over Bangladesh |

## 2. Product page
| Key | বাংলা | English |
|---|---|---|
| pdp.add_to_cart | কার্টে যোগ করুন | Add to Cart |
| pdp.buy_now | এখনই অর্ডার করুন | Order Now |
| pdp.select_size | সাইজ বাছাই করুন | Select size |
| pdp.select_color | রং বাছাই করুন | Select colour |
| pdp.size_chart | সাইজ চার্ট | Size Chart |
| pdp.sold_out | স্টক শেষ | Sold out |
| pdp.low_stock | মাত্র :count টি বাকি | Only :count left |
| pdp.delivery_info | ঢাকার ভিতরে ৳:inside (১–২ দিন), ঢাকার বাইরে ৳:outside (২–৫ দিন) | Inside Dhaka ৳:inside (1–2 days), outside Dhaka ৳:outside (2–5 days) |
| pdp.ask_whatsapp | হোয়াটসঅ্যাপে জিজ্ঞাসা করুন | Ask on WhatsApp |
| pdp.select_variant_error | অনুগ্রহ করে সাইজ ও রং বাছাই করুন | Please select size and colour |

## 3. Cart & checkout
| Key | বাংলা | English |
|---|---|---|
| cart.empty | আপনার কার্ট খালি | Your cart is empty |
| cart.subtotal | সাবটোটাল | Subtotal |
| cart.discount | ডিসকাউন্ট | Discount |
| cart.delivery | ডেলিভারি চার্জ | Delivery charge |
| cart.total | সর্বমোট | Total |
| coupon.apply | কুপন প্রয়োগ করুন | Apply coupon |
| checkout.name | আপনার নাম | Full name |
| checkout.phone | মোবাইল নম্বর | Mobile number |
| checkout.email | ইমেইল (ঐচ্ছিক) | Email (optional) |
| checkout.district | জেলা | District |
| checkout.area | এলাকা/থানা | Area / Thana |
| checkout.address | পূর্ণ ঠিকানা | Full address |
| checkout.note | অর্ডার নোট (ঐচ্ছিক) | Order note (optional) |
| checkout.payment | পেমেন্ট পদ্ধতি | Payment method |
| checkout.cod | ক্যাশ অন ডেলিভারি | Cash on Delivery |
| checkout.cod_help | পণ্য হাতে পেয়ে টাকা দিন। কনফার্মেশনের জন্য আমরা আপনাকে কল করব। | Pay in cash when you receive the parcel. We will call to confirm. |
| checkout.bkash | বিকাশ সেন্ড মানি | bKash Send Money |
| checkout.place_order | অর্ডার কনফার্ম করুন | Place Order |

## 4. bKash panel
| Key | বাংলা | English |
|---|---|---|
| bkash.title | বিকাশে পেমেন্ট করুন | Pay with bKash |
| bkash.step1 | বিকাশ অ্যাপে "Send Money" অপশন নির্বাচন করুন | Open bKash and choose "Send Money" |
| bkash.step2 | এই নম্বরে পাঠান: :number | Send to this number: :number |
| bkash.step3 | টাকার পরিমাণ: ৳:amount | Amount: ৳:amount |
| bkash.step4 | পেমেন্ট শেষে SMS/অ্যাপ থেকে TrxID কপি করে নিচে দিন | After payment, copy the TrxID from the SMS/app and enter it below |
| bkash.copy | নম্বর কপি করুন | Copy number |
| bkash.copied | কপি হয়েছে | Copied |
| bkash.sender | যে বিকাশ নম্বর থেকে পাঠিয়েছেন | bKash number you sent from |
| bkash.trx | ট্রানজেকশন আইডি (TrxID) | Transaction ID (TrxID) |
| bkash.amount | পাঠানো টাকার পরিমাণ | Amount sent |
| bkash.note | সঠিক পরিমাণ পাঠান। ভুল বা পুনরায় ব্যবহৃত TrxID হলে অর্ডার বাতিল হতে পারে। | Send the exact amount. A wrong or reused TrxID may cancel the order. |

## 5. Validation messages
| Key | বাংলা | English |
|---|---|---|
| v.phone | সঠিক ১১ ডিজিটের মোবাইল নম্বর দিন (০১XXXXXXXXX) | Enter a valid 11-digit mobile number (01XXXXXXXXX) |
| v.required | এই তথ্যটি আবশ্যক | This field is required |
| v.trx_format | সঠিক TrxID দিন (৮–১২ অক্ষর, ইংরেজি অক্ষর ও সংখ্যা) | Enter a valid TrxID (8–12 letters/numbers) |
| v.trx_used | এই TrxID আগে ব্যবহার করা হয়েছে | This TrxID has already been used |
| v.amount_low | পাঠানো টাকা অর্ডারের মোটের চেয়ে কম | Amount sent is less than the order total |
| v.stock | দুঃখিত, :name এর মাত্র :count টি স্টকে আছে | Sorry, only :count of :name left in stock |
| v.blocked | অর্ডার সম্পন্ন করা যায়নি। অনুগ্রহ করে আমাদের সাথে যোগাযোগ করুন। | We couldn't place this order. Please contact us. |
| v.too_many | আপনার অনেকগুলো অর্ডার পেন্ডিং আছে। আমাদের সাথে যোগাযোগ করুন। | You have several pending orders. Please contact us. |

## 6. Order success & tracking
- **Title:** ধন্যবাদ, :name! আপনার অর্ডার #:number গ্রহণ করা হয়েছে। / Thank you, :name! Your order #:number has been received.
- **COD:** আমাদের প্রতিনিধি শীঘ্রই কনফার্মেশনের জন্য আপনাকে কল করবেন। / Our team will call you shortly to confirm.
- **bKash:** আমরা আপনার পেমেন্ট যাচাই করছি। যাচাই হলে অর্ডার কনফার্ম করা হবে। / We are verifying your payment. Your order will be confirmed once verified.
- **Status labels:** pending=অপেক্ষমান/Pending · confirmed=কনফার্মড/Confirmed · processing=প্রস্তুত হচ্ছে/Processing · shipped=কুরিয়ারে পাঠানো হয়েছে/Shipped · delivered=ডেলিভারি সম্পন্ন/Delivered · cancelled=বাতিল/Cancelled · returned=ফেরত/Returned

## 7. SMS / WhatsApp templates (Phase 2 for SMS; admin can copy for WhatsApp in MVP)
- **Order placed:** `{BRAND}: আপনার অর্ডার #:number গ্রহণ করা হয়েছে। মোট ৳:total। শীঘ্রই কনফার্ম করা হবে।`
- **Confirmed:** `{BRAND}: আপনার অর্ডার #:number কনফার্ম হয়েছে। শীঘ্রই কুরিয়ারে পাঠানো হবে।`
- **Shipped:** `{BRAND}: অর্ডার #:number কুরিয়ারে দেওয়া হয়েছে (:courier, ID: :consignment)। ক্যাশ অন ডেলিভারির টাকা: ৳:cod`
- **bKash rejected:** `{BRAND}: অর্ডার #:number এর বিকাশ পেমেন্ট যাচাই করা যায়নি (:reason)। সঠিক TrxID পাঠান বা কল করুন :phone`
- **Cancelled (no answer):** `{BRAND}: কনফার্মেশনের জন্য যোগাযোগ করা সম্ভব না হওয়ায় অর্ডার #:number বাতিল করা হয়েছে।`
(Bangla is Unicode: a single SMS segment holds only ~70 characters, so these templates may span 2–3 segments. Confirm the gateway's Unicode limits and per-segment cost, and keep messages short.)

## 8. Static page drafts (owner reviews)

### Delivery Information
- ঢাকার ভিতরে: ১–২ কর্মদিবস, ডেলিভারি চার্জ ৳{inside}.
- ঢাকার বাইরে: ২–৫ কর্মদিবস, ডেলিভারি চার্জ ৳{outside}.
- সারা বাংলাদেশে ক্যাশ অন ডেলিভারি সুবিধা। অর্ডার কনফার্মেশনের জন্য আমরা ফোন করব।
- কুরিয়ার সার্ভিস: Steadfast / Pathao / RedX (পরিবর্তনযোগ্য)।
- English version mirrors the above.

### Return & Exchange (default draft)
- ডেলিভারির ৩ দিনের মধ্যে সাইজ/ডিফেক্টের কারণে এক্সচেঞ্জ করা যাবে; পণ্য ব্যবহার না করা ও ট্যাগসহ থাকতে হবে।
- ভুল/ত্রুটিপূর্ণ পণ্য হলে ফেরত ডেলিভারি খরচ আমরা বহন করব।
- সাইজ/পছন্দের কারণে ফেরতের ক্ষেত্রে ডেলিভারি খরচ কাস্টমার বহন করবেন।
- যোগাযোগ: {phone} / WhatsApp {whatsapp}

### About, Contact, Privacy, Terms, Size Guide
Agent creates structural placeholders; owner writes final text. Privacy page must state what data is collected (name, phone, address, optional email), why, retention, contact for deletion. Size guide: how to measure (chest, waist, length) with simple illustrations; link from every PDP.

## 9. Brand voice
Friendly, respectful, short sentences, no hype. Use "আপনি" (formal-friendly). Avoid slang. English product names allowed inside Bangla sentences.

## 10. Product copy template
```
Title:  {Fit} {Fabric} {Type} – {Colour}        e.g. Slim Fit Cotton Polo – Navy
Bullets: • Fabric: 100% cotton pique, 220 GSM  • Fit: Slim  • Wash: Cold, do not bleach
Sizing:  Model height 5'9", wearing L. Chest: M 38" / L 40" / XL 42".
Delivery: ৳{inside} inside Dhaka, ৳{outside} outside. COD + bKash.
```
