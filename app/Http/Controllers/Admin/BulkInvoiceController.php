<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class BulkInvoiceController extends Controller
{
    /**
     * Store selected order IDs in session for bulk printing
     */
    public function store(Request $request)
    {
        $ids = $request->input('ids', []);

        // ✅ Cast to int and deduplicate
        $ids = array_values(array_unique(
            array_map('intval', array_filter($ids, 'is_numeric'))
        ));

        if (empty($ids)) {
            return redirect()->route('admin.orders.bulk-print')
                ->with('error', 'No valid orders selected for printing');
        }

        session(['bulk_invoice_ids' => $ids]);

        return redirect()->route('admin.invoices.bulk-print-view');
    }

    public function show()
    {
        $ids = session('bulk_invoice_ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.orders.bulk-print')
                ->with('error', 'Session expired. Please select orders again.');
        }

        $orders = Order::with([
            'items.product',
            'items.variant.variantAttributeValues.attribute',
            'items.variant.variantAttributeValues.attributeValue',
        ])
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->get();

        if ($orders->isEmpty()) {
            return redirect()->route('admin.orders.bulk-print')
                ->with('error', 'Selected orders not found.');
        }

        Order::whereIn('id', $ids)->update([
            'is_printed' => true,
            'printed_at' => now(),
        ]);

        session()->forget('bulk_invoice_ids');

        // ✅ Confirm you're passing a COLLECTION of orders, not a single one
        return view('admin.invoices.bulk-print', compact('orders'));
    }
}
