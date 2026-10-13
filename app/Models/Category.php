<?php

namespace App\Models;

use Cache;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'status',
        'parent_id',
        'sort_order',
        'is_featured',
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function isLeaf()
    {
        return $this->children()->count() === 0;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public static function flushCache()
    {
        Cache::forget('categories');
        Cache::forget('global_cache_ready');
    }

    protected static function booted()
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }
}
