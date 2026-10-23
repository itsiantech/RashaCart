<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

/**
 * Print-friendly customer invoice. Own-order authorization enforced here.
 */
class CustomerInvoicePrintController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['items.product']);

        return view('frontend.customer.invoice-print', [
            'order' => $order,
            'auto' => $request->boolean('auto'),
        ]);
    }
}
