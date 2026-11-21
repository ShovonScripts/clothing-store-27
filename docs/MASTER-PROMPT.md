# MASTER PROMPT — paste this to the coding agent

You are a senior Laravel engineer building a **men's fashion e-commerce store for Bangladesh**. The full specification is in the `docs/` folder (the documentation pack: `AGENTS.md`, `00-README.md`, `01` … `14`). Build the whole project yourself, **one phase at a time, testing each phase before starting the next**, without waiting for me between phases unless you are truly blocked.

## 0. Setup (do this first)
1. Put the documentation pack into `docs/` in the project root (create the Laravel project first if it doesn't exist).
2. Read in full, in this order: `docs/AGENTS.md`, `docs/00-README.md`, `docs/01-PRODUCT-REQUIREMENTS.md`, `docs/03-TECH-STACK.md`, `docs/04-DATABASE-SCHEMA.md`, `docs/13-ROADMAP-TASKS.md`. Read the other docs (`02, 05, 06, 07, 08, 09, 10, 11, 12, 14`) at the start of the phase that needs them.
3. Create three tracking files in the project root:
   - `PROGRESS.md` — checklist of phases 0–5 with status (`todo / in-progress / done`), date, and test result summary.
   - `DECISIONS.md` — any ambiguity you resolved and why (pick the simplest option consistent with the docs).
   - `BACKLOG.md` — ideas outside MVP scope (never build them without asking).

## 1. Non-negotiable rules (from `docs/AGENTS.md`, repeated because they matter most)
- **Only two payment methods:** Cash on Delivery and **bKash Send Money (manual)**. Customer enters TrxID + sender number; **admin verifies and approves**. No gateways. Never auto-mark bKash as paid.
- **Every order needs admin approval** before processing/shipping.
- Guest checkout, phone-first; tracking = order number + phone.
- **Money = unsigned integer BDT.** Recompute all totals server-side. Snapshot prices/names into `order_items`.
- TrxID unique (uppercase-normalised, DB unique index).
- Status changes only via `OrderStatusService`; stock changes only via `StockService` with row locks; business logic in Actions/Services in DB transactions.
- Stack is fixed: Laravel 12, PHP 8.3, MySQL, Blade + Livewire 3 + Alpine + Tailwind, Filament 4 admin, Spatie packages, Pest. Do not substitute.
- Mobile-first (360 px), Bangla default with English toggle, performance budget from `docs/11`.
- Admin-editable settings live in the `settings` table (bKash number, delivery charges, etc.).
- Placeholders: brand = `{BRAND}` in `config/store.php`; bKash number `01XXXXXXXXX` in settings. Use them; do not stop to ask.

## 2. The phase loop (repeat for Phase 0 → 5 from `docs/13-ROADMAP-TASKS.md`)

For each phase, follow these steps **in order**:

**A. Plan** — Re-read the phase's tasks and its relevant docs. Write a short task list (TaskCreate) for the phase. Mark `PROGRESS.md` phase as `in-progress`.

**B. Build** — Implement every task in the phase. Small, focused commits with conventional-commit messages (`feat:`, `fix:`, `test:`, `chore:`). Follow `AGENTS.md` conventions (strict types, thin controllers, Enums, Form validation, lang files via `__()`).

**C. Test (hard gate — the phase is NOT done until all pass)**
1. Write Pest tests for the phase: happy path + at least one failure path per feature (use the test lists in `docs/06` §11, `docs/13` test matrix, and the phase's acceptance criteria).
2. Run `php artisan migrate:fresh --seed` — must succeed from scratch.
3. Run `./vendor/bin/pint` and `php artisan test` — both must pass with zero failures.
4. Run the app (`php artisan serve` + `npm run build` or Vite) and **actually exercise the phase's feature**: hit the routes, use the Livewire components, call the Filament pages (e.g. with HTTP requests, Pest browser/Livewire tests, or Artisan tinker). Verify the phase's **Accept** line in `docs/13` item by item.
5. Check: no N+1 (lazy loading prevention on in non-production), 360 px layout sanity for UI work, Bangla text renders, no PHP warnings in `storage/logs`.

**D. Fix** — If anything fails, fix the root cause and re-run **all** of step C. Do not skip, weaken, or delete tests to get green. If the same failure persists after 3 genuine attempts, stop and report it (see §4).

**E. Close the phase** — Update `PROGRESS.md` (status `done`, date, test counts, what was verified, any known limitations), update docs if behaviour/schema changed, commit, and print a short phase report:
```
PHASE X DONE
- Built: …
- Tests: N passed (list key ones)
- Verified manually: … (matches Accept criteria: yes/no per item)
- Decisions/deviations: … (also in DECISIONS.md)
- Next: Phase X+1
```

**F. Continue** — Immediately start the next phase. Do not ask for permission between phases.

## 3. Phase order (summary — details in `docs/13-ROADMAP-TASKS.md`)
0. **Bootstrap** — Laravel, packages, Tailwind/Alpine, fonts, settings service, enums, Money, PhoneNormalizer, CI, tracking files.
1. **Catalogue & admin basics** — migrations, seeders (districts, zones, colours, categories, pages, ~24 demo products), Filament resources, settings pages, roles.
2. **Storefront browsing** — layout, home, listing + filters, product page, search, static pages, SEO/JSON-LD/sitemap.
3. **Cart, checkout, orders (core)** — CartService, pricing/delivery/coupon, Checkout Livewire (COD + bKash panel), `PlaceOrderAction`, success + tracking, anti-abuse.
4. **Admin order management (core)** — OrderResource, all state-aware actions via Action classes, bKash verify/reject, stock release, dashboard, customers/blacklist, invoice + packing slip (Bangla), expiry command.
5. **Polish & hardening** — security headers, rate limits, performance, accessibility, Meta Pixel events, backups, deploy script, go-live checklist report.

Do **not** start Phase 6 (post-launch backlog: SMS, courier API, CAPI, etc.).

## 4. When to stop and ask me
Only stop if: (a) a required decision cannot be resolved with a sensible default, (b) the same failure persists after 3 attempts, (c) an external credential/account is needed that you cannot mock, or (d) a doc contradicts itself in a way that changes behaviour. When you stop, report: what you were doing, what failed, what you tried, and 2–3 options with your recommendation. Otherwise keep going.

## 5. Quality bar
- Seed data must make the UI reviewable (realistic men's fashion products, sizes, colours, stock, Bangla + English names).
- Never hardcode owner-editable content (bKash number, charges, announcement, banners, pages).
- Security basics from `docs/11` are mandatory from Phase 3 onward (server-side prices, rate limits, honeypot, CSRF, blacklist).
- Keep the codebase simple: no microservices, no repository-pattern ceremony, no extra JS libraries.

## 6. Final deliverable (after Phase 5)
Produce `FINAL-REPORT.md` containing: the go-live checklist from `docs/12` with pass/fail per item, list of manual steps the owner must do (real bKash number, logo/colours, real products, courier account, domain/server), known limitations, and the Phase 6 backlog. Then give me a 10-line summary.

**Start now with Phase 0.**
