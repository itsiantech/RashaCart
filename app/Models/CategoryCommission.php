<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryCommission extends Model
{
    protected $fillable = [
        'id',
        'category_id',
        'commission_rate'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
