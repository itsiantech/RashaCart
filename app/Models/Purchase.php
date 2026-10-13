<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'supplier_id',
        'purchase_date',
        'delivery_date',
        'total_quantity',
        'purchase_cost',
        'delivery_cost',
        'net_total',
        'paid_amount',
        'due_amount',
        'payment_status',
        'note',
        'status',
        'created_by',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'delivery_date' => 'date',
        'total_quantity' => 'decimal:2',
        'purchase_cost' => 'decimal:2',
        'delivery_cost' => 'decimal:2',
        'net_total' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function ledger()
    {
        return $this->hasOne(SupplierLedger::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
