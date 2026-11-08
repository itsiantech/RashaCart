# Add-ons

Add-ons are optional plugins that live in `addons/{Slug}`. The shop works exactly
as before when no add-on is installed, and an inactive add-on renders **nothing**:
no sidebar item, page, settings tab, order column, popup step or dashboard card.

- Manage them in the admin panel: **Add Ons → Manage Add Ons** (`/admin/addons`).
- The list of active add-ons is cached and invalidated automatically on every
  install / activate / deactivate / uninstall.

---

## Folder layout

```
addons/HelloWorld/
├─ addon.json                 manifest
├─ src/                       PSR-4: Addons\HelloWorld\…
│  └─ HelloWorldServiceProvider.php
├─ routes/admin.php           optional routes
├─ database/migrations/       optional migrations
├─ resources/views/           optional Blade views
├─ lang/en/                   optional translations
├─ config.php                 optional config (merged into config())
└─ assets/                    optional public assets
```

Classes under `src/` are autoloaded as `Addons\{Slug}\…` by
`App\Addons\AddonManager::registerAutoloader()` — no `composer dump-autoload`
needed after adding a folder.

## Manifest (`addon.json`)

```json
{
    "name": "Hello World",
    "slug": "HelloWorld",
    "version": "1.0.0",
    "description": "What this add-on adds.",
    "author": "Store Team",
    "requires_core": "^1.0",
    "provider": "Addons\\HelloWorld\\HelloWorldServiceProvider",
    "depends_on": [],
    "provides": ["Hello World admin page", "Sidebar item"]
}
```

| Field | Required | Notes |
| --- | --- | --- |
| `slug` | yes | Letters/digits/underscore, must match the folder name |
| `name` | yes | Shown in the admin cards |
| `version` | yes | Compared against `requires_core` of other add-ons / the core |
| `requires_core` | no | Default `*`. Supports `^1.2`, `~1.2`, `>=1.0 <2.0`, `1.2.3`, `\|\|` |
| `provider` | yes | Fully qualified service provider class |
| `depends_on` | no | Slugs that must be installed (install) and active (activate) |
| `provides` | no | Short "Adds: …" chips on the admin card |

Core version used for `requires_core` lives in `config/addons.php` (`core_version`).

## Lifecycle

| Action | What happens |
| --- | --- |
| **Install** | Validates manifest + core version + dependencies, runs the add-on's migrations, calls `onInstall()` on the provider, creates the `addons` row (`installed`) |
| **Activate** | Status → `active`; the provider is booted on the next request. Dependencies must be active |
| **Deactivate** | Status → `inactive`. **Data is kept.** Blocked while another active add-on depends on it |
| **Uninstall** | Deactivates first, optionally calls `onUninstall()` and rolls back the add-on's migrations, then deletes the row. Choose whether to drop data |

Provider hooks (optional methods on the provider class):

```php
public function onInstall(): void   { /* seed settings, defaults */ }
public function onUninstall(): void { /* clean up, only when data is dropped */ }
```

### Broken add-ons are isolated

The manager instantiates providers itself and wraps `register()` + `boot()` in a
`try/catch`. If a provider throws:

1. the error is written to the Laravel log,
2. the add-on is set to `inactive` with `last_error`,
3. the request continues normally,
4. the admin **Manage Add Ons** page shows a red "switched off automatically" box.

### Route protection

Every route an add-on registers must use `EnsureAddonActive` so a direct URL
returns 404 while the add-on is inactive:

```php
Route::middleware(['web', 'auth', 'vendor.approved', 'role:admin', EnsureAddonActive::class.':HelloWorld'])
    ->prefix('admin/addons')
    ->name('admin.addons.')
    ->group(function () {
        Route::get('hello-world', Dashboard::class)->name('hello-world');
    });
```

Conventions:
- URL prefix `admin/addons`, route names `admin.addons.*` so the sidebar group
  highlights correctly.
- Add-on routes are only registered while the add-on is active, so activating or
  deactivating one clears a cached route table (`php artisan route:cache`)
  automatically.
- Views: `loadViewsFrom(__DIR__.'/../resources/views', 'hello-world')` → `view('hello-world::admin.page')`.
- Migrations: `loadMigrationsFrom(__DIR__.'/../database/migrations')`.

