<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Server-Side Tracking (Meta CAPI / TikTok / GA4)

Dynamic, DB-driven tracking. Full guide: `TRACKING_SETUP.md`. Bangla admin guide:
Settings -> Server Side Tracking -> Setup Guide tab.

### Change pixel/account (no deploy, no .env)
1. Admin -> Server Side Tracking -> platform tab
2. New Pixel ID + Access Token (masked, token never shown/logged) -> Save
3. Settings are cached; save clears cache, so the very next event uses the new pixel.
4. Verify: Test Connection, Send Test Event, then Tracking Logs page.

### Queue worker (required) + Supervisor + cron
```bash
php artisan queue:work --queue=tracking --tries=5 --timeout=15
php artisan schedule:run   # every minute via cron (prunes logs older than 90 days daily)
```
Supervisor example (`/etc/supervisor/conf.d/bandrose-tracking.conf`):
```ini
[program:bandrose-tracking]
command=php /path/to/app/artisan queue:work --queue=tracking --sleep=3 --tries=5 --timeout=15
autostart=true
autorestart=true
numprocs=1
```
Cron: `* * * * * php /path/to/app/artisan schedule:run >> /dev/null 2>&1`

### Useful commands
```bash
php artisan tracking:resend 123                 # resend Purchase for one order
php artisan tracking:resend --from=2026-10-01 --status=confirmed
php artisan tracking:prune-logs --days=90
php artisan test --filter=Tracking               # tracking test suite (12 tests)
```

### Add a new platform = one class
1. Create `app/Services/Tracking/Adapters/XyzAdapter.php` implementing
   `TrackingAdapterInterface` (`send/testConnection/sendTestEvent/...`).
2. Register it in `TrackingManager::$adapters` (`''xyz'' => XyzAdapter::class`).
3. Add a `tracking_platforms` row (platform=`xyz`) + tab in the admin page. Nothing else changes.

### Troubleshooting
| Symptom | Cause / fix |
|---|---|
| Login works but no server events | No active platform or no event rules: save settings once (defaults are seeded) |
| Purchase sent twice | Check `orders.purchase_sent_at`; event_id `purchase_{id}` dedups per platform |
| Failed logs pile up, queue warning | Start queue worker; check token validity via Test Connection |
| 400 invalid token/pixel | Permanent error, not retried; platform circuit-breaks, fix token in admin |
| Old pixel still fires in browser | Full-page/CDN cache: purge CDN after pixel switch |

### Acceptance checklist
- [ ] Pixel+token change in admin -> next event hits new pixel (route:list + log)
- [ ] Purchase exactly once per order (value BDT, contents, hashed PII, fbp/fbc, IP/UA)
- [ ] Browser+server share event_id; Meta shows deduplicated
- [ ] Checkout fast; failures never block orders (queued, try/catch)
- [ ] Every event in Tracking Logs with payload/response; failed ones retryable
- [ ] Meta Test Events + TikTok Events Manager + GA4 DebugView verified

## Attribute / Variant / Swatch System

### Admin guide (Bangla)
- **Attribute ON/OFF:** Settings -> Business Settings -> "Attribute System" toggle.
  ON ????? Add/Edit Product-? "Product Variations" section ???? (auto-save, cache clear
  ??? ???? ????)? OFF ???? ??????? ????, product simple ?????? ?????
- **Attribute ????:** Catalog -> Attributes -> Add New (??? + type: Color Swatch /
  Image Swatch / Button / Dropdown) -> value ??? ???? (???? hex code, swatch ???,
  sort order)? ??????? attribute/value delete ??? ???? ?? — ??? Deactivate ?????
- **Product-? variant:** Add Product -> attribute ??? ??? -> value ??????? ???? ->
  "Generate Variants" (?? combination ??????, ????????? ??? ??, SKU auto: BASE-RED-M)?
  Bulk price/stock, per-row ???/????/Active edit, order-???? variant delete-? ?????
