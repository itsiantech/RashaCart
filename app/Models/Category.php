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
        'is_featured'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
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



    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('categories');
            Cache::forget('global_cache_ready');
        });

        static::deleted(function () {
            Cache::forget('categories');
            Cache::forget('global_cache_ready');
        });
    }
}
