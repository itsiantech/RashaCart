<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Http\Response;

/**
 * Catalog feeds: one row per ACTIVE variant (simple products → one row).
 * item_group_id = product id, content/sku ids match tracking content_id.
 */
class ProductFeedController extends Controller
{
    protected function rows()
    {
        return Product::query()
            ->where('status', true)
            ->with([
                'variants' => fn ($q) => $q->active()->with([
                    'variantAttributeValues.attribute',
                    'variantAttributeValues.attributeValue',
                ]),
                'imageOptions' => fn ($q) => $q->active()->ordered(),
            ])
            ->cursor()
            ->flatMap(function (Product $product) {
                $link = fn () => route('frontend.product.single.view', $product->slug);
                $base = [
                    'group' => $product->id,
                    'name' => $product->name,
                    'link' => $link(),
                    'image' => $product->thumbnail ? asset('storage/'.$product->thumbnail) : null,
                    'brand' => $product->brand?->name ?? config('app.name'),
                ];

                $variants = $product->variants->isNotEmpty()
                    ? $product->variants
                    : [null]; // simple product → single row
                $options = $product->has_image_options && $product->imageOptions->isNotEmpty()
                    ? $product->imageOptions
                    : [null]; // no options → single row per variant

                $rows = [];
                foreach ($variants as $variant) {
                    $attrs = [];
                    if ($variant) {
                        foreach ($variant->variantAttributeValues as $vav) {
                            $attrs[strtolower($vav->attribute?->name ?? '')] = $vav->attributeValue?->value;
                        }
                    }
                    $vPrice = $variant
                        ? (float) $variant->effective_price
                        : (float) $product->sale_price;
                    $vSku = $variant?->sku ?? $product->sku;
                    $vAvail = $variant
                        ? InventoryService::currentStock($variant->product_id, $variant->id)
                        : InventoryService::currentStock($product->id);
                    $vImage = ($variant && $variant->image)
                        ? asset('storage/'.$variant->image) : $base['image'];

                    foreach ($options as $option) {
                        $price = $option && $option->effective_price !== null
                            ? (float) $option->effective_price : $vPrice;
                        $avail = $vAvail;
                        if ($option && \App\Services\ImageOptionStock::hasOwnStock($option->id)) {
                            $avail = min($avail, \App\Services\ImageOptionStock::currentStock($option->id));
                        }
                        // Matches tracking content_id: productSKU[-variantSKU]-optionId
                        $parts = [$product->sku];
                        if ($variant) {
                            $parts[] = $variant->sku;
                        }
                        if ($option) {
                            $parts[] = 'opt'.$option->id;
                        }
                        $rows[] = [
                            ...$base,
                            'id' => $option ? implode('-', $parts) : (string) $vSku,
                            'price' => number_format($price, 2, '.', ''),
                            'availability' => $avail > 0 ? 'in_stock' : 'out_of_stock',
                            'image' => $option ? asset('storage/'.$option->image) : $vImage,
                            'color' => $attrs['color'] ?? $attrs['colour'] ?? null,
                            'size' => $attrs['size'] ?? null,
                        ];
                    }
                }

                return $rows;
            });
    }

    /** Google Merchant Center (RSS 2.0). */
    public function google(): Response
    {
        $items = '';
        foreach ($this->rows() as $row) {
            $items .= '<item>'
                .'<g:id>'.e($row['id']).'</g:id>'
                .'<g:item_group_id>'.e($row['group']).'</g:item_group_id>'
                .'<title>'.e($row['name']).'</title>'
                .'<link>'.e($row['link']).'</link>'
                .($row['image'] ? '<g:image_link>'.e($row['image']).'</g:image_link>' : '')
                .'<g:price>'.$row['price'].' BDT</g:price>'
                .'<g:availability>'.$row['availability'].'</g:availability>'
                .'<g:brand>'.e($row['brand']).'</g:brand>'
                .'<g:condition>new</g:condition>'
                .($row['color'] ? '<g:color>'.e($row['color']).'</g:color>' : '')
                .($row['size'] ? '<g:size>'.e($row['size']).'</g:size>' : '')
                .'</item>';
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">'
            ."<channel><title><![CDATA[".config('app.name')." products]]></title>"
            .'<link>'.e(url('/')).'</link>'
            .'<description>Product feed</description>'
            .$items.'</channel></rss>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /** Meta / TikTok catalog (CSV). */
    public function meta(): Response
    {
        $lines = ['id,item_group_id,title,link,image_link,price,availability,brand,color,size'];
        foreach ($this->rows() as $row) {
            $lines[] = implode(',', [
                $this->csv($row['id']), $this->csv($row['group']), $this->csv($row['name']),
                $this->csv($row['link']), $this->csv($row['image'] ?? ''),
                $this->csv($row['price'].' BDT'), $this->csv($row['availability']),
                $this->csv($row['brand']), $this->csv($row['color'] ?? ''), $this->csv($row['size'] ?? ''),
            ]);
        }

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/csv']);
    }

    protected function csv($value): string
    {
        return '"'.str_replace('"', '""', (string) ($value ?? '')).'"';
    }
}
