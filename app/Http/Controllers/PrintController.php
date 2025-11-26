<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PrintController extends Controller
{
    public function print(Order $order)
    {
        $order->load(['items.product', 'items.variant.variantAttributeValues.attribute', 'items.variant.variantAttributeValues.attributeValue']);
        
        return view('print.invoice-print', compact('order'));
    }
}
