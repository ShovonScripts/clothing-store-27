# 09 — UI/UX Design System

## 1. Design direction
Modern, clean, **men's streetwear/menswear boutique**: lots of whitespace, large product photography, confident typography, minimal chrome. Must feel trustworthy and quick, not flashy. Owner will supply brand colours/logo; defaults below.

## 2. Tokens (Tailwind theme)

```
Colors
  ink        #111111   (text, primary buttons)
  paper      #FAFAF8   (page background)
  surface    #FFFFFF
  line       #E7E5E0   (borders)
  muted      #6B6B6B
  accent     #C8102E → configurable (brand accent: sale badges, key CTA hover)
  success    #1F8A4C   warning #B7791F   danger #C53030   info #2B6CB0
  bkash      #E2136E   (only on bKash badge/panel accents)
Radius      sm 6, md 10, lg 16, full for pills
Shadow      subtle: 0 1px 2px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.06)
Spacing     4px scale; page gutter 16px mobile / 24 tablet / 32+ desktop; max width 1280px
Breakpoints 360 (base) · 640 · 768 · 1024 · 1280
```

## 3. Typography
- Bangla: **Hind Siliguri** (or Noto Sans Bengali) 400/500/600/700.
- Latin/headings: **Plus Jakarta Sans** or **Inter** 500/600/700.
- Base 16px mobile (never smaller than 14px), line-height 1.6 for Bangla (taller glyphs; 1.5 minimum), headings 1.25.
- Prices: tabular numbers, semibold; use `৳` prefix; thousands separator; optionally Bangla digits when locale = bn (setting, default ASCII digits for clarity).
- Self-host fonts, preload the primary weight, `font-display: swap`.

## 4. Layout rules (mobile first)
- 2-column product grid; 8–12 px gap; image ratio **4:5** (portrait) consistently.
- Tap targets ≥ 44×44 px; primary buttons full width on mobile.
- Sticky elements: header (compact 56px), product-page bottom CTA bar, checkout summary collapses into expandable bar.
- Avoid layout shift: fixed image aspect ratios, reserved heights for banners.
- Respect safe-area insets (iOS) for sticky bars.

## 5. Components (Blade components in `resources/views/components`)
`x-button` (primary/secondary/ghost/danger, loading state), `x-input`, `x-select`, `x-textarea`, `x-radio-card` (payment choice), `x-badge`, `x-price` (price + old price + discount %), `x-product-card`, `x-rating` (later), `x-swatch` (colour), `x-size-pill`, `x-qty-stepper`, `x-modal`, `x-drawer`, `x-accordion`, `x-breadcrumbs`, `x-toast`, `x-alert`, `x-empty-state`, `x-section-heading`, `x-trust-strip`, `x-whatsapp-button`, `x-skeleton`.

States: hover/focus-visible rings (accessible), disabled, loading, error (with inline Bangla/English message), success.

## 6. Key screen specs

### Home (mobile)
1. Announcement bar (40px).
2. Header (logo left, search + cart icons right).
3. Hero slider 4:5 or 1:1 mobile image, one clear CTA ("Shop New Arrivals").
4. Horizontal category chips/tiles (scrollable).
5. "New Arrivals" product row.
6. Trust strip (4 icons, 2×2 on mobile).
7. "Best Sellers" grid.
8. Footer with accordion links.

### Product page (mobile)
- Full-width gallery with dot indicators → title → price row → colour swatches → size pills + "Size chart" link → stock hint → delivery & payment badges → accordions → related.
- **Sticky bottom bar:** `[Add to Cart]` (outline) `[Order Now]` (solid). Shows selected size/colour summary when scrolled.

### Checkout (mobile)
- Order summary collapsed at top ("Show order summary ৳1,370 ▾").
- Section cards: 1 Delivery info → 2 Payment → 3 Review.
- Payment as two large radio-cards: **Cash on Delivery** (default) and **bKash Send Money** (pink accent). bKash panel expands with steps, copy button, TrxID input (monospace, auto-uppercase), sender number, amount.
- Sticky `Place Order — ৳1,370` button.
- Inline validation, scroll to first error, keep keyboard type correct (`inputmode="tel"` for phone, `autocapitalize="characters"` for TrxID).

### Order success
Large check icon, order number, plain-language next steps, WhatsApp CTA, summary card.

### Cart drawer
Slides from right (full width mobile), items list, subtotal, `View Cart` + `Checkout`.

## 7. Imagery
- Product photos: portrait 4:5, 1200×1500 source, clean background, consistent crop; 3–6 per product (front, back, detail, model).
- WebP/AVIF delivery; `srcset` + `sizes`; `loading="lazy"` except LCP image (`fetchpriority="high"`).
- Blur-up/colour placeholder (dominant colour background) instead of spinners.

## 8. Motion
Subtle only: 150–200 ms transitions, drawer slide, button press scale 0.98. Respect `prefers-reduced-motion`.

## 9. Accessibility
Colour contrast ≥ 4.5:1, visible focus, labels for all inputs, `aria-live` for cart count & errors, modals trap focus & close on Esc, alt text for images (product name + colour), semantic landmarks.

## 10. Bangla/English handling
- Language toggle in header (BN | EN). Persist in cookie.
- Layout must not break with longer Bangla strings (avoid fixed-width buttons).
- Numbers: ASCII by default; store both labels in lang files.
- Address and name inputs accept Bangla and English.

## 11. Trust & conversion elements
- Badges: Cash on Delivery · bKash accepted · Easy Exchange · Fast Delivery.
- Visible phone/WhatsApp in header or footer and on checkout.
- Clear delivery charges before checkout completion.
- Return policy summary on PDP.
- Real product photos; optional "customer photos" strip later.
- "Order now" copy in Bangla: **এখনই অর্ডার করুন**.

## 12. Admin UI
Use Filament defaults with brand colour; enable dark mode; use compact tables on mobile; configure `->striped()`, sticky header; ensure action buttons are visible without horizontal scroll.

## 13. Performance-driven design constraints
No autoplay video above the fold; max 1 hero image preloaded; limit third-party scripts (Pixel only, deferred); avoid web-font icon packs.
