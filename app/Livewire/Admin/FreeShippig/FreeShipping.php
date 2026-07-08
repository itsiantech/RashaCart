<?php

namespace App\Livewire\Admin\FreeShippig;

use App\Models\Category;
use App\Models\FreeShipping as FreeShippingModel;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class FreeShipping extends Component
{
    use WithPagination;

    public $search = '';

    public $sortBy = 'created_at';

    public $sortDir = 'DESC';

    public $perPage = 10;

    // Form properties
    public $showForm = false;

    public $isEditing = false;

    public $editId = null;

    public $name;

    public $description;

    public $minimum_amount;

    public $type = 'amount';

    public $category_id;

    public $product_id;

    public $is_active = true;

    public $sort_order = 0;

    // For dropdowns
    public $categories;

    public $products;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'minimum_amount' => 'nullable|numeric|min:0',
        'type' => 'required|in:amount,category,product',
        'category_id' => 'nullable|exists:categories,id',
        'product_id' => 'nullable|exists:products,id',
        'is_active' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    public function mount()
    {
        $this->loadDropdowns();
    }

    public function loadDropdowns()
    {
        $this->categories = Category::get(['id', 'name']);
        $this->products = Product::get(['id', 'name']);
    }

    public function render()
    {
        $freeShippings = FreeShippingModel::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.free-shippig.free-shipping', [
            'freeShippings' => $freeShippings,
        ])->layout('livewire.admin.layouts.app');
    }

    public function openForm($edit = false, $id = null)
    {
        if ($edit && $id) {
            $this->isEditing = true;
            $this->editId = $id;
            $freeShipping = FreeShippingModel::findOrFail($id);

            $this->name = $freeShipping->name;
            $this->description = $freeShipping->description;
            $this->minimum_amount = $freeShipping->minimum_amount;
            $this->type = $freeShipping->type;
            $this->category_id = $freeShipping->category_id;
            $this->product_id = $freeShipping->product_id;
            $this->is_active = $freeShipping->is_active;
            $this->sort_order = $freeShipping->sort_order;
        } else {
            $this->resetFormFields();
        }

        $this->showForm = true;
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetFormFields();
    }

    public function resetFormFields()
    {
        $this->reset(['name', 'description', 'minimum_amount', 'type', 'category_id', 'product_id', 'sort_order']);
        $this->type = 'amount';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->isEditing = false;
        $this->editId = null;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->isEditing) {
                $freeShipping = FreeShippingModel::findOrFail($this->editId);
                $freeShipping->update([
                    'name' => $this->name,
                    'description' => $this->description,
                    'minimum_amount' => $this->minimum_amount,
                    'type' => $this->type,
                    'category_id' => $this->type === 'category' ? $this->category_id : null,
                    'product_id' => $this->type === 'product' ? $this->product_id : null,
                    'is_active' => $this->is_active,
                    'sort_order' => $this->sort_order,
                ]);
                $this->dispatch('success', message: 'Free Shipping updated successfully!');
            } else {
                FreeShippingModel::create([
                    'name' => $this->name,
                    'description' => $this->description,
                    'minimum_amount' => $this->minimum_amount,
                    'type' => $this->type,
                    'category_id' => $this->type === 'category' ? $this->category_id : null,
                    'product_id' => $this->type === 'product' ? $this->product_id : null,
                    'is_active' => $this->is_active,
                    'sort_order' => $this->sort_order,
                ]);
                $this->dispatch('success', message: 'Free Shipping created successfully!');
            }
            $this->closeForm();
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error: '.$e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            FreeShippingModel::findOrFail($id)->delete();
            $this->dispatch('success', message: 'Free Shipping deleted successfully!');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error deleting: '.$e->getMessage());
        }
    }

    public function toggleActive($id)
    {
        try {
            $freeShipping = FreeShippingModel::findOrFail($id);
            $freeShipping->update(['is_active' => ! $freeShipping->is_active]);
            $this->dispatch('success', message: 'Status updated successfully!');
        } catch (\Exception $e) {
            $this->dispatch('error', message: 'Error: '.$e->getMessage());
        }
    }

    public function setSortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'ASC' ? 'DESC' : 'ASC';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'ASC';
        }
    }
}
