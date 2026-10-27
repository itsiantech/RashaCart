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

    // Homepage sections (visibility + title)
    public $shop_by_category_enabled = true;
    public $shop_by_category_title = 'Shop by Category';
    public $top_brands_enabled = true;
    public $top_brands_title = 'Top Brands';
    public $flash_sale_enabled = true;
    public $flash_sale_title = 'Flash Sale';
    public $flash_sale_limit = 5;
    public $shop_by_category_limit = 8;
    public $top_brands_limit = 8;

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

        // Load homepage section visibility + titles
        $this->shop_by_category_enabled = (bool) setting('shop_by_category_enabled', 1);
        $this->shop_by_category_title = setting('shop_by_category_title', 'Shop by Category');
        $this->shop_by_category_limit = (int) setting('shop_by_category_limit', 8);
        $this->top_brands_enabled = (bool) setting('top_brands_enabled', 1);
        $this->top_brands_title = setting('top_brands_title', 'Top Brands');
        $this->top_brands_limit = (int) setting('top_brands_limit', 8);
        $this->flash_sale_enabled = (bool) setting('flash_sale_enabled', 1);
        $this->flash_sale_title = setting('flash_sale_title', 'Flash Sale');
        $this->flash_sale_limit = (int) setting('flash_sale_limit', 5);
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

    public function saveSectionSettings()
    {
        $this->validate([
            'shop_by_category_title' => 'required|string|max:100',
            'shop_by_category_limit' => 'required|integer|min:2|max:12',
            'top_brands_title' => 'required|string|max:100',
            'top_brands_limit' => 'required|integer|min:2|max:12',
            'flash_sale_title' => 'required|string|max:100',
            'flash_sale_limit' => 'required|integer|min:1|max:12',
        ]);

        $sections = [
            'shop_by_category' => [$this->shop_by_category_enabled, $this->shop_by_category_title],
            'top_brands' => [$this->top_brands_enabled, $this->top_brands_title],
            'flash_sale' => [$this->flash_sale_enabled, $this->flash_sale_title],
        ];

        foreach ($sections as $section => [$enabled, $title]) {
            Settings::updateOrCreate(['key' => $section.'_enabled'], ['value' => $enabled ? '1' : '0']);
            Settings::updateOrCreate(['key' => $section.'_title'], ['value' => trim($title)]);
        }

        Settings::updateOrCreate(['key' => 'shop_by_category_limit'], ['value' => $this->shop_by_category_limit]);
        Settings::updateOrCreate(['key' => 'top_brands_limit'], ['value' => $this->top_brands_limit]);
        Settings::updateOrCreate(['key' => 'flash_sale_limit'], ['value' => $this->flash_sale_limit]);

        Settings::clearCache();

        $this->dispatch('success', message: 'Homepage section settings updated!');
    }

    public function render()
    {
        return view('livewire.admin.settings.home-page-settings')
            ->layout('livewire.admin.layouts.app');
    }
}
