# AGENTS.md — Master Instructions for the Coding Agent

You are building a **men's fashion e-commerce site for Bangladesh** from the docs in this folder. Read `00-README.md` first, then this file, then the numbered docs as you reach each phase.

## Non-negotiable product rules

1. **Only two payment methods:** `cod` and `bkash_send_money`. Do NOT integrate SSLCommerz, bKash PGW, Nagad, cards, or anything else.
2. **bKash is manual Send Money.** Checkout shows the store's bKash number (from `settings`). Customer sends money, then enters **TrxID + sender bKash number** (+ amount sent). The order is created as *awaiting verification*. **An admin verifies and approves.** Never auto-mark a bKash order as paid.
3. **Every order needs admin approval** (COD and bKash) before it can move to processing/shipping.
4. **Guest checkout, phone-first.** Email is optional. No forced registration. Order tracking = order number + phone.
5. **Money = unsigned integer BDT.** No floats for money. Ever.
6. **TrxID must be unique** across all orders (DB unique index, case-normalised to uppercase).
7. **Mobile-first.** Design at 360 px width first. Initial page weight budget in `11-SECURITY-SEO-PERFORMANCE.md`.
8. **Admin-editable settings** (bKash number, delivery charges, free-delivery threshold, store phone/WhatsApp, announcement bar) live in the `settings` table, not in code or `.env`.

## Stack (do not substitute without asking)

Laravel 12 · PHP 8.3 · MySQL · Blade + Livewire 3 + Alpine.js + Tailwind CSS (Vite) · Filament 4 admin · Spatie Media Library / Permission / Activitylog / Sluggable · Pest tests. Details: `03-TECH-STACK.md`.

## Conventions

- PSR-12, `declare(strict_types=1);` in new PHP files, typed properties & return types.
- **Thin controllers/Livewire components; business logic in `app/Services` and `app/Actions`.** Order placement, stock changes, status transitions and payment verification go through dedicated Action classes in a DB transaction.
- Order status changes ONLY via `OrderStatusService::transition()` (validates allowed transitions, writes `order_status_histories`, fires events).
- Stock changes ONLY via `StockService` (reserve / release / adjust) with row locking (`lockForUpdate`).
- Use Enums (backed string enums) for statuses: `OrderStatus`, `PaymentStatus`, `PaymentMethod`.
- Form validation in Form Requests or Livewire `rules()`; error messages in Bangla + English lang files (`lang/bn`, `lang/en`).
- All user-visible strings through `__()` helpers. Default locale `bn`, fallback `en`.
- Never trust client prices: **recompute totals server-side** from DB at order time. Snapshot product name, variant, unit price into `order_items`.
- Eager-load relations; no N+1 (use `Model::preventLazyLoading(! app()->isProduction())`).
- Queue anything slow (SMS, emails, Pixel CAPI, image conversions).
- Commit small and often with conventional-commit messages.

## Definition of done (every task)

- [ ] Works at 360 px width and desktop
- [ ] Validation + friendly Bangla/English errors
- [ ] Pest feature test for the happy path and at least one failure path
- [ ] No N+1, indexes exist for new queries
- [ ] Admin side manageable (no hardcoded content that the owner will need to change)
- [ ] `php artisan test` and `./vendor/bin/pint` pass
- [ ] Docs updated if behaviour/schema changed

## Working method

1. Follow phases in `13-ROADMAP-TASKS.md` in order. Finish and verify one phase before the next.
2. If a doc is ambiguous, pick the simplest option consistent with the docs, note it in `DECISIONS.md` (create it), and continue. Ask the owner only for blockers (brand name, bKash number, logo).
3. Seed realistic demo data (men's fashion: T-shirt, polo, shirt, panjabi, pant, jeans, hoodie…) so the UI can be reviewed.
4. Do not add features outside the MVP list without asking; put ideas in `BACKLOG.md`.

## Things to avoid

- SPA frameworks, heavy JS libraries (no jQuery, no large carousels/lightboxes — use Alpine + CSS scroll-snap).
- Storing card or any financial credentials. (We only store the customer-typed TrxID and sender number.)
- Auto-approving anything. Deleting orders (use status + soft deletes).
- Over-engineering: no microservices, no repository pattern ceremony, no event sourcing.
