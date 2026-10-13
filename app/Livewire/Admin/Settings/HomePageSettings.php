<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Category;
use App\Models\HomepageCategory;
use App\Models\Settings;
use Livewire\Component;

class HomePageSettings extends Component
{
    public $selected_category_id;
    public $product_limit = 6;
    public $homepageCategories;
    public $allCategories;

    // Recommended For You settings
    public $recommended_title = 'Recommended For You';
    public $recommended_product_limit = 12;

    protected $rules = [
        'selected_category_id' => 'required|exists:categories,id',
        'product_limit' => 'required|integer|min:1|max:50',
        'recommended_title' => 'required|string|max:255',
        'recommended_product_limit' => 'required|integer|min:1|max:100',
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->homepageCategories = HomepageCategory::with('category')
            ->orderBy('sort_order', 'asc')
            ->get();

        $this->allCategories = Category::where('status', 1)
            ->whereNotIn('id', $this->homepageCategories->pluck('category_id'))
            ->orderBy('name')
            ->get();

        // Load Recommended For You settings
        $this->recommended_title = setting('recommended_title') ?? 'Recommended For You';
        $this->recommended_product_limit = setting('recommended_product_limit') ?? 12;
    }

    public function addCategory()
    {
        $this->validate();

        $maxSortOrder = HomepageCategory::max('sort_order') ?? 0;

        HomepageCategory::create([
            'category_id' => $this->selected_category_id,
            'product_limit' => $this->product_limit,
            'sort_order' => $maxSortOrder + 1,
            'is_active' => true,
        ]);

        $this->selected_category_id = null;
        $this->product_limit = 6;

        $this->loadData();

        $this->dispatch('success', message: 'Category added to homepage successfully!');
    }

    public function updateProductLimit($id, $limit)
    {
        $homepageCategory = HomepageCategory::findOrFail($id);
        $homepageCategory->update(['product_limit' => $limit]);

        $this->dispatch('success', message: 'Product limit updated!');
    }

    public function toggleActive($id)
    {
        $homepageCategory = HomepageCategory::findOrFail($id);
        $homepageCategory->update(['is_active' => !$homepageCategory->is_active]);

        $this->dispatch('success', message: 'Status updated!');
    }

    public function moveUp($id)
    {
        $homepageCategory = HomepageCategory::findOrFail($id);
        $aboveItem = HomepageCategory::where('sort_order', '<', $homepageCategory->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($aboveItem) {
            $tempOrder = $homepageCategory->sort_order;
            $homepageCategory->sort_order = $aboveItem->sort_order;
            $aboveItem->sort_order = $tempOrder;

            $homepageCategory->save();
            $aboveItem->save();

            $this->loadData();
        }
    }

    public function moveDown($id)
    {
        $homepageCategory = HomepageCategory::findOrFail($id);
        $belowItem = HomepageCategory::where('sort_order', '>', $homepageCategory->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($belowItem) {
            $tempOrder = $homepageCategory->sort_order;
            $homepageCategory->sort_order = $belowItem->sort_order;
            $belowItem->sort_order = $tempOrder;

            $homepageCategory->save();
            $belowItem->save();

            $this->loadData();
        }
    }

    public function delete($id)
    {
        $homepageCategory = HomepageCategory::findOrFail($id);
        $homepageCategory->delete();

        $this->reorderSort();

        $this->loadData();

        $this->dispatch('success', message: 'Category removed from homepage!');
    }

    private function reorderSort()
    {
        $categories = HomepageCategory::orderBy('sort_order', 'asc')->get();
        foreach ($categories as $index => $category) {
            $category->update(['sort_order' => $index + 1]);
        }
    }

    public function saveRecommendedSettings()
    {
        $this->validate([
            'recommended_title' => 'required|string|max:255',
            'recommended_product_limit' => 'required|integer|min:1|max:100',
        ]);

        Settings::updateOrCreate(['key' => 'recommended_title'], ['value' => $this->recommended_title]);
        Settings::updateOrCreate(['key' => 'recommended_product_limit'], ['value' => $this->recommended_product_limit]);

        Settings::clearCache();

        $this->dispatch('success', message: 'Recommended For You settings updated!');
    }

    public function render()
    {
        return view('livewire.admin.settings.home-page-settings')
            ->layout('livewire.admin.layouts.app');
    }
}
