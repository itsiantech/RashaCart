<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LandingPage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'product_ids',
        'main_title',
        'hero_image',
        'countdown_end_date',
        'regular_price',
        'discount_price',
        'benefits_title',
        'benefits_list',
        'benefits_icon',
        'trust_title',
        'trust_list',
        'trust_icon',
        'secondary_image',
        'contact_number',
        'status',
        'show_hero',
        'show_countdown',
        'show_price',
        'show_benefits',
        'show_trust',
        'show_contact',
        'show_checkout',
        'show_footer',
    ];

    public const SECTIONS = [
        'show_hero' => 'Hero Section',
        'show_countdown' => 'Countdown Timer',
        'show_price' => 'Price Block',
        'show_benefits' => 'Benefits List',
        'show_trust' => 'Trust List',
        'show_contact' => 'Contact Banner',
        'show_checkout' => 'Checkout / Order Form',
        'show_footer' => 'Footer',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'benefits_list' => 'array',
        'trust_list' => 'array',
        'countdown_end_date' => 'datetime',
        'status' => 'boolean',
        'show_hero' => 'boolean',
        'show_countdown' => 'boolean',
        'show_price' => 'boolean',
        'show_benefits' => 'boolean',
        'show_trust' => 'boolean',
        'show_contact' => 'boolean',
        'show_checkout' => 'boolean',
        'show_footer' => 'boolean',
    ];

    public function getProductsAttribute()
    {
        if (empty($this->product_ids)) {
            return collect([]);
        }
        return Product::whereIn('id', $this->product_ids)->get();
    }

    public const LIST_ICONS = [
        'bx-check-circle',
        'bxs-check-circle',
        'bx-check',
        'bx-check-square',
        'bx-check-double',
        'bx-right-arrow-circle',
        'bx-right-arrow-alt',
        'bx-chevron-right-circle',
        'bx-chevron-right',
        'bx-caret-right-circle',
        'bx-plus-circle',
        'bx-star',
        'bxs-star',
        'bx-heart',
        'bx-like',
        'bx-shield',
        'bx-badge-check',
        'bx-radio-circle-marked',
        'bx-circle',
        'bx-dot-circle',
        'bx-leaf',
        'bx-gift',
        'bx-trophy',
        'bx-award',
    ];

    public function listIconClass(string $field = 'benefits_icon'): string
    {
        $fallback = $field === 'trust_icon' ? 'bx-right-arrow-circle' : 'bx-check-circle';
        $icon = $this->{$field} ?: $fallback;

        if (! in_array($icon, self::LIST_ICONS, true)) {
            $icon = $fallback;
        }

        return 'bx ' . $icon;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
