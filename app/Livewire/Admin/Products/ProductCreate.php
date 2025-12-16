<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Inventory;
use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use DB;

class ProductCreate extends Component
{
    use WithFileUploads;

    public $name, $slug, $sku, $short_description, $long_description, $buying_price;
    public $price, $discount_type, $discount_price, $stock;
    public $sale_price;
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
        'varient_price' => null,
        'stock' => 0,
        'image' => null,
    ];

    public function mount()
    {
        $this->variantSystemEnabled = setting('attribute_system');
        $this->productAttributes = Attribute::with('values')->get();
        $this->slug = '';
        $this->sku = 'SKU-' . strtoupper(Str::random(6));
        $this->calculateSalePrice();
    }

    // ----------------------- Price Calculations -----------------------
    public function updatedDiscountType($value) { $this->calculateSalePrice(); }
    public function updatedPrice() { $this->calculateSalePrice(); }
    public function updatedDiscountPrice() { $this->calculateSalePrice(); }

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
    }

    // ----------------------- Variant Methods -----------------------
    public function openVariantForm()
    {
        $this->newVariant = [
            'attributes' => [],
            'sku' => 'SKU-' . strtoupper(Str::random(6)),
            'varient_price' => $this->sale_price ?? 0,
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
        if (!in_array($attributeId, $this->selectedAttributes)) {
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
        if (!$this->validateVariant()) return;

        // Check duplicate
        if ($this->variantExists()) {
            $this->variantErrors['duplicate'] = 'A variant with these attributes already exists.';
            return;
        }

        $variant = [
            'id' => 'new_' . ++$this->variantCounter,
            'sku' => $this->newVariant['sku'],
            'varient_price' => (float)$this->newVariant['varient_price'],
            'stock' => (int)$this->newVariant['stock'],
            'values' => [],
            'image' => null,
            'new_image_file' => $this->newVariantImage,
        ];

        foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
            if (!empty($valueId)) {
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

        if (!empty($missingValues)) {
            $this->variantErrors['attribute_values'] = 'Please select values for: ' . implode(', ', $missingValues);
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

        if (!is_numeric($this->newVariant['varient_price'])) {
            $this->variantErrors['varient_price'] = 'Variant price must be a number.';
        }

        if (!is_numeric($this->newVariant['stock']) || $this->newVariant['stock'] < 0) {
            $this->variantErrors['stock'] = 'Stock must be a non-negative number.';
        }

        return empty($this->variantErrors);
    }

    protected function variantExists()
    {
        foreach ($this->variants as $variant) {
            if (count($variant['values']) !== count($this->newVariant['attributes'])) continue;

            $match = true;
            foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
                $found = false;
                foreach ($variant['values'] as $v) {
                    if ($v['attribute_id'] == $attrId && $v['attribute_value_id'] == $valueId) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) { $match = false; break; }
            }

            if ($match) return true;
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
            'varient_price' => 0,
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
            'price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'short_description' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'required|image|max:7048',
            'video' => 'nullable|string|max:255',
            'discount_type' => 'nullable|string|in:fixed,percentage|required_with:discount_price',
            'discount_price' => 'nullable|numeric|min:0|required_with:discount_type',
            'images.*' => 'nullable|image|max:7048',
        ]);

        DB::beginTransaction();
        try {
            $product = Product::create([
                'name' => $this->name,
                'sku' => $this->sku,
                'slug' => $this->slug,
                'short_description' => $this->short_description,
                'long_description' => $this->long_description,
                'buying_price' => $this->buying_price,
                'price' => $this->price,
                'discount_type' => $this->discount_type ?: null,
                'discount_price' => $this->discount_price,
                'sale_price' => $this->sale_price,
                'category_id' => $this->category_id,
                'video' => $this->video,
                'status' => $this->status,
                'is_featured' => $this->is_featured,
                'has_variant' => $this->variantSystemEnabled && !empty($this->variants),
            ]);

            if ($this->thumbnail) {
                $path = $this->thumbnail->store('products/thumbnails', 'public');
                $product->thumbnail = $path;
                $product->save();
            }

            if (!$product->has_variant) {
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

            foreach ($this->variants as $variantData) {
                $variant = ProductVariant::create([
                    'product_id' => $product->id,
                    'sku' => $variantData['sku'],
                    'varient_price' => $variantData['varient_price'],
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
                    'quantity' => $variantData['stock'],
                    'current_stock' => $variantData['stock'],
                    'notes' => 'Initial stock for variant',
                    'user_id' => auth()->id(),
                ]);
            }

            $galleryPaths = [];
            foreach ($this->images as $img) {
                $galleryPaths[] = $img->store('products/images', 'public');
            }

            if (!empty($galleryPaths)) {
                $product->gallery_image = $galleryPaths;
                $product->save();
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

    public function render()
    {
        return view('livewire.admin.products.product-create', [
            'categories' => Category::whereDoesntHave('children')->get(),
        ])->layout('livewire.admin.layouts.app');
    }
}
