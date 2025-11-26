<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use Cache;
use DB;

class ProductCreate extends Component
{
    use WithFileUploads;

    public $name, $slug, $short_description, $long_description, $buying_price;
    public $price, $discount_type, $discount_price, $stock;
    public $sale_price = 0;
    public $category_id;
    public $thumbnail, $video, $status = true;
    public $images = [];

    public $productAttributes = [];
    public $selectedAttributes = [];

    public $variants = [];
    public $variantCounter = 0;
    public $showVariantForm = false;
    public $variantSystemEnabled = true;

    public $newVariantImage;
    public $variantErrors = [];
    public $is_featured = false;
    


    public $newVariant = [
        'attributes' => [],
        'sku' => '',
        'price_adjustment' => 0,
        'stock' => 0,
        'image' => null,
    ];

    public function mount()
    {
        $this->productAttributes = Attribute::with('values')->get();
        $this->slug = '';
        $this->generateSku();
    }

    public function updatedDiscountType($value)
    {
        if ($value === '' || $value === null) {
            $this->discount_price = null;
        }

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
                $this->sale_price = max(0, $this->price - ($this->price * ($this->discount_price / 100)));
            }
        } else {
            $this->sale_price = $this->price;
        }
    }

     public function generateSku()
    {
        $this->newVariant['sku'] = 'SKU-' . strtoupper(Str::random(6));
    }

    public function updatedName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function addAttributeToVariant($attributeId)
    {
        if (!in_array($attributeId, $this->selectedAttributes)) {
            $this->selectedAttributes[] = $attributeId;
            $this->newVariant['attributes'][$attributeId] = '';
            $this->variantErrors = []; // Clear errors when adding attributes
        }
    }

    public function removeAttributeFromVariant($index)
    {
        if (isset($this->selectedAttributes[$index])) {
            $attributeId = $this->selectedAttributes[$index];
            unset($this->selectedAttributes[$index]);
            unset($this->newVariant['attributes'][$attributeId]);
            $this->selectedAttributes = array_values($this->selectedAttributes);
            $this->variantErrors = []; // Clear errors
        }
    }

    public function addVariant()
    {
        $this->validateVariant();

        // Check if variant with same attributes already exists
        if ($this->variantExists()) {
            $this->variantErrors['duplicate'] = 'A variant with these attributes already exists.';
            return;
        }

        $variant = [
            'id' => 'new_' . (++$this->variantCounter),
            'sku' => $this->newVariant['sku'],
            'price_adjustment' => (float) $this->newVariant['price_adjustment'],
            'stock' => (int) $this->newVariant['stock'],
            'values' => [],
            'image' => null,
            'new_image_file' => null,
        ];

        // Store attribute values
        foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
            if (!empty($valueId)) {
                $variant['values'][] = [
                    'attribute_id' => $attrId,
                    'attribute_value_id' => $valueId,
                ];
            }
        }

        // Handle image upload
        if ($this->newVariantImage) {
            $variant['new_image_file'] = $this->newVariantImage;
        }

        $this->variants[] = $variant;
        $this->resetVariantForm();
        $this->variantErrors = [];
    }

    protected function validateVariant()
    {
        $this->variantErrors = [];

        // Validate attributes
        if (count($this->selectedAttributes) === 0) {
            $this->variantErrors['attributes'] = 'Please select at least one attribute.';
        }

        // Validate attribute values
        $missingValues = [];
        foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
            if (empty($valueId)) {
                $attribute = Attribute::find($attrId);
                $missingValues[] = $attribute->name;
            }
        }
        
        if (!empty($missingValues)) {
            $this->variantErrors['attribute_values'] = 'Please select values for: ' . implode(', ', $missingValues);
        }

        // Validate SKU
        if (empty($this->newVariant['sku'])) {
            $this->variantErrors['sku'] = 'SKU is required.';
        } else {
            // Check if SKU already exists in current variants
            foreach ($this->variants as $variant) {
                if ($variant['sku'] === $this->newVariant['sku']) {
                    $this->variantErrors['sku'] = 'This SKU is already used in another variant.';
                    break;
                }
            }
        }

        // Validate price adjustment
        if (!is_numeric($this->newVariant['price_adjustment'])) {
            $this->variantErrors['price_adjustment'] = 'Price adjustment must be a number.';
        }

        // Validate stock
        if (!is_numeric($this->newVariant['stock']) || $this->newVariant['stock'] < 0) {
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
                foreach ($variant['values'] as $value) {
                    if ($value['attribute_id'] == $attrId && $value['attribute_value_id'] == $valueId) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
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
            'price_adjustment' => 0,
            'stock' => 0,
            'image' => null,
        ];
        $this->newVariantImage = null;
        $this->selectedAttributes = [];
        $this->showVariantForm = false;
        $this->generateSku();
        $this->variantErrors = [];
    }

    public function cancelVariantForm()
    {
        $this->resetVariantForm();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'short_description' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'required|image|max:2048',
            'video' => 'nullable|string|max:255',
            'discount_type'  => 'nullable|string|in:fixed,percentage|required_with:discount_price',
            'discount_price' => 'nullable|numeric|min:0|required_with:discount_type',
            'images.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'short_description' => $this->short_description,
                'long_description' => $this->long_description,
                'buying_price' => $this->buying_price,
                'price' => $this->price,
                'discount_type' => $this->discount_type ?: null,
                'discount_price' => $this->discount_price,
                'sale_price' => $this->sale_price,
                'stock' => $this->stock,
                'category_id' => $this->category_id,
                'video' => $this->video,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'has_variant' => $this->variantSystemEnabled && !empty($this->variants),
            ]);

            // Save thumbnail
            if ($this->thumbnail) {
                $path = $this->thumbnail->store('products/thumbnails', 'public');
                $product->thumbnail = $path;
                $product->save();
            }

            // Save variants
            foreach ($this->variants as $variantData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'price_diff' => $variantData['price_adjustment'],
                    'stock' => $variantData['stock'],
                    'image' => $variantData['image'] ?? null,
                ]);

                // Handle image upload for new variants
                if (isset($variantData['new_image_file']) && $variantData['new_image_file']) {
                    $path = $variantData['new_image_file']->store('products/variants', 'public');
                    $variant->image = $path;
                    $variant->save();
                }

                // Save attribute values
                foreach ($variantData['values'] as $value) {
                    VariantAttributeValue::create([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $value['attribute_id'],
                        'attribute_value_id' => $value['attribute_value_id'],
                    ]);
                }
            }

            // ✅ Save gallery images into JSON field
            $galleryPaths = [];

            foreach ($this->images as $img) {
                $path = $img->store('products/images', 'public');
                $galleryPaths[] = $path;
            }

            if (!empty($galleryPaths)) {
                $product->gallery_image = $galleryPaths;
                $product->save();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error',  ['message' => $e->getMessage()]);
            return;
        }

        
        $this->dispatch('success', message: 'Product Created Successfully!');
        return redirect()->route('admin.product.index');
    }


    public function render()
    {
        return view('livewire.admin.products.product-create', [
            'categories' => Category::whereDoesntHave('children')->get(),
        ])->layout('livewire.admin.layouts.app');
    }
}