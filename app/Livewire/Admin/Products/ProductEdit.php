<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use DB;
use Storage;

class ProductEdit extends Component
{
    use WithFileUploads;

    public $productId;

    // Basic fields
    public $name, $slug, $short_description, $long_description, $buying_price;
    public $price, $discount_type, $discount_price;
    public $category_id, $thumbnail, $video, $status, $is_featured;
    public $images = []; // new uploads
    public $existingImages = []; // old gallery images

    // Variant system
    public $productAttributes = [];
    public $selectedAttributes = [];
    public $variants = [];
    public $variantCounter = 0;
    public $showVariantForm = false;
    public $variantSystemEnabled = true;

    public $newVariantImage;
    public $variantErrors = [];
    public $sale_price = 0;
    public $newVariant = [
        'attributes' => [],
        'sku' => '',
        'varient_price' => 0,
        'image' => null,
    ];

    public function mount($id)
    {
        $product = Product::with(['variants.values', 'variants.inventories'])->findOrFail($id);

        $this->productId = $product->id;

        // Basic fields
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->short_description = $product->short_description;
        $this->long_description = $product->long_description;
        $this->buying_price = $product->buying_price;
        $this->price = $product->price;
        $this->discount_type = $product->discount_type;
        $this->discount_price = $product->discount_price;
        $this->category_id = $product->category_id;
        $this->video = $product->video;
        $this->status = $product->status ? true : false;
        $this->is_featured = $product->is_featured ? true : false;
        $this->existingImages = $product->gallery_image ?? [];

        // Load attributes and variants
        $this->productAttributes = Attribute::with('values')->get();

        $this->variants = $product->variants
            ->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'varient_price' => $variant->varient_price,
                    'values' => $variant->values
                        ->map(function ($v) {
                            return [
                                'attribute_id' => $v->attribute_id,
                                'attribute_value_id' => $v->attribute_value_id,
                            ];
                        })
                        ->toArray(),
                    'image' => $variant->image,
                    'new_image_file' => null,
                ];
            })
            ->toArray();

        $this->generateSku();
        $this->calculateSalePrice();
    }

    public function updatedPrice()
    {
        $this->calculateSalePrice();
    }

    public function updatedDiscountType($value)
    {
        if ($value === '' || $value === null) {
            $this->discount_price = null;
        }
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
    }

    public function generateSku()
    {
        $this->newVariant['sku'] = 'SKU-' . strtoupper(Str::random(6));
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
        if (!$this->validateVariant()) {
            return;
        }

        // Check if variant with same attributes already exists
        if ($this->variantExists()) {
            $this->variantErrors['duplicate'] = 'A variant with these attributes already exists.';
            return;
        }

        $variant = [
            'id' => 'new_' . ++$this->variantCounter,
            'sku' => $this->newVariant['sku'],
            'varient_price' => (float) $this->newVariant['varient_price'],
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
        if (!is_numeric($this->newVariant['varient_price'])) {
            $this->variantErrors['varient_price'] = 'Price adjustment must be a number.';
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
            // If it's an existing variant (not new), mark for deletion
            if (is_numeric($this->variants[$index]['id'])) {
                $this->variants[$index]['_delete'] = true;
            } else {
                unset($this->variants[$index]);
                $this->variants = array_values($this->variants);
            }
        }
    }

    public function resetVariantForm()
    {
        $this->newVariant = [
            'attributes' => [],
            'sku' => '',
            'varient_price' => 0,
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

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:products,slug,' . $this->productId,
            'price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:fixed,percentage|required_with:discount_price',
            'discount_price' => 'nullable|numeric|min:0|required_with:discount_type',
            'category_id' => 'required|exists:categories,id',
            'thumbnail' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($this->productId);

            $product->update([
                'name' => $this->name,
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

            // Save new gallery images
            $galleryPaths = $this->existingImages;
            foreach ($this->images as $img) {
                $path = $img->store('products/images', 'public');
                $galleryPaths[] = $path;
            }

            $product->gallery_image = $galleryPaths;
            $product->save();

            // Handle variants - Update existing and create new ones
            $existingVariantIds = [];

            foreach ($this->variants as $variantData) {
                // Skip variants marked for deletion
                if (isset($variantData['_delete']) && $variantData['_delete']) {
                    if (is_numeric($variantData['id'])) {
                        ProductVariant::find($variantData['id'])->delete();
                    }
                    continue;
                }

                if (is_numeric($variantData['id'])) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantData['id']);
                    $variant->update([
                        'sku' => $variantData['sku'],
                        'varient_price' => $variantData['varient_price'],
                    ]);
                } else {
                    // Create new variant
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $variantData['sku'],
                        'varient_price' => $variantData['varient_price'],
                    ]);
                }

                // Handle variant image
                if (isset($variantData['new_image_file']) && $variantData['new_image_file']) {
                    $path = $variantData['new_image_file']->store('products/variants', 'public');
                    $variant->image = $path;
                    $variant->save();
                } elseif (!empty($variantData['image']) && is_numeric($variantData['id'])) {
                    $variant->image = $variantData['image'];
                    $variant->save();
                }

                // Delete existing attribute values and create new ones
                if (is_numeric($variantData['id'])) {
                    VariantAttributeValue::where('product_variant_id', $variant->id)->delete();
                }

                foreach ($variantData['values'] as $value) {
                    VariantAttributeValue::create([
                        'product_variant_id' => $variant->id,
                        'attribute_id' => $value['attribute_id'],
                        'attribute_value_id' => $value['attribute_value_id'],
                    ]);
                }

                $existingVariantIds[] = $variant->id;
            }

            // Delete variants that were removed
            ProductVariant::where('product_id', $product->id)->whereNotIn('id', $existingVariantIds)->delete();

            DB::commit();
            $this->dispatch('success', message: 'Product Updated Successfully!');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('error', message: $e->getMessage());
        }
    }

    public function deleteImage($index)
    {
        if (isset($this->existingImages[$index])) {
            $path = $this->existingImages[$index];
            Storage::delete('public/' . $path);
            unset($this->existingImages[$index]);
            $this->existingImages = array_values($this->existingImages);

            $product = Product::find($this->productId);
            if ($product) {
                $product->gallery_image = $this->existingImages;
                $product->save();
            }

            $this->dispatch('success', message: 'Image Deleted!');
        }
    }

    public function render()
    {
        return view('livewire.admin.products.product-edit', [
            'categories' => Category::whereDoesntHave('children')->get(),
        ])->layout('livewire.admin.layouts.app');
    }
}
