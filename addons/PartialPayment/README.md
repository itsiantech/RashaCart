# Partial Payment (advance + cash on delivery)

Lets the shop ask for a small **advance** on selected products while the rest
stays **cash on delivery**. The customer sends the advance himself through
bKash / Nagad / Rocket, types in the TrxID, and the admin verifies it.

Nothing about the checkout changes when this add-on is **inactive or uninstalled**
— the popup shows no advance row, no payment step, orders are plain COD, and the
admin pages lose the extra column, tab, panel and cards. Data is kept on
deactivation and reused on reactivation.

---

## Install

1. The add-on system must be present (`addons/` folder, `App\Addons\AddonManager`,
   `App\Support\Hooks`). If the admin shows *"the addons table is missing"*,
   run `php artisan migrate` once.
2. **Add Ons → Manage Add Ons** (`/admin/addons`) → **Partial Payment → Install**.
   Installation runs this add-on's own migration (one file, four `pp_*` tables)
   and seeds the settings below. Then press **Activate**.
3. Fill in at least one merchant number in **Settings → Partial Payment**,
   otherwise the payment step tells the customer no number is available yet.

`php artisan addon:install PartialPayment && php artisan addon:activate PartialPayment`
does the same from the command line.

## What it owns

Four tables, all prefixed and all created by
`database/migrations/2026_10_30_000001_create_partial_payment_tables.php`:

| Table | Holds |
| --- | --- |
| `pp_product_rules` | one row per product: `advance_type` (`percent` \| `fixed`) + `advance_value` |
| `pp_order_plans` | one row per order: `advance_amount`, `due_amount`, `payment_status`, `expires_at`, `due_override` |
| `pp_payments` | every money claim: `type` (`advance` \| `balance`), `method`, `amount`, `sender_number`, `trx_id`, `status` (`pending` \| `verified` \| `rejected`). `(method, trx_id)` is **unique** |
| `pp_activities` | who did what and when — every action below is logged with `auth()->id()` |

Settings (core `Setting` store, `pp_` prefix, seeded in `onInstall()`):

| Key | Default | Meaning |
| --- | --- | --- |
| `pp_base` | `total` | `total` = order total incl. delivery, `subtotal` = goods only |
| `pp_expiry_hours` | `24` | `0` disables the expiry job for new orders |
| `pp_bkash_number`, `pp_nagad_number`, `pp_rocket_number` | empty | a method is offered only when its number is filled |
| `pp_instructions` | Bengali sentence | shown on the payment step |

## How the advance is calculated

`Addons\PartialPayment\Services\PlanCalculator`:

```
advance = percent ? ceil(base × value / 100) : min(value, base)
advance = max(1, min(advance, ceil(base)))     // whole taka, never more than the base
due     = total − advance
```

- Percentages always round **up** to the next whole taka.
- If the advance would cover the whole order, **no plan is created** — the order
  stays plain COD rather than demanding 100% prepayment.
- A multi-item order takes the **largest single advance** among the products that
  carry a rule.
- The customer's browser never decides an amount. The popup posts only
  `method`, `sender_number`, `trx_id`; `PlanCalculator::fromOrder()` recomputes
  advance and due from the prices, quantities and delivery charge the server
  already stored. A forged `amount` in the payload is ignored.

## Customer flow

1. Product popup shows **Pay now (advance)** and **On delivery** rows plus a note
   explaining the split (`popup.summary.extra`).
2. The submit button reads *এডভান্স পেমেন্টে যান*. Pressing it walks the customer to
   a second step (`popup.submit_gate` + `popup.steps`) — the order is **not**
   created yet, so incomplete details are still reported normally.
3. The payment step lists only the methods with a merchant number, each with a
   copy button, the exact amount, an instructions box, the sender's number
   (11-digit BD mobile) and the TrxID (6–20 letters/digits). *তথ্যে ফিরে যান*
   returns to the form; the WhatsApp button keeps working on every step.
4. Submit validates, then creates the order in one transaction:
   `order.created` → plan (`payment_status = advance_pending`, `expires_at`) and a
   **pending** payment row with the claimed TrxID. A TrxID already on the books is
   rejected by validation *before* an order is created; `registerOrder()` keeps a
   second guard and logs `duplicate_trx` instead of overwriting a real claim.
5. The success screen repeats what was paid and what is still due.
6. The WhatsApp channel creates the same awaiting-advance order and appends the
   advance/due lines to the message.

## Admin flow

| Where | What |
| --- | --- |
| Product create / edit | *Advance payment* section: none / percentage / fixed, with a live example for the product's own price. Saved through `admin.product.saved` |
| Settings → Partial Payment tab | base, expiry hours, the three merchant numbers, instructions |
| Orders list | extra **Payment** column (advance, state badge, due), a **Payment** filter (awaiting verification / not paid / advance paid / fully collected / refund due) and an **Awaiting advance** tab |
| Order details | payment list with **Verify**, **Reject** (a note is required), **Collect due**, and an override **Release due** (note required). Every action writes an activity row with the admin and the time |
| Add Ons → Partial Payment | refunds page: awaiting verifications, refunds to process, total collected, total to refund, **Mark refunded** |
| Dashboard | three cards — awaiting verification, advance collected, due to collect |
| Courier / CSV | the COD amount handed to the courier and the CSV export use the **due**, not the order total (`order.courier_cod_amount`, `admin.orders.export_*`) |

Delivery guard: an order with an unverified advance, or with an open due,
**cannot be marked delivered** — the save throws and the admin page shows the
reason. *Release due* (or *Collect due*) is the admin override. Cancelling an
order whose advance was verified moves the plan to `refund_due`.

Plan states: `advance_pending → advance_paid → paid`, or `refund_due → refunded`.

## Expiry

```bash
php artisan partial-payment:expire
```

Cancels `pending` orders whose advance window (`expires_at`) has run out and logs
`expired`. The provider schedules it hourly (with `withoutOverlapping`) **only
while the add-on is active**, so `php artisan schedule:run` (or the existing cron
entry) is enough — no extra crontab line.

## The one thing this add-on could not do to the core

`orders.status` is a MySQL **ENUM**. Writing `awaiting_advance` into it would mean
`ALTER TABLE orders` on a core table, which an add-on must not do. Instead the
state lives in `pp_order_plans.payment_status` and the admin list exposes
**Awaiting advance** as a *virtual* status: `order.statuses` adds the tab,
`order.status_query` filters it with a sub-query on the add-on's own table and
`order.status_counts` fills its badge. The popup's initial status therefore
stays `pending`, and `order.initial_status` is deliberately not touched.

## Tests

```bash
php artisan test --filter=PartialPaymentTest
```

17 tests / 148 assertions covering: install touches only `pp_*` tables, rounding
and fixed-claim caps, `pp_base`, the popup rows + gate, no-rule products staying
plain COD, server-authoritative amounts vs a tampered claim, sender/TrxID
validation and a replayed TrxID, verify → reject → collect → refund transitions,
delivery blocked then allowed, the override release, the product rule form, the
awaiting-advance tab and CSV columns, the expiry command, and the two guarantees:
**deactivated = no advance UI and plain COD**, **reactivating restores every
existing rule, plan and payment**.
