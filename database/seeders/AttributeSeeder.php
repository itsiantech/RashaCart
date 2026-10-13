<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\AttributeValue;
use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Default: attribute system ON (Add Product shows the section)
        Settings::updateOrCreate(['key' => 'attribute_system'], ['value' => 1]);
        Settings::clearCache();

        $data = [
            [
                'name' => 'Color', 'slug' => 'color', 'type' => Attribute::TYPE_COLOR_SWATCH,
                'is_filterable' => true, 'is_active' => true, 'sort_order' => 1,
                'values' => [
                    ['value' => 'Red', 'color_hex' => '#DC2626'],
                    ['value' => 'Green', 'color_hex' => '#16A34A'],
                    ['value' => 'Blue', 'color_hex' => '#2563EB'],
                    ['value' => 'Black', 'color_hex' => '#111111'],
                    ['value' => 'White', 'color_hex' => '#FFFFFF'],
                ],
            ],
            [
                'name' => 'Size', 'slug' => 'size', 'type' => Attribute::TYPE_BUTTON,
                'is_filterable' => true, 'is_active' => true, 'sort_order' => 2,
                'values' => ['S', 'M', 'L', 'XL', 'XXL'],
            ],
            [
                'name' => 'Unit', 'slug' => 'unit', 'type' => Attribute::TYPE_DROPDOWN,
                'is_filterable' => false, 'is_active' => true, 'sort_order' => 3,
                'values' => ['Piece', 'Box', 'Kg', 'Litre'],
            ],
        ];

        foreach ($data as $i => $attr) {
            $values = $attr['values'];
            unset($attr['values']);

            $attribute = Attribute::updateOrCreate(
                ['slug' => $attr['slug']],
                $attr
            );

            foreach (array_values($values) as $j => $v) {
                $row = is_array($v) ? $v : ['value' => $v];
                AttributeValue::updateOrCreate(
                    ['attribute_id' => $attribute->id, 'value' => $row['value']],
                    array_merge($row, [
                        'slug' => Str::slug($row['value']),
                        'sort_order' => $j,
                    ])
                );
            }
        }
    }
}
