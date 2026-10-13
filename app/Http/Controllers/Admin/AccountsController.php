<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountsController extends Controller
{
    public function dashboard()
    {
        return view('livewire.admin.accounts.dashboard');
    }

    public function salesCategory()
    {
        return view('livewire.admin.accounts.sales_return');
    }

    public function supplierLedger(Request $request)
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('name')->get();
        $products = Product::select('id', 'name', 'buying_price', 'stock')
            ->where('status', 1)
            ->orWhereNull('status')
            ->orderBy('name')
            ->get();

        $query = SupplierLedger::with(['supplier', 'purchase.items.product'])
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('period')) {
            if ($request->period === 'weekly') {
                $query->where('date', '>=', Carbon::now()->subDays(7)->toDateString());
            } elseif ($request->period === 'monthly') {
                $query->where('date', '>=', Carbon::now()->startOfMonth()->toDateString());
            } elseif ($request->period === 'yearly') {
                $query->where('date', '>=', Carbon::now()->startOfYear()->toDateString());
            }
        }

        if ($request->filled('keyword')) {
            $keyword = trim($request->keyword);
            $query->where(function ($q) use ($keyword) {
                $q->where('invoice_no', 'like', "%{$keyword}%")
                    ->orWhere('product_specs', 'like', "%{$keyword}%")
                    ->orWhereHas('supplier', function ($sq) use ($keyword) {
                        $sq->where('name', 'like', "%{$keyword}%")
                            ->orWhere('company', 'like', "%{$keyword}%");
                    });
            });
        }

        $ledgers = $query->paginate(20)->withQueryString();

        return view('livewire.admin.accounts.supplier_ledger', compact('ledgers', 'suppliers', 'products'));
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        $openingBalance = (float)($validated['opening_balance'] ?? 0);

        return DB::transaction(function () use ($validated, $openingBalance) {
            $supplier = Supplier::create([
                'name' => $validated['name'],
                'company' => $validated['company'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'opening_balance' => $openingBalance,
                'total_due' => $openingBalance,
                'status' => 'active',
            ]);

            if ($openingBalance > 0) {
                SupplierLedger::create([
                    'supplier_id' => $supplier->id,
                    'purchase_id' => null,
                    'date' => Carbon::now()->toDateString(),
                    'invoice_no' => 'OPENING-BAL',
                    'product_specs' => 'Opening Balance Entry',
                    'total_qty' => 0,
                    'purchase_cost' => $openingBalance,
                    'delivery_cost' => 0,
                    'paid_amount' => 0,
                    'due_amount' => $openingBalance,
                    'balance' => $openingBalance,
                    'type' => 'opening_balance',
                    'status' => 'active',
                    'note' => 'Initial supplier opening balance',
                    'created_by' => Auth::id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Supplier registered successfully!',
                'supplier' => $supplier,
            ]);
        });
    }

    public function storePurchase(Request $request)
    {
        $request->validate([
            'purchase_date' => 'required|date',
            'delivery_date' => 'nullable|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_no' => 'nullable|string|max:100',
            'delivery_cost' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:paid,partial,due',
            'paid_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'nullable|string|max:255',
            'items.*.specs' => 'nullable|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $invoiceNo = trim($request->invoice_no);
            if (empty($invoiceNo)) {
                $invoiceNo = 'PUR-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }

            // Ensure invoice uniqueness
            $counter = 1;
            $originalInvoiceNo = $invoiceNo;
            while (Purchase::where('invoice_no', $invoiceNo)->exists()) {
                $invoiceNo = $originalInvoiceNo . '-' . $counter;
                $counter++;
            }

            $totalQuantity = 0;
            $totalPurchaseCost = 0;
            $itemsData = [];
            $specsSummary = [];

            foreach ($request->items as $item) {
                $qty = (float)$item['quantity'];
                $unitCost = (float)$item['unit_cost'];
                $rowCost = $qty * $unitCost;
                $totalQuantity += $qty;
                $totalPurchaseCost += $rowCost;

                $productName = $item['product_name'] ?? null;
                $specs = $item['specs'] ?? null;

                if (!empty($item['product_id']) && empty($productName)) {
                    $prod = Product::find($item['product_id']);
                    if ($prod) {
                        $productName = $prod->name;
                    }
                }

                $desc = $specs ?: $productName;
                if (!empty($desc)) {
                    $specsSummary[] = $desc . ($qty > 1 ? " ({$qty} Pcs/Units)" : '');
                }

                $itemsData[] = [
                    'product_id' => $item['product_id'] ?? null,
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $productName,
                    'specs' => $specs,
                    'quantity' => $qty,
                    'unit_cost' => $unitCost,
                    'total_cost' => $rowCost,
                ];
            }

            $deliveryCost = (float)($request->delivery_cost ?? 0);
            $netTotal = $totalPurchaseCost + $deliveryCost;

            if ($request->payment_status === 'paid') {
                $paidAmount = $netTotal;
            } elseif ($request->payment_status === 'due') {
                $paidAmount = 0;
            } else {
                $paidAmount = min($netTotal, (float)($request->paid_amount ?? 0));
            }

            $dueAmount = max(0, $netTotal - $paidAmount);

            // 1. Create Purchase
            $purchase = Purchase::create([
                'invoice_no' => $invoiceNo,
                'supplier_id' => $request->supplier_id,
                'purchase_date' => $request->purchase_date,
                'delivery_date' => $request->delivery_date ?? $request->purchase_date,
                'total_quantity' => $totalQuantity,
                'purchase_cost' => $totalPurchaseCost,
                'delivery_cost' => $deliveryCost,
                'net_total' => $netTotal,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'payment_status' => $request->payment_status,
                'note' => $request->note,
                'status' => 'received',
                'created_by' => Auth::id(),
            ]);

            // 2. Create Purchase Items & Update Inventory
            foreach ($itemsData as $itemData) {
                $itemData['purchase_id'] = $purchase->id;
                PurchaseItem::create($itemData);

                if (!empty($itemData['product_id'])) {
                    $product = Product::lockForUpdate()->find($itemData['product_id']);
                    if ($product) {
                        DB::table('products')->where('id', $product->id)->increment('stock', $itemData['quantity']);

                        Inventory::create([
                            'product_id' => $product->id,
                            'variant_id' => $itemData['variant_id'] ?? null,
                            'type' => 'in',
                            'quantity' => $itemData['quantity'],
                            'current_stock' => $product->fresh()->stock,
                            'notes' => 'Supplier Purchase: ' . $invoiceNo . ($itemData['specs'] ? ' (' . $itemData['specs'] . ')' : ''),
                            'user_id' => Auth::id(),
                        ]);
                    }
                }
            }

            // 3. Update Supplier Total Due
            $supplier = Supplier::lockForUpdate()->findOrFail($request->supplier_id);
            $supplier->increment('total_due', $dueAmount);
            $newBalance = $supplier->fresh()->total_due;

            // 4. Create Supplier Ledger Entry
            $ledger = SupplierLedger::create([
                'supplier_id' => $supplier->id,
                'purchase_id' => $purchase->id,
                'date' => $request->purchase_date,
                'invoice_no' => $invoiceNo,
                'product_specs' => implode(', ', $specsSummary) ?: 'Purchased Goods',
                'total_qty' => $totalQuantity,
                'purchase_cost' => $totalPurchaseCost,
                'delivery_cost' => $deliveryCost,
                'paid_amount' => $paidAmount,
                'due_amount' => $dueAmount,
                'balance' => $newBalance,
                'type' => 'purchase',
                'status' => 'active',
                'note' => $request->note,
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase recorded and ledger updated successfully!',
                'purchase' => $purchase,
                'ledger' => $ledger->load('supplier'),
            ]);
        });
    }

    public function voidPurchase($id)
    {
        return DB::transaction(function () use ($id) {
            $ledger = SupplierLedger::with(['purchase.items', 'supplier'])->findOrFail($id);

            if ($ledger->status === 'voided') {
                return response()->json(['success' => false, 'message' => 'This entry is already voided.'], 400);
            }

            // Revert stock if purchase exists
            if ($ledger->purchase) {
                foreach ($ledger->purchase->items as $item) {
                    if ($item->product_id) {
                        $product = Product::lockForUpdate()->find($item->product_id);
                        if ($product) {
                            DB::table('products')->where('id', $product->id)->decrement('stock', $item->quantity);

                            Inventory::create([
                                'product_id' => $product->id,
                                'variant_id' => $item->variant_id,
                                'type' => 'out',
                                'quantity' => $item->quantity,
                                'current_stock' => max(0, $product->fresh()->stock),
                                'notes' => 'Void Purchase: ' . $ledger->purchase->invoice_no,
                                'user_id' => Auth::id(),
                            ]);
                        }
                    }
                }
                $ledger->purchase->update(['status' => 'voided']);
            }

            // Revert supplier due
            if ($ledger->due_amount > 0 && $ledger->supplier) {
                $supplier = Supplier::lockForUpdate()->find($ledger->supplier_id);
                if ($supplier) {
                    $supplier->decrement('total_due', min($supplier->total_due, $ledger->due_amount));
                }
            }

            $ledger->update(['status' => 'voided']);

            return response()->json([
                'success' => true,
                'message' => 'Transaction voided successfully and stock/dues reversed.',
            ]);
        });
    }

    public function purchaseDetails($id)
    {
        $ledger = SupplierLedger::with(['supplier', 'purchase.items.product', 'creator'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'ledger' => $ledger,
        ]);
    }

    public function officeExpenses()
    {
        return view('livewire.admin.accounts.office_expenses');
    }

    public function techCost()
    {
        return view('livewire.admin.accounts.tech_cost');
    }

    public function marketingAds()
    {
        return view('livewire.admin.accounts.marketing_ads');
    }

    public function payrollInvestment()
    {
        return view('livewire.admin.accounts.payroll_investment');
    }

    public function voidLogs()
    {
        return view('livewire.admin.accounts.void_logs');
    }

    public function exportReports()
    {
        return view('livewire.admin.accounts.export_reports');
    }
}
