<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Admin\Products\Concerns\ManagesImageOptions;
use App\Livewire\Admin\Products\Concerns\ManagesProductImages;
use App\Livewire\Admin\Products\Concerns\ManagesProductVariants;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ImageOption;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttributeValue;
use DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Storage;

class ProductEdit extends Component
{
    use WithFileUploads, ManagesProductVariants, ManagesProductImages, ManagesImageOptions;

    public $has_attributes = true;

    public $has_image_options = false;

    public $require_image_option = true;

    public $attrRequired = [];

    public $meta_title;

    public $meta_description;

    public $meta_keywords;

    public $meta_tags;

    public $productId;

    // Basic fields
    public $name;

    public $slug;

    public $sku;

    public $short_description;

    public $long_description;

    public $buying_price;

    public $price;

    public $discount_type;

    public $discount_price;

    public $category_id;

    public $brand_id;

    public $thumbnail;

    public $video;

    public $status;

    public $is_featured;

    public $images = []; // new uploads

    public $existingImages = []; // old gallery images

    public bool $is_preorder = false;

    public ?string $preorder_start_date = null;

    public ?string $preorder_end_date = null;

    public ?string $expected_release_date = null;

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
        'variant_price' => 0,
        'stock' => 0,
        'image' => null,
    ];

    protected $listeners = ['longDescriptionUpdated' => 'setLongDescription'];

    public function setLongDescription($html)
    {
        $this->long_description = $html;
    }

    public function mount($id)
    {
        $this->variantSystemEnabled = (bool) setting('attribute_system', true);
        $product = Product::with(['variants.values', 'variants.inventories'])->findOrFail($id);

        $this->productId = $product->id;

        // Basic fields
        $this->name = $product->name;
        $this->slug = $product->slug;
        $this->sku = $product->sku;
        $this->has_attributes = (bool) ($product->has_attributes ?? true);
        $this->has_image_options = (bool) ($product->has_image_options ?? false);
        $this->require_image_option = (bool) ($product->require_image_option ?? true);
        $this->attrRequired = $product->productAttributes()
            ->get()->mapWithKeys(fn ($a) => [$a->id => (bool) $a->pivot->is_required])->all();
        $this->meta_title = $product->meta_title;
        $this->meta_description = $product->meta_description;
        $this->meta_keywords = $product->meta_keywords;
        $this->meta_tags = $product->meta_tags;
        $this->short_description = $product->short_description;
        $this->long_description = $product->long_description;
        $this->buying_price = $product->buying_price;
        $this->price = $product->price;
        $this->discount_type = $product->discount_type;
        $this->discount_price = $product->discount_price;
        $this->category_id = $product->category_id;
        $this->brand_id = $product->brand_id;
        $this->video = $product->video;
        $this->status = $product->status ? true : false;
        $this->is_featured = $product->is_featured ? true : false;
        $this->existingImages = $product->gallery_image ?? [];
        $this->is_preorder = $product->is_preorder ?? false;
        $this->preorder_start_date = $product->preorder_start_date?->format('Y-m-d\TH:i');
        $this->preorder_end_date = $product->preorder_end_date?->format('Y-m-d\TH:i');
        $this->expected_release_date = $product->expected_release_date?->format('Y-m-d\TH:i');

        // Load attributes and variants
        $this->productAttributes = Attribute::with('values')->get();

        $this->variants = $product->variants
            ->map(function ($variant) {
                $currentStock = $variant->inventories->sortByDesc('id')->first()?->current_stock ?? 0;

                return [
                    'id' => $variant->id,
                    'sku' => $variant->sku,
                    'variant_price' => $variant->variant_price,
                    'sale_price' => $variant->sale_price,
                    'stock' => $currentStock,
                    'low_stock_threshold' => $variant->low_stock_threshold ?? 5,
                    'is_active' => (bool) ($variant->is_active ?? true),
                    'is_default' => (bool) ($variant->is_default ?? false),
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

        // Swatch-linked gallery
        $this->loadImageLinks($product->id);

        // Image options layer
        $this->loadImageOptions($product->id);
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
        $this->newVariant['sku'] = 'SKU-'.strtoupper(Str::random(6));
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

        // Check if variant with same attributes already exists
        if ($this->variantExists()) {
            $this->variantErrors['duplicate'] = 'A variant with these attributes already exists.';

            return;
        }

        $variant = [
            'id' => 'new_'.++$this->variantCounter,
            'sku' => $this->newVariant['sku'],
            'variant_price' => (float) $this->newVariant['variant_price'],
            'stock' => (int) ($this->newVariant['stock'] ?? 0),
            'values' => [],
            'image' => null,
            'new_image_file' => null,
        ];

        // Store attribute values
        foreach ($this->newVariant['attributes'] as $attrId => $valueId) {
            if (! empty($valueId)) {
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
            $this->variantErrors['variant_price'] = 'Price adjustment must be a number.';
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
            if (is_numeric($this->variants[$index]['id'])) {
                // Ordered variants cannot be deleted — deactivate instead
                $orders = $this->variantOrderedCount((int) $this->variants[$index]['id']);
                if ($orders > 0) {
                    $this->dispatch('error', message: "এই variant-এ {$orders}টি order আছে। Delete না করে Deactivate করুন।");

                    return;
                }
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
            'variant_price' => 0,
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
            'slug' => 'required|string|unique:products,slug,'.$this->productId,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_tags' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_type' => 'nullable|string|in:fixed,percentage|required_with:discount_price',
            'discount_price' => 'nullable|numeric|min:0|required_with:discount_type',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'thumbnail' => 'nullable|image|max:2048',
            'images.*' => 'nullable|image|max:2048',
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
                $rowErrors = $this->validateVariantRows($this->productId);
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

            $product = Product::findOrFail($this->productId);

            $product->update([
                'name' => $this->name,
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

            // Handle thumbnail update
            if ($this->thumbnail) {
                $path = $this->thumbnail->store('products/thumbnails', 'public');
                $product->thumbnail = $path;
                $product->save();
            }

            // Handle gallery images update
            $galleryPaths = [];

            // Keep existing images if they exist
            if (! empty($this->existingImages) && is_array($this->existingImages)) {
                $galleryPaths = $this->existingImages;
            }

            // Add new images
            if (! empty($this->images) && is_array($this->images)) {
                foreach ($this->images as $img) {
                    $path = $img->store('products/images', 'public');
                    $galleryPaths[] = $path;
                }
            }

            // Update gallery_image field
            $product->gallery_image = $galleryPaths;
            $product->save();

            // Handle variants - Update existing and create new ones.
            // When the attributes layer is OFF, existing variants stay in DB untouched.
            $existingVariantIds = [];

            foreach ($useAttributes ? $this->variants : [] as $i => $variantData) {
                // Skip variants marked for deletion
                if (isset($variantData['_delete']) && $variantData['_delete']) {
                    if (is_numeric($variantData['id'])) {
                        $variantToDelete = ProductVariant::find($variantData['id']);
                        if ($variantToDelete) {
                            $variantToDelete->delete();
                        }
                    }

                    continue;
                }

                if (is_numeric($variantData['id']) && ! str_starts_with((string) $variantData['id'], 'new_')) {
                    // Update existing variant
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant) {
                        $variant->update([
                            'sku' => $variantData['sku'],
                            'variant_price' => $this->nullIfEmpty($variantData['variant_price'] ?? null),
                            'sale_price' => $this->nullIfEmpty($variantData['sale_price'] ?? null),
                            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                            'is_active' => $variantData['is_active'] ?? true,
                            'is_default' => $variantData['is_default'] ?? false,
                        ]);

                        // Sync edited stock into inventory ledger (adjustment)
                        if (isset($variantData['stock'])) {
                            $current = \App\Services\InventoryService::computeStock($variant->inventories());
                            $diff = (int) $variantData['stock'] - $current;
                            if ($diff !== 0) {
                                Inventory::create([
                                    'product_id' => $product->id,
                                    'variant_id' => $variant->id,
                                    'type' => 'adjustment',
                                    'quantity' => $diff,
                                    'current_stock' => (int) $variantData['stock'],
                                    'notes' => 'Manual stock edit from product page',
                                    'user_id' => auth()->id(),
                                ]);
                            }
                        }
                    }
                } else {
                    // Create new variant
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'stock' => $variantData['stock'] ?? 0,
                        'sku' => $variantData['sku'],
                        'variant_price' => $this->nullIfEmpty($variantData['variant_price'] ?? null),
                        'sale_price' => $this->nullIfEmpty($variantData['sale_price'] ?? null),
                        'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                        'is_active' => $variantData['is_active'] ?? true,
                        'is_default' => $variantData['is_default'] ?? false,
                    ]);

                    // Create inventory record for new variant
                    Inventory::create([
                        'product_id' => $product->id,
                        'variant_id' => $variant->id,
                        'type' => 'in',
                        'quantity' => $variantData['stock'] ?? 0,
                        'current_stock' => $variantData['stock'] ?? 0,
                    ]);
                }

                // Handle variant image
                if (isset($variantData['new_image_file']) && $variantData['new_image_file']) {
                    $path = $variantData['new_image_file']->store('products/variants', 'public');
                    $variant->image = $path;
                    $variant->save();
                } elseif (! empty($variantData['image']) && is_numeric($variantData['id'])) {
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

                // Wholesale slabs for this row
                $this->syncVariantSlabs($variant, $this->variantSlabs[$this->rowKey($variantData, $i)] ?? []);

                $existingVariantIds[] = $variant->id;
            }

            // Delete variants that were removed (only when layer is ON)
            if ($useAttributes) {
                ProductVariant::where('product_id', $product->id)->whereNotIn('id', $existingVariantIds)->delete();
            }

            // Link product ↔ attributes used by its variants
            $attrIds = collect($this->variants)
                ->reject(fn ($v) => ! empty($v['_delete']))
                ->flatMap(fn ($v) => array_column($v['values'] ?? [], 'attribute_id'))
                ->unique()->values()->all();
            if ($useAttributes && ! empty($attrIds)) {
                $sync = [];
                foreach ($attrIds as $attrId) {
                    $sync[$attrId] = ['is_required' => (bool) ($this->attrRequired[$attrId] ?? true)];
                }
                $product->productAttributes()->sync($sync);
            }

            // Persist swatch-linked gallery
            $this->persistImageLinks($product->id);

            // Persist image options layer
            if ($useOptions && ! empty($this->imageOptions)) {
                foreach ($this->persistImageOptions($product->id) as $warning) {
                    $this->dispatch('warning', message: $warning);
                }
            } elseif (! $useOptions) {
                // Layer OFF: drop staged options but keep it simple — delete only
                // options never ordered; ordered ones are deactivated instead.
                foreach (ImageOption::where('product_id', $product->id)->get() as $opt) {
                    if (OrderItem::where('image_option_id', $opt->id)->exists()) {
                        $opt->update(['is_active' => false]);
                    } else {
                        $opt->delete();
                    }
                }
            }

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
            Storage::delete('public/'.$path);
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
            'brands' => Brand::all(),
        ])->layout('livewire.admin.layouts.app');
    }
}
