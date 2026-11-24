# PlaceOrderAction Query Breakdown (30 queries, deterministic)

## Query Breakdown by Table/Operation

| Table | Operation | Count | Purpose |
|-------|-----------|-------|---------|
| blacklists | select exists (phone) | 2 | Check phone blacklist (called twice) |
| blacklists | select exists (ip) | 2 | Check IP blacklist (called twice) |
| blacklists | select value (address keyword) | 1 | Check address keyword blacklist |
| product_variants | select for update | 1 | Lock variant row for stock reservation |
| product_variants | update stock | 1 | Decrement stock |
| product_variants | select by id | 1 | Load variant for pricing |
| customers | select by phone | 1 | Find/create customer |
| customers | insert | 1 | Create customer if new |
| customers | update total_orders | 1 | Increment total_orders |
| customers | update pending_orders_today | 1 | Increment daily counter |
| orders | count pending | 1 | Check daily limit |
| orders | insert | 1 | Create order |
| order_items | insert | 1 | Create order item |
| payments | insert | 1 | Create payment record |
| order_status_histories | insert | 1 | Record initial status |
| activity_log | insert | 1 | Log order placed |
| daily_counters | select for update | 1 | Lock daily counter |
| daily_counters | update sequence | 1 | Increment sequence |
| daily_counters | select by id | 1 | Verify increment |
| product_variants | select by id | 1 | Load for pricing |
| products | select by id | 1 | Load product for pricing |
| colors | select by id | 1 | Load color for pricing |
| media | select by model | 1 | Load product image |
| districts | select by id | 1 | Load district |
| areas | select by id | 2 | Load area (district + pricing) |
| delivery_zones | select by key | 1 | Get delivery charge |
| users | select admins | 1 | Find admins for notifications |
| activity_log | insert | 1 | Log order placed |
| product_variants | update stock | 1 | Decrement stock |
| daily_counters | select for update | 1 | Lock daily counter |
| daily_counters | update sequence | 1 | Increment sequence |
| daily_counters | select by id | 1 | Verify increment |

**Total: 30 queries (deterministic, clean state)**

---