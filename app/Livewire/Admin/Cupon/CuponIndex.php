<?php

namespace App\Livewire\Admin\Cupon;

use Livewire\Component;
use App\Models\Coupon;

class CuponIndex extends Component
{
    public $search = '';
    public $coupons;

    protected $listeners = ['couponAdded' => 'loadCoupons', 'couponDeleted' => 'loadCoupons', 'delete' => 'delete'];

    public function mount()
    {
        $this->loadCoupons();
    }

    public function loadCoupons()
    {
        $this->coupons = Coupon::with(['products', 'categories'])
            ->where('code', 'like', '%'.$this->search.'%')
            ->orWhereHas('products', function($q){
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->orWhereHas('categories', function($q){
                $q->where('name', 'like', '%'.$this->search.'%');
            })
            ->get();
    }

  
    public function delete($id)
    {
        if (!auth()->user()->can('banner.delete')) {
            $this->dispatch('toast', type: 'error', message: 'You do not have permission to delete banners!');
            return;
        }
        $banner = Cupon::findOrFail($id);       
        $banner->delete();

       $this->dispatch('success', message: 'Banner Deleted!');
        return;
    }

    public function render()
    {
        return view('livewire.admin.cupon.cupon-index')->layout('livewire.admin.layouts.app');
    }
}
