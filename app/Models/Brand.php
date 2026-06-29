<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cache;

class Brand extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'is_active',
        'sort_order'
    ];

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    public function products() {
        return $this->hasMany(Product::class);
    }

    // Automatic slug generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            if (empty($brand->slug)) {
                $brand->slug = Str::slug($brand->name);
            }
        });
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('brands');
            Cache::forget('global_cache_ready');
        });

        static::deleted(function () {
            Cache::forget('brands');
            Cache::forget('global_cache_ready');
        });
    }
}
