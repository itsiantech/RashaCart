<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'purchase_id',
        'date',
        'invoice_no',
        'product_specs',
        'total_qty',
        'purchase_cost',
        'delivery_cost',
        'paid_amount',
        'due_amount',
        'balance',
        'type',
        'status',
        'note',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_qty' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