- **Stock:** Edit-? stock ?????? inventory ledger-? adjustment ????? Order ??? variant
  ???? stock ???, cancel/return-? ???? ??? (???? ????)?

### Developer notes
- Tables: `attributes` (+type/filterable/active/sort), `attribute_values` (+slug/hex/
  swatch/sort), `product_attributes` (pivot), `product_variants` (+sale/cost/low-stock/
  weight/barcode/active/default; ????? `variant_price/stock/image` column ??????????),
  `product_images` (gallery + `attribute_value_id` swatch link), `order_items`
  (+`variant_sku/name/image` snapshot)?
- `App\Services\VariantGenerator` — combination/SKU/dedup? `ManagesProductVariants`
  trait — Add/Edit Product-? shared (generate/validate/bulk/order-guard, ??????? error)?
- `App\Services\PriceResolver` — ????? ??????? ???? Cart + Order ????? ??? ??????? ???,
  ??? browser-? ??? ???? ????? ????? ??? DB-?? ???? Order total-? server-side recompute ????
- Order item-? variant snapshot ????; variant ???? ????? order view-? ??? ???????
- Tracking: AddToCart-?? `content_id` = variant SKU (feed-?? ???? consistent)?
- Storefront: `?color=Red&size=M` URL share ??? ????, swatch-? color dot/image ??????,
  out-of-stock combination disabled ?????
- Seeder: `php artisan db:seed --class=AttributeSeeder` (setting ON + Color/Size/Unit)?
- Tests: `php artisan test --filter="VariantGeneratorTest|VariantSystemTest"` (7 tests)?

### (Cont.) Variant system — part 2
- **Quick-add value:** generator-? ??????? attribute-?? ???? + ???? — modal ???? value
  (??? + color) ??? ???, list refresh + auto-select ???, reload ???? ???
- **Swatch gallery:** Add/Edit Product-?? ???? "Swatch Gallery" — ??? ????? ??? ???
  color/value-?? ???? ????? ???? (???? = common)? Product ???? ?? swatch ??????? ????
  ?????? ??? ??? ???; thumbnail-? ????? ???? matching swatch auto-select ????
- **Listing filter:** Category ???? Price-?? ???? ??????? filterable attribute
  (???? Color/Size)-?? ????; multi-select, URL-? query string ?????
- **Product card:** color dots — ?????? card-?? ??? ?????? (variant image ?????)?
- **Wholesale slabs:** variant row-? "Slabs" — min qty + ??? (???? 10+: ?480)?
  Cart + Order ??????? `PriceResolver::resolve($product, $variant, $qty)` ????
- **Feeds:** `/feed/google.xml` (Merchant Center), `/feed/meta.csv` (Meta/TikTok) —
  ????? active variant ?? row, `item_group_id` = product id, id = variant SKU
  (tracking `content_id`-?? ???? same)?
- **GA4:** purchase items-? `item_id` (variant SKU) + `item_variant` ("Color: Red") ?????
- Tests: `php artisan test --filter="Variant"` — 11 tests pass?

### (Cont.) Image Options layer (two independent layers)
- Product flags: `has_attributes` (global setting OFF ????? panel hidden — master switch
  ???? ????), `has_image_options`, `require_image_option`. Attribute group-wise required:
  generator-? ??????? attribute-?? ???? Required checkbox (`product_attributes.is_required`)?
- `image_options` table (label/image/own price/sale/stock/active/sort) + ?????? ledger
  `image_option_stock_movements` (main `inventories` untouched)? Admin-? Image Options panel +
  live Effective preview table (??????? combination-?? ???/????)?
- ???: option ?? variant ?? product; variant slab + flash ????? ????: min(variant/product, option)?
  Order-? variant (?? product) + option ??????? deduct, cancel/return-? ??????? restore
  (idempotent, double-deduct/restore ??????)? Checkout-? `SELECT ... FOR UPDATE` lock
  (product ? variant ? option order) — oversell/concurrency safe?
