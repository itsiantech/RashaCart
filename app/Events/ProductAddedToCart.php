<?php

namespace App\Events;

use App\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductAddedToCart
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Product $product,
        public int $quantity = 1,
        public ?int $userId = null
    ) {}
}
