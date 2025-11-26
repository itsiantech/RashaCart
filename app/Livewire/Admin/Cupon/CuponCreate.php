<?php

namespace App\Livewire\Admin\Cupon;

use Livewire\Component;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;

class CuponCreate extends Component
{
    public $coupon_id;
    public $code, $discount_type, $discount_value, $min_order_amount, $max_discount_amount, $start_date, $end_date, $usage_limit, $status;
    public $selected_products = [];
    public $selected_categories = [];

    public function mount($coupon_id = null)
    {
        if ($coupon_id) {
            $coupon = Coupon::with(['products', 'categories'])->findOrFail($coupon_id);
            $this->coupon_id = $coupon->id;
            $this->code = $coupon->code;
            $this->discount_type = $coupon->discount_type;
            $this->discount_value = $coupon->discount_value;
            $this->min_order_amount = $coupon->min_order_amount;
            $this->max_discount_amount = $coupon->max_discount_amount;
            $this->start_date = date('Y-m-d', strtotime($coupon->start_date));
            $this->end_date = date('Y-m-d', strtotime($coupon->end_date));

            $this->usage_limit = $coupon->usage_limit;
            $this->status = $coupon->status;
            $this->selected_products = $coupon->products->pluck('id')->toArray();
            $this->selected_categories = $coupon->categories->pluck('id')->toArray();
        }
    }

    public function save()
    {
        
        $validated = $this->validate([
            'code' => 'required|unique:coupons,code,' . $this->coupon_id,
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:0',
            
        ]);       

       
        $coupon = Coupon::updateOrCreate(
            ['id' => $this->coupon_id],
            $validated
        );

        // Sync products and categories
        $coupon->products()->sync($this->selected_products);
        $coupon->categories()->sync($this->selected_categories);
      
        $this->dispatch('success', message: 'Coupon saved successfully.');
       
        $this->resetInputs();
    }

    public function resetInputs()
    {
        $this->coupon_id = null;
        $this->code = '';
        $this->discount_type = '';
        $this->discount_value = '';
        $this->min_order_amount = '';
        $this->max_discount_amount = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->usage_limit = '';
        $this->status = 1;
        $this->selected_products = [];
        $this->selected_categories = [];
    }

    public function render()
    {
        return view('livewire.admin.cupon.cupon-create', [
            'products' => Product::all(),
            'categories' => Category::all()
        ])->layout('livewire.admin.layouts.app');
    }
}
