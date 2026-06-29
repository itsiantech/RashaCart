<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FreeShipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'minimum_amount',
        'type',
        'category_id',
        'product_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'minimum_amount' => 'decimal:2',
    ];

    /**
     * Relationship to category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship to product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for active free shipping
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Check if a product qualifies for free shipping
     */
    public static function hasProductFreeShipping($productId, $categoryId = null)
    {
        return self::where('is_active', true)
            ->where(function($q) use ($productId, $categoryId) {
                // Check by product ID
                $q->where(function($q2) use ($productId) {
                    $q2->where('type', 'product')->where('product_id', $productId);
                });

                // Check by category ID
                if ($categoryId) {
                    $q->orWhere(function($q2) use ($categoryId) {
                        $q2->where('type', 'category')->where('category_id', $categoryId);
                    });
                }
            })
            ->exists();
    }
}