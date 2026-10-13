<?php

namespace App\Listeners;

use App\Events\CheckoutInitiated;
use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Events\ProductAddedToCart;
use App\Events\ProductViewed;
use App\Services\Tracking\TrackingManager;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackingListener implements ShouldQueue
{
    protected TrackingManager $trackingManager;

    public function __construct(TrackingManager $trackingManager)
    {
        $this->trackingManager = $trackingManager;
    }

    /**
     * Handle product viewed event
     */
    public function handleProductViewed(ProductViewed $event): void
    {
        $data = [
            'content_ids' => [$event->product->sku ?? $event->product->id],
            'content_type' => 'product',
            'content_name' => $event->product->name,
            'value' => $event->product->getPriceInfoAttribute()['final'],
            'currency' => 'BDT',
        ];

        $this->trackingManager->trackEvent('ViewContent', $data);
    }

    /**
     * Handle product added to cart event
     */
    public function handleProductAddedToCart(ProductAddedToCart $event): void
    {
        $data = [
            'content_ids' => [$event->product->sku ?? $event->product->id],
            'content_type' => 'product',
            'content_name' => $event->product->name,
            'value' => $event->product->getPriceInfoAttribute()['final'] * $event->quantity,
            'currency' => 'BDT',
            'num_items' => $event->quantity,
        ];

        $this->trackingManager->trackEvent('AddToCart', $data);
    }

    /**
     * Handle checkout initiated event
     */
    public function handleCheckoutInitiated(CheckoutInitiated $event): void
    {
        $data = [
            'content_type' => 'product',
            'num_items' => count($event->cartItems),
        ];

        $this->trackingManager->trackEvent('InitiateCheckout', $data);
    }

    /**
     * Handle order placed event
     */
    public function handleOrderPlaced(OrderPlaced $event): void
    {
        // Track purchase event
        $this->trackingManager->trackPurchase($event->order);
    }

    /**
     * Handle order status changed event
     */
    public function handleOrderStatusChanged(OrderStatusChanged $event): void
    {
        // Status-gated Purchase (e.g. on confirmed/delivered). The manager
        // enforces exactly-once via purchase_sent_at, so a re-send can never
        // happen for the same order.
        $dispatched = $this->trackingManager->trackEvent('Purchase', [], $event->order);

        if ($dispatched > 0) {
            $event->order->markPurchaseAsSent();
        }
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe(): array
    {
        return [
            ProductViewed::class => 'handleProductViewed',
            ProductAddedToCart::class => 'handleProductAddedToCart',
            CheckoutInitiated::class => 'handleCheckoutInitiated',
            OrderPlaced::class => 'handleOrderPlaced',
            OrderStatusChanged::class => 'handleOrderStatusChanged',
        ];
    }
}