---

## Hooks API

`App\Support\Hooks` is a small WordPress-style filter/action/slot system.

```php
use App\Support\Hooks;

// Filters: receive a value, return the (possibly) modified value.
Hooks::addFilter('order.initial_status', fn (string $status) => 'confirmed');
$status = Hooks::filter('order.initial_status', 'pending');   // 'confirmed'

// Actions: side effects only.
Hooks::addAction('order.created', fn ($order) => /* … */);
Hooks::doAction('order.created', $order);

// UI slots: a view name or a closure receiving the data array.
Hooks::addSlot('admin.menu', 'hello-world::admin.menu');
Hooks::addSlot('admin.menu', fn (array $data) => '<li>…</li>');

echo Hooks::renderSlot('popup.summary.extra', ['order' => $order]);
```

Priorities (default `10`, lower runs first):

```php
Hooks::addFilter('order.payment_plan', $callback, priority: 5);
```

In Blade:

```blade
@hook('popup.summary.extra', ['order' => $order])
@hook('admin.menu')
```

A failing filter/action/slot is logged and skipped — it never breaks the page.

---

## Extension points

Everything below is live and exercised by `addons/PartialPayment`
(see its `README.md` for a full worked example).

### Order creation — `App\Services\OrderService::createOrder`

All of these run inside the order's own DB transaction.

| Hook | Type | Payload / return |
| --- | --- | --- |
| `order.before_create` | action | `(array $data, array $lines, array $addonInput)` — before the row is inserted |
| `order.create_data` | filter | `(array $data, array $lines, array $addonInput)` → data to store. Core re-applies its own `total_amount` after the filter, so money cannot be changed here |
| `order.initial_status` | filter | `(string $status, array $data, array $lines)` → status for the new order |
| `order.payment_plan` | filter | `(array $plan, Order $order, array $lines, array $addonInput)`; plan = `['pay_now' => float, 'pay_later' => float]` |
| `order.created` | action | `(Order $order, array $paymentPlan, array $addonInput)` |

`$addonInput` is whatever the customer submitted into the popup's `addon` bag
(`OrderPopup::$addon`), forwarded by core as `_addons`.

### Order popup — `App\Livewire\Frontend\OrderPopup`

| Hook | Type | Payload / return |
| --- | --- | --- |
| `popup.steps` | filter | `(array $steps, $popup)` — append `['key' => …, 'label' => …, 'slot' => …]`; core contributes `details` |
| *the slot named by a step's `slot`* | slot | `$hookData` — renders **instead of** the core fields while that step is active. If no add-on registers that slot the step is ignored, so the form can never disappear |
| `popup.submit_gate` | filter | `(null, $popup)` → a step key: the submit button becomes "continue" and jumps to that step instead of validating the payment fields |
| `popup.validation_rules` | filter | `(array $rules, $popup)` — add rules such as `addon.trx_id` |
| `popup.validation_messages` | filter | `(array $messages, $popup)` |
| `popup.validation_attributes` | filter | `(array $attributes, $popup)` |
| `popup.primary_button_label` | filter | `(string $label, $popup)` |
| `popup.summary.extra` | slot | `$hookData` — extra rows under the totals |
| `popup.success.extra` | slot | `['invoice' => string, 'data' => array]` — success screen |
| `popup.success_data` | filter | `(array $data, Order $order, $popup)`; data = `order_id`, `invoice`, `total` |
| `popup.whatsapp_text` | filter | `(string $text, Order $order, $popup)` — the prefilled WhatsApp message |
| `popup.footer` | slot | `$hookData` |

`$hookData` keys: `component`, `product_id`, `variant_id`, `option_id`, `qty`,
`zone`, `unit_price`, `subtotal`, `shipping`, `total`, `currency`. The customer's
own fields are read from `$hookData['component']` (`name`, `phone`, `address`, …).

### Admin