- Cart key: `product-variant-option`; snapshot: variant + option label/image/price?
  Tampered variant/option reject (????? error)? Tracking/feed content_id ???? option
  combination-? ???? format (`productSKU-variantSKU-optionId`), ???? ?? ???? format?
- Storefront: attribute selectors ?? image strip ?????; `?opt=ID&size=M` deep-link;
  sold-out-? reason ?? disabled; option ????? gallery option-???; required-? `*`?
- Feeds: ????? combination ?? row (`/feed/google.xml`, `/feed/meta.csv`)?
- Tests: `php artisan test --filter=VariantSystemTest` — 18 tests (precedence, min-stock,
  double-deduct/restore, oversell, tampering, required, single/none-layer)?

### (Cont.) Auto price/stock — "???? ????? auto"
- Variant ??? ???? ? product ??? (row-?? "auto: ?X" hint); Generate ?? pre-fill ??? ???
  Explicit 0 = ?????????? ?0 (???? data-? ???? ?????? ??)?
- Option stock ???? ? variant/product stock (???? ledger ???? ??? ??); 0 = sold out?
- Bulk: variant Auto-price + Product-stock-copy ????; options-? Bulk ???/?????
- Stock clear ???? ledger ???? follow-variant ???; ??? pending order ????? ledger ???? +
  warning toast (oversell-safe)?
- Preview table ?????? resolved final value ?????? — Save-?? ??? verify ?????

