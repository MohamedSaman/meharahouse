<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Electronics',   'description' => 'Latest gadgets, phones, and electronic devices.', 'sort_order' => 1],
            ['name' => 'Clothing',      'description' => 'Men\'s, women\'s, and kids\' fashion clothing.', 'sort_order' => 2],
            ['name' => 'Food & Drinks', 'description' => 'Fresh groceries, snacks, and beverages.', 'sort_order' => 3],
            ['name' => 'Beauty',        'description' => 'Skincare, cosmetics, and personal care products.', 'sort_order' => 4],
            ['name' => 'Home & Living', 'description' => 'Furniture, décor, and household essentials.', 'sort_order' => 5],
            ['name' => 'Sports',        'description' => 'Sports equipment, activewear, and accessories.', 'sort_order' => 6],
            ['name' => 'Books',         'description' => 'Bestselling books, textbooks, and stationery.', 'sort_order' => 7],
            ['name' => 'Toys & Kids',   'description' => 'Educational toys, games, and children\'s products.', 'sort_order' => 8],
            ['name' => 'Jewelry',       'description' => 'Gold, silver, and fashion jewelry collections.', 'sort_order' => 9],
            ['name' => 'Furniture',     'description' => 'Premium furniture for home and office.', 'sort_order' => 10],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name'        => $cat['name'],
                    'slug'        => Str::slug($cat['name']),
                    'description' => $cat['description'],
                    'is_active'   => true,
                    'sort_order'  => $cat['sort_order'],
                ]
            );
        }
    }
}
