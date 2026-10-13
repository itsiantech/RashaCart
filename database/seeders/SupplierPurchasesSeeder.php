<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupplierPurchasesSeeder extends Seeder
{
    public function run(): void
    {
        if (Supplier::count() > 0) {
            return;
        }

        $admin = User::first();
        $adminId = $admin ? $admin->id : null;

        $s1 = Supplier::create([
            'name' => 'Marvel Fabric Suppliers',
            'company' => 'Marvel Textiles Ltd',
            'phone' => '01711-234567',
            'email' => 'marvel@example.com',
            'address' => 'Narayanganj, Dhaka',
            'opening_balance' => 0,
            'total_due' => 33500,
            'status' => 'active',
        ]);

        $s2 = Supplier::create([
            'name' => 'Attica Accessories Ltd',
            'company' => 'Attica Group',
            'phone' => '01822-345678',
            'email' => 'attica@example.com',
            'address' => 'Gazipur, Dhaka',
            'opening_balance' => 0,
            'total_due' => 0,
            'status' => 'active',
        ]);

        $p = Purchase::create([
            'invoice_no' => 'PUR-20261001-001',
            'supplier_id' => $s1->id,
            'purchase_date' => '2026-10-01',
            'delivery_date' => '2026-10-01',
            'total_quantity' => 200,
            'purchase_cost' => 180000,
            'delivery_cost' => 3500,
            'net_total' => 183500,
            'paid_amount' => 150000,
            'due_amount' => 33500,
            'payment_status' => 'partial',
            'note' => 'Initial Cotton Roll delivery',
            'status' => 'received',
            'created_by' => $adminId,
        ]);

        PurchaseItem::create([
            'purchase_id' => $p->id,
            'product_id' => null,
            'product_name' => 'Cotton Roll',
            'specs' => 'Cotton Roll (50 Kg / Roll)',
            'quantity' => 200,
            'unit_cost' => 900,
            'total_cost' => 180000,
        ]);

        SupplierLedger::create([
            'supplier_id' => $s1->id,
            'purchase_id' => $p->id,
            'date' => '2026-10-01',
            'invoice_no' => 'PUR-20261001-001',
            'product_specs' => 'Cotton Roll (50 Kg / Roll)',
            'total_qty' => 200,
            'purchase_cost' => 180000,
            'delivery_cost' => 3500,
            'paid_amount' => 150000,
            'due_amount' => 33500,
            'balance' => 33500,
            'type' => 'purchase',
            'status' => 'active',
            'note' => 'Sample initial ledger record',
            'created_by' => $adminId,
        ]);
    }
}
