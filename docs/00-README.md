# Men's Fashion E-Commerce (Bangladesh) — Project Documentation Pack

> Brand name is not decided yet. Everywhere in these docs, `{BRAND}` is a placeholder. Replace it once, in `config/store.php` and `.env`.

## 1. What we are building

A small, fast, mobile-first online clothing store for **men's fashion in Bangladesh**.

- **Payments (only two):**
  1. **Cash on Delivery (COD)**
  2. **bKash Send Money (manual)** — customer sends money to the store's bKash number, then types the **Transaction ID (TrxID)** and sender number at checkout.
- **Every order is approved by an admin** before it is processed. No automatic confirmation.
- Typical BD small-shop reality: traffic comes mostly from **Facebook/Instagram/TikTok on cheap Android phones over mobile data**. Customers expect a **phone call/WhatsApp confirmation**, phone-number-first checkout, Bangla + English, and courier delivery (Steadfast / Pathao / RedX / Paperfly).

## 2. Chosen stack (short version — full reasoning in `03-TECH-STACK.md`)

| Layer | Choice |
|---|---|
| Backend | **Laravel 12** (PHP 8.3+) |
| Storefront | **Blade + Livewire 3 + Alpine.js + Tailwind CSS** (server-rendered = fast on weak phones + great SEO) |
| Admin panel | **Filament 4** |
| DB | **MySQL 8 / MariaDB 10.6+** |
| Cache/Queue | Database driver first, Redis optional later |
| Images | Spatie Media Library, auto WebP + responsive sizes |
| Tests | Pest |
| Hosting | Single VPS (Singapore region) + Cloudflare |

Why not Next.js/React/Vue SPA: more moving parts, worse first-load on cheap phones, harder SEO and harder for a solo dev to maintain. Not needed for this scope.

## 3. Document index (read in this order)

| File | Purpose |
|---|---|
| `AGENTS.md` | **Master instructions for the coding agent.** Rules, conventions, definition of done. Read first. |
| `01-PRODUCT-REQUIREMENTS.md` | Goals, scope, personas, MVP vs later, business rules |
| `02-BD-MARKET-CONTEXT.md` | How small BD clothing e-commerce actually works; behaviours to design for |
| `03-TECH-STACK.md` | Stack decision, packages, folder structure, conventions |
| `04-DATABASE-SCHEMA.md` | Every table, column, index, relationship, seed data |
| `05-USER-FLOWS-AND-FEATURES.md` | Storefront pages & behaviours, checkout flow, order states |
| `06-PAYMENTS-COD-BKASH.md` | COD + bKash manual verification, anti-fraud rules, edge cases |
| `07-ADMIN-PANEL.md` | Filament resources, order approval UX, dashboard, roles |
| `08-ROUTES-AND-ARCHITECTURE.md` | Route list, controllers/Livewire components, services, jobs, events |
| `09-UI-UX-DESIGN.md` | Design system, Bangla typography, mobile layouts, component list |
| `10-DELIVERY-COURIER.md` | Delivery zones/charges, courier workflow, returns |
| `11-SECURITY-SEO-PERFORMANCE.md` | Security checklist, SEO, Facebook Pixel/CAPI, performance budget |
| `12-DEPLOYMENT-OPERATIONS.md` | Server setup, env, backups, monitoring, go-live checklist |
| `13-ROADMAP-TASKS.md` | Phased task list with acceptance criteria + ready-to-paste agent prompts |
| `14-COPY-AND-CONTENT.md` | Bangla/English UI strings, policy pages, SMS templates |

## 4. Open decisions (owner must fill; agent should use defaults until then)

| # | Item | Default used in docs |
|---|---|---|
| 1 | Brand name, logo, colours | `{BRAND}`, black/off-white/one accent |
| 2 | bKash personal number to receive money | `01XXXXXXXXX` in settings table (admin editable) |
| 3 | Delivery charge | Dhaka 70 ৳ / outside Dhaka 130 ৳ (editable in admin) |
| 4 | Free delivery threshold | Off by default (setting) |
| 5 | Return/exchange policy window | 3 days exchange for size issue (editable page) |
| 6 | Courier | Manual first (admin types consignment ID); API integration in Phase 2 |
| 7 | SMS provider | None in MVP; interface prepared, provider in Phase 2 |
| 8 | Domain | `.com.bd` or `.com` — decide before launch |
| 9 | Language | Bangla + English toggle, Bangla default for UI copy |

> **Verify before launch** (policies change): current bKash personal-account rules/limits for receiving business payments, courier charges and API terms, and any e-commerce registration/trade-licence requirements. These docs are written from general knowledge of the BD market, not from live checks.

## 5. Currency & units

- Currency: **BDT (৳)**. Store prices as **unsigned integers in taka** (no paisa). Display as `৳1,250`.
- Phone format: Bangladeshi mobile `01[3-9]XXXXXXXX` (11 digits). Normalise `+8801…` / `8801…` to `01…`.
- Timezone: `Asia/Dhaka`.
