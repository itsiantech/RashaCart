# Server-Side Tracking System - Setup Guide

## Overview

This is a production-grade, fully dynamic, multi-platform server-side tracking system for your Laravel ecommerce application. It supports Meta (Facebook), TikTok, and GA4 with pluggable architecture for adding new platforms.

## Key Features

- ✅ **Dynamic Configuration**: Change Pixel ID/Token from admin panel - no code/.env/deployment needed
- ✅ **Multiple Pixels**: Support for multiple pixels per platform
- ✅ **Encrypted Tokens**: Access tokens encrypted at rest, masked in UI
- ✅ **Server-Side + Browser**: Both browser and server-side tracking with deduplication
- ✅ **Queue-Based**: All API calls go through queued jobs (checkout never slows down)
- ✅ **PII Hashing**: Bangladesh phone normalization, SHA256 hashing for all PII
- ✅ **Event Rules**: Configure which events to send, trigger type, delay, etc.
- ✅ **Audit Logs**: Track who changed what (without sensitive data)
- ✅ **Bangla UI**: Setup guide in Bangla for non-technical admins
- ✅ **Circuit Breaker**: Pause failing platforms automatically
- ✅ **Event Logs**: View all events, retry failed ones, resend for specific orders

## Prerequisites

- Laravel 12+
- Redis (recommended) or Database queue
- PHP 8.2+
- MySQL

## Installation

### 1. Run Migrations

```bash
php artisan migrate
```

This will create the following tables:
- `pixel_settings` (modified for multiple pixels, encrypted tokens)
- `tracking_event_rules`
- `tracking_logs`
- `tracking_audit_logs`
- Additional columns in `orders` table for attribution

### 2. Configure Queue

**Option A: Redis (Recommended)**

Update `.env`:
```env
QUEUE_CONNECTION=redis
REDIS_QUEUE=tracking
```

**Option B: Database Queue**

Update `.env`:
```env
QUEUE_CONNECTION=database
DB_QUEUE=tracking
```

### 3. Configure Supervisor

Create supervisor config file: `/etc/supervisor/conf.d/tracking-worker.conf`

```ini
[program:tracking-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/your/project/artisan queue:work redis --queue=tracking --sleep=3 --tries=5 --timeout=15
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/your/project/storage/logs/tracking-worker.log
stopwaitsecs=3600
```

Then:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start tracking-worker:*
```

### 4. Configure Cron

Add to your crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

The system includes a scheduled command to prune old tracking logs (configurable).

### 5. Enable Attribution Middleware

Add to your web routes (if not already added in `bootstrap/app.php`):

```php
Route::middleware(['tracking.attribution'])->group(function () {
    // Your frontend routes
});
```

Or apply globally:
```php
// In bootstrap/app.php
$middleware->alias([
    'tracking.attribution' => \App\Http\Middleware\TrackingAttribution::class,
]);
```

## Usage

### 1. Access Admin Panel

Navigate to: `Settings > Server Side Tracking`

### 2. Configure Platforms

#### Meta (Facebook)
1. Go to Meta Business Suite > Events Manager
2. Select your pixel
3. Settings > Conversions API
4. Generate Access Token
5. Copy Pixel ID and Access Token
6. Paste in admin panel
7. Click "Test Connection" to verify
8. Click "Send Test Event" to test
9. Save

#### TikTok
1. Go to TikTok Ads Manager > Assets > Event > Web Events
2. Select or create pixel
3. Event Setup > Events API
4. Generate Access Token
5. Copy Pixel ID and Access Token
6. Paste in admin panel
7. Test and Save

#### GA4
1. Go to Google Analytics > Admin > Data Streams
2. Select your web stream
3. Copy Measurement ID (G-XXXXXXXXXX)
4. Measurement Protocol API Secrets > Create
5. Copy API Secret
6. Paste in admin panel
7. Test and Save

### 3. Configure Event Rules

Navigate to the Event Rules tab (or section) to configure:
- Which events to track (PageView, ViewContent, AddToCart, InitiateCheckout, Purchase)
- Trigger type (frontend, order_placed, order_status)
- Browser vs Server-side sending
- Delay (in minutes)
- Value mode (full, exclude_shipping, custom)

### 4. View Logs

Navigate to Tracking Logs to:
- Filter by platform, event, status, date, order ID
- View payload and response
- Retry failed events
- Resend events for specific orders

## Integration with Existing Code

### Product View
```php
use App\Events\ProductViewed;

