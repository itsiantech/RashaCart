<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CheckoutInitiated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ?int $userId = null,
        public array $cartItems = []
    ) {}
}
