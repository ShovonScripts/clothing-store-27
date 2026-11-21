# 11 — Security, SEO, Analytics & Performance

## 1. Security checklist

### Application
- [ ] `APP_DEBUG=false`, `APP_ENV=production`, unique `APP_KEY`, `.env` not web-accessible.
- [ ] HTTPS only (Cloudflare/Let's Encrypt), HSTS, secure + httpOnly + SameSite=Lax cookies.
- [ ] CSRF on all forms (Livewire handles); no CSRF exemptions.
- [ ] Mass-assignment: use `$fillable` or explicit DTOs; never `$request->all()` into models.
- [ ] **Server-side price/total computation only.** Never accept price/total/delivery from client.
- [ ] Validation on every input; strip/escape output (Blade `{{ }}`); sanitise rich text (pages/product description) with an allow-list (e.g. `mews/purifier`).
- [ ] Rate limits: checkout 5/10 min/IP, tracking 10/min/IP, login 5/min, search suggest 30/min.
- [ ] Honeypot field on checkout; optional Cloudflare Turnstile.
- [ ] File uploads: images only (mime + extension + size), re-encode via image library, random filenames, stored outside executable paths.
- [ ] Admin: strong password, 2FA for owner, role-based permissions, session timeout, login throttling, optional IP allow-list or Cloudflare Access for `/admin`.
- [ ] **Order data privacy:** tracking page requires order # + phone; mask details; success page not guessable (session/signed URL).
- [ ] Blacklist checks server-side, generic error message.
- [ ] Do not log full request bodies; never log passwords or tokens. PII (phone/address) access limited to staff roles.
- [ ] Security headers: `Content-Security-Policy` (start in report-only; allow Meta Pixel/GA domains), `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`.
- [ ] Dependency hygiene: `composer audit`, `npm audit`, update monthly; lock versions.
- [ ] Backups encrypted, tested restore.

### Data & privacy
Collect only what's needed (name, phone, address, optional email). Provide Privacy Policy page. Allow customer data deletion on request (admin action to anonymise a customer while retaining order totals for accounting).

### Server
SSH keys only, firewall (80/443/SSH), fail2ban, unattended security upgrades, separate DB user with least privilege, no phpMyAdmin exposed.

## 2. SEO

### Technical
- Clean slugs: `/product/slim-fit-cotton-polo-navy`, `/category/panjabi`.
- Unique `<title>` (≤ 60 chars) and meta description (≤ 155) per page; defaults from templates: `{Product} | {Category} | {BRAND}`.
- Canonical URLs (strip filter/sort params; `noindex,follow` on filtered listing variations and search results).
- `sitemap.xml` (products, categories, pages) regenerated daily; `robots.txt` blocks `/admin`, `/cart`, `/checkout`, `/track-order`.
- Structured data JSON-LD: `Organization`, `WebSite` (+ SearchAction), `BreadcrumbList`, `Product` (name, image[], description, sku, brand, offers{price, priceCurrency=BDT, availability, url}).
- Open Graph + Twitter tags with product image (1200×630 fallback) — important for Facebook sharing.
- `hreflang` only if separate bn/en URLs are introduced (MVP uses toggle without separate URLs → skip).
- 301 redirect when slug changes (store old slugs).
- Image `alt` text, descriptive filenames.
- Fast TTFB (cache), compressed assets (gzip/brotli).

### On-page / content
- Category intro text (100–200 words) with natural keywords: "men's polo shirt price in Bangladesh", "panjabi for men Dhaka", "cotton t-shirt BD".
- Product descriptions: fabric, fit, GSM, wash care, size notes, model height/size.
- Internal linking: related products, category breadcrumbs.
- Google Search Console + Bing Webmaster: submit sitemap. Google Business Profile if physical presence.
- Blog (Phase 2): styling guides, Eid panjabi guide, size guides.

## 3. Analytics & ads

| Tool | Setup |
|---|---|
| **Meta Pixel** | Base code deferred; events: `PageView`, `ViewContent`, `AddToCart`, `InitiateCheckout`, `AddPaymentInfo`, `Lead`/`Purchase` per rule below. Pixel ID from settings. |
| **Meta Conversions API (Phase 2)** | Server events via queued job with hashed phone/email, `fbp/fbc`, event_id dedupe with browser events. |
| **Google Analytics 4** | Optional, same events (`view_item`, `add_to_cart`, `begin_checkout`, `purchase`). |
| **TikTok Pixel** | Optional. |
| **UTM capture** | Save `utm_source/medium/campaign` + `fbclid` first-touch in session → `orders.source` + meta JSON. |

**Purchase event policy (important for fake-order control):** fire browser `InitiateCheckout` + `Lead` at order placement; fire server-side `Purchase` (CAPI) **only when admin approves** (or when delivered, owner's choice, setting `pixel_purchase_on = placed|approved|delivered`, default `approved`). Always use a shared `event_id` for dedupe.

Cookie/consent: simple notice; load pixels after interaction if owner wants strict privacy.

## 4. Performance budget (mobile, 4G)

| Metric | Target |
|---|---|
| LCP | < 2.5 s |
| CLS | < 0.1 |
| INP | < 200 ms |
| TTFB | < 600 ms |
| HTML + critical CSS (home) | < 60 KB gz |
| JS total (home) | < 90 KB gz (Alpine + Livewire + app) |
| Fonts | ≤ 3 files, ≤ 80 KB total, swapped |
| Images above fold | 1 (LCP) preloaded, ≤ 80 KB WebP |
| Lighthouse mobile | ≥ 85 perf, ≥ 95 SEO/accessibility |

### Tactics
- Server: PHP OPcache + JIT off/on tested, `php artisan optimize` (config/route/view/event cache), Redis optional later.
- DB: proper indexes, eager loading, `simplePaginate`, avoid `COUNT(*)` on big lists.
- Caching: home/category tree/settings; `Cache-Control` for static (immutable 1y); Cloudflare cache for assets/images; page caching for anonymous GET of home/category/product is optional (invalidate on admin save).
- Images: responsive srcset (400/600/900/1200), WebP (AVIF optional), lazy-load, fixed aspect boxes, CDN via Cloudflare.
- Vite build: code-split, purge unused Tailwind, no unused Alpine plugins.
- Livewire: `wire:model.live.debounce`, `wire:lazy` for below-fold sections, avoid large public properties (use IDs, not models).
- Third-party scripts: defer Pixel until after load/interaction.
- Preconnect only to essentials; no chat widgets.

## 5. Accessibility & QA gates before launch
Lighthouse mobile audits for Home, Category, PDP, Checkout; manual test on a low-end Android (Chrome) over throttled 4G; screen-reader pass on checkout; test with Bangla long names/addresses; keyboard navigation of modals/drawers.

## 6. Abuse monitoring
Log (activity log) blacklisted attempts, repeated failed TrxID submissions, rate-limit hits; weekly review of cancelled/fake orders → update blacklist.
