<?php

namespace App\Livewire\Admin\Products\Concerns;

use App\Models\ProductImage;

/**
 * Swatch-linked image manager (product_images table).
 * Staged rows: ['key', 'id'?, 'path'?, 'preview'?, 'sort_order', 'attribute_value_id'?]
 * Create stages uploads until the product exists; Edit persists immediately.
 */
trait ManagesProductImages
{
    public $imageLinks = [];

    public $imageUploads = [];

    public $imageLinkCounter = 0;

    public function loadImageLinks(int $productId): void
    {
        $this->imageLinks = ProductImage::where('product_id', $productId)
            ->ordered()
            ->get()
            ->map(fn ($img) => [
                'key' => 'db_'.$img->id,
                'id' => $img->id,
                'path' => $img->path,
                'preview' => asset('storage/'.$img->path),
                'sort_order' => $img->sort_order,
                'attribute_value_id' => $img->attribute_value_id,
            ])
            ->toArray();
    }

    public function updatedImageUploads(): void
    {
        foreach ((array) $this->imageUploads as $upload) {
            if (! $upload) {
                continue;
            }
            $path = $upload->store('products/swatch-gallery', 'public');
            $this->imageLinks[] = [
                'key' => 'new_'.($this->imageLinkCounter++),
                'id' => null,
                'path' => $path,
                'preview' => asset('storage/'.$path),
                'sort_order' => count($this->imageLinks),
                'attribute_value_id' => null,
            ];
        }
        $this->imageUploads = [];
    }

    public function removeImageLink($index): void
    {
        if (! isset($this->imageLinks[$index])) {
            return;
        }

        $row = $this->imageLinks[$index];

        // Persisted row on an existing product → delete now
        if (! empty($row['id']) && isset($this->productId)) {
            ProductImage::where('id', $row['id'])->where('product_id', $this->productId)->delete();
            $this->dispatch('success', message: 'ছবি মুছে ফেলা হয়েছে।');
        } elseif (! empty($row['path'])) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($row['path']);
        }

        unset($this->imageLinks[$index]);
        $this->imageLinks = array_values($this->imageLinks);
    }

    /** Write staged links for a product (keeps existing rows in sync). */
    public function persistImageLinks(int $productId): void
    {
        $keepIds = [];
        foreach (array_values($this->imageLinks) as $sort => $row) {
            if (! empty($row['id'])) {
                ProductImage::where('id', $row['id'])->where('product_id', $productId)->update([
                    'sort_order' => $sort,
                    'attribute_value_id' => $row['attribute_value_id'] ?: null,
                ]);
                $keepIds[] = $row['id'];
            } else {
                $img = ProductImage::create([
                    'product_id' => $productId,
                    'path' => $row['path'],
                    'sort_order' => $sort,
                    'attribute_value_id' => $row['attribute_value_id'] ?: null,
                ]);
                $keepIds[] = $img->id;
            }
        }

        ProductImage::where('product_id', $productId)->whereNotIn('id', $keepIds)->delete();
    }
}
