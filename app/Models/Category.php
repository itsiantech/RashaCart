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

    /** Flat list of every category below this one (sub + sub-sub + ...). */
    public function descendantIds(): array
    {
        $byParent = static::query()->select('id', 'parent_id')->get()->groupBy('parent_id');

        $collect = function (int $parentId) use (&$collect, $byParent): array {
            $ids = [];
            foreach ($byParent->get($parentId, collect()) as $child) {
                $ids[] = (int) $child->id;
                $ids = array_merge($ids, $collect((int) $child->id));
            }

            return $ids;
        };

        return $collect((int) $this->id);
    }

    public function selfAndDescendantIds(): array
    {
        return array_merge([(int) $this->id], $this->descendantIds());
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
        static::saved(function () {
            static::flushCache();
            \App\Services\SeoService::clearSitemapCache();
        });
        static::deleted(fn () => static::flushCache());
    }
}
