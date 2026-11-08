<?php

namespace Addons\PartialPayment\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductRule extends Model
{
    protected $table = 'pp_product_rules';

    protected $fillable = ['product_id', 'advance_type', 'advance_value'];

    protected $casts = [
        'advance_value' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function forProduct(?int $productId): ?self
    {
        if (! $productId) {
            return null;
        }

        return static::where('product_id', $productId)->first();
    }

    public function isPercentage(): bool
    {
        return $this->advance_type === 'percent';
    }
}