| Hook | Type | Payload / return |
| --- | --- | --- |
| `admin.menu` | slot | sidebar items under **Add Ons** (only rendered for active add-ons) |
| `admin.settings.tabs` | slot | a `<button>` inside the Settings sidebar (`#settings-tab`) |
| `admin.settings.panes` | slot | the matching `.tab-pane` panel |
| `admin.product.form.sections` | slot | `['productId' => ?int, 'mode' => 'create' \| 'edit']` — `productId` is null until the product is first saved |
| `admin.product.saved` | action | `(Product $product)` |
| `admin.orders.columns` | filter | `(array $columns, $component)` → `['key' => …, 'label' => …]` |
| `admin.orders.cell.{key}` | slot | `['order' => Order]` — body of that column |
| `admin.orders.filters` | slot | `['af' => array]` — bind inputs to `af.*` |
| `admin.orders.query` | filter | `(Builder $query, array $af)` — apply your own filters |
| `admin.orders.export_headers` | filter | `(array $headers)` — CSV header row |
| `admin.orders.export_row` | filter | `(array $row, Order $order)` — one CSV row |
| `admin.order.details.panels` | slot | `['order' => Order]` — order view page |
| `admin.dashboard.cards` | slot | extra cards in the top stat row |
| `order.statuses` | filter | `(array $statuses)` — the tab list on the orders page |
| `order.status_query` | filter | `(Builder $query, string $status)` — how each tab filters |
| `order.status_counts` | filter | `(array $counts)` — badge numbers per tab |
| `order.courier_cod_amount` | filter | `(float $amount, Order $order)` — cash the courier is asked to collect |

### Constraints worth knowing before you write an add-on

- **`orders.status` is a MySQL ENUM.** Adding a real status value means altering a
  core table, which add-ons must not do. Expose a *virtual* status instead: keep the
  state in the add-on's own table and surface it through `order.statuses` +
  `order.status_query` + `order.status_counts`. Partial Payment does exactly this
  with `awaiting_advance`.
- **A Livewire component owns its public properties**, so an add-on cannot add fields
  to a core component. Core gives two generic bags instead: `OrderPopup::$addon`
  (customer input) and `OrderIndex::$af` (admin filters). Bind with dot notation,
  e.g. `wire:model.trim="addon.trx_id"`, `wire:model.live="af.payment_status"`.
- **Filters are not exception-safe** — a throwing filter breaks the request. Actions
  and slots are wrapped: they are logged and skipped, so a broken add-on can never
  take the shop down.
- Keep any CSS/JS your views need **inside your own views**. Nothing is emitted while
  the add-on is inactive, so nothing can conflict with the storefront then.

A failing action or slot is logged (`Log::error`) and skipped — a broken add-on
never takes the shop down. A failing **filter** is not caught, so guard it yourself.


---

## Artisan commands

```bash
php artisan addon:list
php artisan addon:make BlogTools        # scaffold a new add-on
php artisan addon:install BlogTools
php artisan addon:activate BlogTools
php artisan addon:deactivate BlogTools
```

`addon:make` creates the manifest, provider, a Livewire page, a route file, a
migration stub, a config file, translations and a README. The generated provider
demonstrates one filter (`order.initial_status`) and one slot (`admin.menu`).

## Testing add-ons

Point the manager at a fixture directory and drive it directly:

```php
config(['addons.path' => base_path('tests/Fixtures/addons')]);
app()->forgetInstance(\App\Addons\AddonManager::class);

$manager = app(\App\Addons\AddonManager::class);
$manager->install('TestDemo');
$manager->activate('TestDemo');

\App\Support\Hooks::flush();
$manager->boot();          // registers the active provider in this process

\App\Support\Hooks::flush(); // between tests, so registrations never leak
```

See `tests/Feature/Addons/AddonSystemTest.php`, the much larger
`tests/Feature/Addons/PartialPaymentTest.php` (17 tests, boots the real
`addons/PartialPayment` against the popup, the admin pages and the queue of
order states) and `tests/Fixtures/addons/`.

Both sample add-ons pass `php artisan test --filter=PartialPaymentTest`, and
`--filter=AddonSystemTest` covers the manager itself.

## Security

Add-ons are PHP code running with full application privileges. Only install
add-ons you wrote or trust. Keep the `addons/` folder out of version control
sources you do not control, and review `addon.json`/`src/` before installing.
