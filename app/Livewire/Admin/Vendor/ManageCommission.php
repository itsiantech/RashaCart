<?php

namespace App\Livewire\Admin\Vendor;

use App\Models\Category;
use App\Models\CategoryCommission;
use Livewire\Component;


class ManageCommission extends Component
{
    public $commissions = [];

    public function mount()
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            $rate = CategoryCommission::where('category_id', $category->id)
                ->value('commission_rate');

            $this->commissions[$category->id] = $rate ?? 0;
        }
    }

    public function save()
    {
        $this->validate([
            'commissions.*' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($this->commissions as $categoryId => $rate) {
            CategoryCommission::updateOrCreate(
                ['category_id' => $categoryId],
                ['commission_rate' => (float) ($rate ?? 0)]
            );
        }

        session()->flash('success', 'Commission saved successfully!');
    }



    public function render()
    {
        $categories = Category::all();
        return view('livewire.admin.vendor.manage-commission', compact('categories'))->layout('livewire.admin.layouts.app');
    }
}