## Sidebar theme engine (per-admin)
- Menu text: Inter font, UPPERCASE (menu + submenu), default 14px.
- Bottom-left palette button: 5 themes (Light / Dark / Periwinkle #D9DEF2 / Mint #E7F0EB /
  Lavender #E3E0E7) — click applies instantly, no reload. Font size A-/A+ (12–18px),
  submenu auto 1px smaller, reset button included.
- Prefs stored per admin (`users.sidebar_theme` JSON) — login ????? ????? theme ??? ????
  Invalid values fall back to Light/14px. New admins see Light/14px (nothing changes suddenly).
- Files: `App\Services\SidebarTheme` (palettes), `Admin\Settings\SidebarThemePicker`
  (Livewire), `public/assets/admin/css/sidebar-theme.css` (CSS vars), theme vars rendered
  server-side in admin layout head + live-applied via `sidebar-theme-applied` event.
- Tests: `php artisan test --filter=SidebarThemeTest` (4 tests).

## Automation foundation (??????? ? — ???? ??????)
- Sidebar-? **Automation** menu (Settings-?? ???): Overview + ? module + AI Settings?
  ???? module = grey + Off badge; ??????? = Coming soon badge?
- ?? module default **OFF**? Mode: Off / Shadow (???? log) / Live? Master switch OFF = ?? ?????
- OFF ???? ?????? ????: `module.enabled:{key}` middleware route ??????, `SkipsWhenDisabled`
  trait listener/job-?? ?????? check ???, ???? API/AI call ??? ???
- Save Cantonment: model observer cache clear ??? — deploy/command ?????? ???? ???? ????????
- Toggle + AI Settings ???? super-admin (`role:admin`); audit log-? secret ???? ???? ???

### ?? module ??? ??? (???? ? ???)
1. `app/Automation/Modules/MyModule/MyModule.php` — `AutomationModule` contract implement
   (key/name/description/icon/status/settingsSchema/guide ????? RiskScoreModule)?
2. `AutomationRegistry::$modules`-? ?? ????: `MyModule::class,`
3. `php artisan db:seed --class=AutomationSeeder` (OFF state-? row ??????)?
Menu, Overview card, settings form, guide, audit — ?? automatic? Custom route/job ?????
`routes()`, listener-? `SkipsWhenDisabled`, job-?? ?????? `skipUnlessEnabled()`?

### AI layer
- `AiService::complete($moduleKey, $prompt)` — budget guard + usage log + PII mask?
  Key encrypted (`automation_api_key`), UI-?? masked? Test Connection rate-limited?
- ???: `ai_usage_logs`; ????? ??%+-? Overview-? warning, ?????? block?
- Tests: `php artisan test --filter=AutomationFoundationTest` (9 tests)?

### (Cont.) Automation menu toggle
- Business Settings-? **Automation Menu** switch: OFF ???? sidebar ???? ???? Automation
  section (header + ?? submenu) ???? ??; ON ???? ????? Auto-save + cache clear, ???? ???? ????????
- Default ON (seeder `firstOrCreate` — re-seed-? ????? ????? overwrite ?? ??)?

### (Cont.) Seller Manage menu toggle
- Business Settings-? **Seller Manage Menu** switch: OFF ???? admin-??? sidebar ???? ????
  Seller Manage section ???? ??; vendor-?? ??????? **Seller Payout** ???? ????? ????
  Auto-save + cache clear, ???? ???? ???????? Default ON?

## Google SEO menu (dynamic, no code edits)
- Sidebar **Google SEO** menu (Business Settings toggle SO/Menu, default ON): Search Console,
  Analytics 4, Tag Manager, XML Sitemap, Robots.txt, Schema Markup — 1 Livewire page, 6 tabs.
- **GA4 shared source**: Measurement ID writes the tracking module`s ga4 row — browser pixel
  + server API both use it, no duplicate tags. Clearing disables GA4.
- **GTM**: container ID + ON/OFF ? head script + body noscript on all storefront layouts + homepage.
- **Sitemap**: `/sitemap.xml` (products/categories/static, 1h cache, auto-refresh on save).
  Submit URL in Search Console. **Robots**: `/robots.txt` from textarea (empty = default + sitemap line).
- **Schema**: Organization/WebSite/Product(???/????/????? auto)/Breadcrumb toggles ? JSON-LD.
  Org info comes from All Settings. Test: validator.schema.org.
- Tests: `php artisan test --filter=SeoTest` (5 tests).

## Category flyout menu (Daraz-style, 3 level)
- Homepage hero panel ??? 3-level flyout: hover/focus-? L2 panel, ??? ????? hover-? L3?
  Mobile-? ???? ??? pills ????? Parent row-?? click ???? parent page-? ?????
- Data: top-level + children.children eager load (N+1 ???), inactive category/child ???????
  L3 data ?? ????? L3 panel ??? ?? — admin sub-sub-category ??????? ???????
- ????? recursive lists (welcome/mobile sidebar) markup ???? ??? ?????? CSS ??? ?? —
  hover/focus/tap-to-open CSS (`@once`) ??? ??????
- Tests: `php artisan test --filter=CategoryMenuTest` (3 tests)?

## Unified storefront chrome (homepage header/footer everywhere)
- `partials/main-header`, `main-footer`, `main-chrome-css`: exact copies of the homepage
  navbar/subnav/footer (homepage.blade.php untouched — zero regression risk).
- New layout `livewire.frontend.layouts.store` uses them. PLP pages
  (CategoryProduct, CategoryView) moved to it; other pages stay on old layouts for now.
- Tests: `php artisan test --filter=StoreLayoutTest` (new chrome present, old header gone, homepage intact).

## PLP layout fix (scoped theme CSS)
- Category pages previously rendered unstyled on the new store layout (theme CSS lived
  only in the old layout). `public/assets/frontend/css/plp.css` holds ONLY PLP classes
  extracted from the theme (scripted extraction, no globals) — header/footer look untouched.
- "No products found" on empty categories is legitimate (query verified by test).

## All Brands page (mock redesign)
- `BrandView` rebuilt: search box, 6-col logo cards (logo/name/N active products),
  LOAD MORE (+18), store layout (homepage header/footer). Sidebar removed (mock).
- Active brands only, alphabetical; slug param ignored (previously crashed on unknown column).
- Tests: `php artisan test --filter=BrandIndexPageTest` (2 tests).
