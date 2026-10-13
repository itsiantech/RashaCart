<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Admin\Products\Concerns\ManagesImageOptions;
use App\Livewire\Admin\Products\Concerns\ManagesProductImages;
use App\Livewire\Admin\Products\Concerns\ManagesProductVariants;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCreate extends Component
{
    use WithFileUploads, ManagesProductVariants, ManagesProductImages, ManagesImageOptions;

    public $has_attributes = true;

    public $has_image_options = false;

    public $require_image_option = true;

    public $attrRequired = [];

    public $name;

    public $slug;

    public $sku;

    public $short_description;

    public $long_description;

    public $buying_price;

    public $price;

    public $discount_type;

    public $discount_price;

    public $stock;

    public $sale_price;

    public $category_id;

    public $brand_id;

    public $thumbnail;

    public $video;

    public $status = true;

    public $images = [];

    public $uploadedImages = []; // permanent storage

    public $meta_title;

    public $meta_description;

    public $meta_keywords;

    public $meta_tags;

    public $productAttributes = [];

    public $selectedAttributes = [];

    public $variants = [];

    public $variantCounter = 0;

    public $showVariantForm = false;

    public $variantSystemEnabled = true;

    public $newVariantImage;

    public $variantErrors = [];

    public $is_featured = false;

    public bool $is_preorder = false;

    public ?string $preorder_start_date = null;

    public ?string $preorder_end_date = null;

    public ?string $expected_release_date = null;

    public $subcategory_id;

    public $subcategories = [];

    public $newVariant = [
        'attributes' => [],
        'sku' => '',
        'variant_price' => null,
        'stock' => 0,
        'image' => null,
    ];

    public function mount()
    {
        $this->variantSystemEnabled = (bool) setting('attribute_system', true);
        $this->productAttributes = Attribute::with('values')->get();
        $this->slug = '';
        $this->sku = 'SKU-'.strtoupper(Str::random(6));
        $this->calculateSalePrice();
    }

    // ----------------------- Price Calculations -----------------------
    public function updatedDiscountType($value)
    {
        $this->calculateSalePrice();
    }

    public function updatedPrice()
    {
        $this->calculateSalePrice();
    }

    public function updatedDiscountPrice()
    {
        $this->calculateSalePrice();
    }

    public function calculateSalePrice()
    {
        if ($this->discount_type && $this->discount_price) {
            if ($this->discount_type === 'fixed') {
                $this->sale_price = max(0, $this->price - $this->discount_price);
            } elseif ($this->discount_type === 'percentage') {
                $this->sale_price = max(0, $this->price - $this->price * ($this->discount_price / 100));
            }
        } else {
            $this->sale_price = $this->price;
        }
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);

        // Auto-fill SEO fields only if they are currently empty
        if (empty($this->meta_title)) {
            $this->meta_title = $value;
        }

        if (empty($this->meta_description)) {
            $this->meta_description = $value;
        }
    }

    // ----------------------- Variant Methods -----------------------
    public function openVariantForm()
    {
        $this->newVariant = [
            'attributes' => [],
            'sku' => 'SKU-'.strtoupper(Str::random(6)),
            'variant_price' => $this->sale_price ?? 0,
            'stock' => 0,
            'image' => null,
        ];

        $this->selectedAttributes = [];
        $this->variantErrors = [];
        $this->showVariantForm = true;
        $this->newVariantImage = null;
    }

    public function addAttributeToVariant($attributeId)
    {
        if (! in_array($attributeId, $this->selectedAttributes)) {
            $this->selectedAttributes[] = $attributeId;
            $this->newVariant['attributes'][$attributeId] = '';
            $this->variantErrors = [];
        }
    }

    public function removeAttributeFromVariant($index)
    {
        if (isset($this->selectedAttributes[$index])) {
            $attributeId = $this->selectedAttributes[$index];
            unset($this->selectedAttributes[$index]);
            unset($this->newVariant['attributes'][$attributeId]);
            $this->selectedAttributes = array_values($this->selectedAttributes);
            $this->variantErrors = [];
        }
    }

    public function addVariant()
    {
        if (! $this->validateVariant()) {
            return;
        }

        // Check duplicate
        if ($this->variantExists()) {
            $this->variantErrors['duplicate'] = 'A variant with these attributes already exists.';

            return;
        }

        $variant = [
            'id' => 'new_'.++$this->variantCounter,
            'sku' => $this->newVariant['sku'],
            'variant_price' => (float) $this->newVariant['variant_price'],
            'stock' => (int) $this->newVariant['stock'],
            'values' => [],
            'image' => null,
            'new_image_file' => $this->newVariantImage,
        ];

        foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
            if (! empty($valueId)) {
                $variant['values'][] = [
                    'attribute_id' => $attrId,
                    'attribute_value_id' => $valueId,
                ];
            }
        }

        $this->variants[] = $variant;
        $this->resetVariantForm();
    }

    protected function validateVariant()
    {
        $this->variantErrors = [];

        if (count($this->selectedAttributes) === 0) {
            $this->variantErrors['attributes'] = 'Please select at least one attribute.';
        }

        $missingValues = [];
        foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
            if (empty($valueId)) {
                $attribute = Attribute::find($attrId);
                $missingValues[] = $attribute->name;
            }
        }

        if (! empty($missingValues)) {
            $this->variantErrors['attribute_values'] = 'Please select values for: '.implode(', ', $missingValues);
        }

        if (empty($this->newVariant['sku'])) {
            $this->variantErrors['sku'] = 'SKU is required.';
        } else {
            foreach ($this->variants as $variant) {
                if ($variant['sku'] === $this->newVariant['sku']) {
                    $this->variantErrors['sku'] = 'This SKU is already used in another variant.';
                    break;
                }
            }
        }

        if (! is_numeric($this->newVariant['variant_price'])) {
            $this->variantErrors['variant_price'] = 'Variant price must be a number.';
        }

        if (! is_numeric($this->newVariant['stock']) || $this->newVariant['stock'] < 0) {
            $this->variantErrors['stock'] = 'Stock must be a non-negative number.';
        }

        if (! isset($this->newVariant['stock']) || ! is_numeric($this->newVariant['stock']) || $this->newVariant['stock'] < 0) {
            $this->variantErrors['stock'] = 'Stock must be a non-negative number.';
        }

        return empty($this->variantErrors);
    }

    protected function variantExists()
    {
        foreach ($this->variants as $variant) {
            if (count($variant['values']) !== count($this->newVariant['attributes'])) {
                continue;
            }

            $match = true;
            foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
                $found = false;
                foreach ($variant['values'] as $v) {
                    if ($v['attribute_id'] == $attrId && $v['attribute_value_id'] == $valueId) {
                        $found = true;
                        break;
                    }
                }
                if (! $found) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                return true;
            }
        }

        return false;
    }

    public function removeVariant($index)
    {
        if (isset($this->variants[$index])) {
            unset($this->variants[$index]);
            $this->variants = array_values($this->variants);
        }
    }

    public function resetVariantForm()
    {
        $this->newVariant = [
            'attributes' => [],
            'sku' => '',
            'variant_price' => 0,
            'stock' => 0,
            'image' => null,
        ];
        $this->newVariantImage = null;
        $this->selectedAttributes = [];
        $this->showVariantForm = false;
        $this->variantErrors = [];
    }

    public function cancelVariantForm()
    {
        $this->resetVariantForm();
    }

    // ----------------------- Save Product -----------------------
    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_tags' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'short_description' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'thumbnail' => 'required|image|max:7048',
            'video' => 'nullable|string|max:255',
            'discount_type' => 'nullable|string|in:fixed,percentage|required_with:discount_price',
            'discount_price' => 'nullable|numeric|min:0|required_with:discount_type',
            'images.*' => 'nullable|image|max:7048',
            'imageUploads.*' => 'nullable|image|max:7048',
            'is_preorder' => 'boolean',
            'preorder_start_date' => 'nullable|date|required_if:is_preorder,true',
            'preorder_end_date' => 'nullable|date|after_or_equal:preorder_start_date',
            'expected_release_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $useAttributes = $this->variantSystemEnabled && $this->has_attributes;
            $useOptions = $this->has_image_options;

            // Server-side validation for variant rows (Bangla messages)
            if ($useAttributes && ! empty($this->variants)) {
                $rowErrors = $this->validateVariantRows();
                if (! empty($rowErrors)) {
                    DB::rollBack();
                    $this->dispatch('error', message: collect($rowErrors)->flatten()->implode(' '));

                    return;
                }
            }

            // Server-side validation for image options (Bangla messages)
            if ($useOptions && ! empty($this->imageOptions)) {
                $optErrors = $this->validateImageOptions();
                if (! empty($optErrors)) {
                    DB::rollBack();
                    $this->dispatch('error', message: implode(' ', $optErrors));

                    return;
                }
            }

            $product = Product::create([
                'user_id' => auth()->id(),
                'name' => $this->name,
                'sku' => $this->sku,
                'slug' => $this->slug,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'meta_tags' => $this->meta_tags,
                'short_description' => $this->short_description,
                'long_description' => $this->long_description,
                'buying_price' => $this->buying_price,
                'price' => $this->price,
                'discount_type' => $this->discount_type ?: null,
                'discount_price' => $this->discount_price,
                'sale_price' => $this->sale_price,
                'category_id' => $this->category_id,
                'brand_id' => $this->brand_id,
                'video' => $this->video,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'has_variant' => $useAttributes && ! empty($this->variants),
                'has_attributes' => $useAttributes,
                'has_image_options' => $useOptions && ! empty($this->imageOptions),
                'require_image_option' => (bool) $this->require_image_option,
                'is_preorder' => $this->is_preorder,
                'preorder_start_date' => $this->is_preorder ? $this->preorder_start_date : null,
                'preorder_end_date' => $this->is_preorder ? $this->preorder_end_date : null,
                'expected_release_date' => $this->is_preorder ? $this->expected_release_date : null,
            ]);

            if ($this->thumbnail) {
                $path = $this->thumbnail->store('products/thumbnails', 'public');
                $product->thumbnail = $path;
                $product->save();
            }

            if (! $product->has_variant) {
                Inventory::create([
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'type' => 'in',
                    'quantity' => $this->stock,
                    'current_stock' => $this->stock,
                    'notes' => 'Initial stock for product',
                    'user_id' => auth()->id(),
                ]);
            }

            // Variants persist only when the attributes layer is ON (else simple product)
            foreach ($useAttributes ? $this->variants : [] as $i => $variantData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'stock' => $variantData['stock'] ?? 0,
                    'sku' => $variantData['sku'],
                    'variant_price' => $this->nullIfEmpty($variantData['variant_price'] ?? null),
                    'sale_price' => $this->nullIfEmpty($variantData['sale_price'] ?? null),
                    'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                    'is_active' => $variantData['is_active'] ?? true,
                    'is_default' => $variantData['is_default'] ?? false,
                    'image' => null,
                ]);

                if (isset($variantData['new_image_file']) && $variantData['new_image_file']) {
                    $path = $variantData['new_image_file']->store('products/variants', 'public');
                    $variant->image = $path;
                    $variant->save();
                }

                foreach ($variantData['values'] as $value) {
                    VariantAttributeValue::create([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $value['attribute_id'],
                        'attribute_value_id' => $value['attribute_value_id'],
                    ]);
                }

                Inventory::create([
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'type' => 'in',
                    'quantity' => $variantData['stock'] ?? 0,
                    'current_stock' => $variantData['stock'] ?? 0,
                    'notes' => 'Initial stock for variant',
                    'user_id' => auth()->id(),
                ]);

                // Wholesale slabs for this row
                $this->syncVariantSlabs($variant, $this->variantSlabs[$this->rowKey($variantData, $i)] ?? []);
            }

            // Link product ↔ attributes used by its variants
            $attrIds = collect($this->variants)
                ->flatMap(fn ($v) => array_column($v['values'] ?? [], 'attribute_id'))
                ->unique()->values()->all();
            if ($useAttributes && ! empty($attrIds)) {
                $sync = [];
                foreach ($attrIds as $attrId) {
                    $sync[$attrId] = ['is_required' => (bool) ($this->attrRequired[$attrId] ?? true)];
                }
                $product->productAttributes()->sync($sync);
            }

            $galleryPaths = [];
            foreach ($this->uploadedImages as $img) {
                $galleryPaths[] = $img->store('products/images', 'public');
            }

            if (! empty($galleryPaths)) {
                $product->gallery_image = $galleryPaths;
                $product->save();
            }

            // Persist swatch-linked gallery
            $this->persistImageLinks($product->id);

            // Persist image options layer
            if ($useOptions && ! empty($this->imageOptions)) {
                foreach ($this->persistImageOptions($product->id) as $warning) {
                    $this->dispatch('warning', message: $warning);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', ['message' => $e->getMessage()]);

            return;
        }

        $this->dispatch('success', message: 'Product Created Successfully!');

        return redirect()->route('admin.product.index');
    }

    public function updatedImages()
    {
        foreach ($this->images as $img) {
            $this->uploadedImages[] = $img;
        }
        $this->images = []; // reset input
    }

    public function removeImage($index)
    {
        array_splice($this->uploadedImages, $index, 1);
    }

    public function updatedCategoryId($value)
    {
        $this->subcategory_id = null;

        $this->subcategories = Category::where('parent_id', $value)->get();
    }

    public function render()
    {
        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->get();

        return view('livewire.admin.products.product-create', [
            'categories' => $categories,
            'brands' => Brand::all(),
        ])->layout('livewire.admin.layouts.app');
    }
}