event(new ProductViewed($product));
```

### Add to Cart
```php
use App\Events\ProductAddedToCart;

event(new ProductAddedToCart($product, $quantity));
```

### Checkout Initiated
```php
use App\Events\CheckoutInitiated;

event(new CheckoutInitiated($userId, $cartItems));
```

### Order Placed
The system automatically fires `OrderPlaced` event when an order is created via `OrderService`. No additional code needed.

### Order Status Changed
```php
use App\Events\OrderStatusChanged;

event(new OrderStatusChanged($order, $oldStatus, $newStatus));
```

## Browser Tracking

The browser pixel is automatically included in the frontend layout. To use:

```javascript
// Track ViewContent
window.TrackingHelper.trackViewContent(productId, productName, value, 'BDT');

// Track AddToCart
window.TrackingHelper.trackAddToCart(productId, productName, value, 'BDT', quantity);

// Track InitiateCheckout
window.TrackingHelper.trackInitiateCheckout(value, 'BDT', numItems);

// Track Purchase (on thank-you page)
window.TrackingHelper.trackPurchase(orderId, value, 'BDT', contents);
```

## Troubleshooting

### Events not being sent
1. Check if queue worker is running: `php artisan queue:work --queue=tracking`
2. Check `tracking_logs` table for errors
3. Verify Pixel ID and Access Token are correct
4. Check platform health status

### Connection test failing
1. Verify Pixel ID format
2. Check Access Token is valid and not expired
3. Ensure platform API is accessible from your server
4. Check for IP whitelist restrictions

### PII errors
1. Ensure phone numbers are in Bangladesh format: 01XXXXXXXXX
2. Check that email addresses are valid
3. Verify hashing service is working correctly

### Queue worker not processing
1. Check Supervisor is running: `sudo supervisorctl status`
2. Check logs: `tail -f storage/logs/tracking-worker.log`
3. Restart worker: `sudo supervisorctl restart tracking-worker:*`

### Duplicate events
1. Check `tracking_logs` for duplicate entries
2. Verify event_id generation is unique
3. Check deduplication logic in TrackingManager

## Adding a New Platform

1. Create adapter class implementing `TrackingAdapterInterface`:
```php
namespace App\Services\Tracking\Adapters;

class NewPlatformAdapter implements TrackingAdapterInterface
{
    // Implement methods: send(), testConnection(), sendTestEvent(), etc.
}
```

2. Register adapter in `TrackingManager`:
```php
protected array $adapters = [
    'meta' => MetaAdapter::class,
    'tiktok' => TikTokAdapter::class,
    'ga4' => Ga4Adapter::class,
    'new_platform' => NewPlatformAdapter::class, // Add this
];
```

3. Add UI tab in `server-side-tracking.blade.php`
4. Add platform settings partial
5. Update browser pixel partial

## Security

- Access tokens are encrypted using Laravel's encrypted cast
- Tokens are never exposed to frontend or page source
- Tokens are masked in admin UI (showing only first 8 and last 4 characters)
- PII is hashed before sending to platforms
- PII is masked in tracking_logs
- Only admin role can access tracking settings

## Performance

- All tracking API calls are queued (non-blocking)
- Checkout speed is never affected
- Settings are cached with automatic invalidation
- Queue worker processes events asynchronously
- Circuit breaker prevents repeated failures

## Maintenance

### Prune Old Logs
```bash
php artisan tracking:prune-logs --days=30
```

Or configure in admin panel (when implemented).

### Backfill Missed Events
```bash
php artisan tracking:resend --order-id=123
php artisan tracking:resend --from=2024-01-01 --to=2024-01-31
php artisan tracking:resend --status=confirmed
```

## API Version Notes

- Meta Conversions API: Default v19.0 (configurable in admin)
- TikTok Events API: Latest version
- GA4 Measurement Protocol: Latest version

## Support

For issues or questions:
1. Check tracking logs for error details
2. Review platform-specific documentation
3. Verify queue worker is running
4. Check Supervisor logs

## License

This tracking system is part of your Laravel ecommerce application.
