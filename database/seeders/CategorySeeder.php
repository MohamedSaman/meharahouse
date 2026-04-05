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
            ['name' => 'Abaya',             'description' => 'Elegant full-length abaya dresses for all occasions.', 'sort_order' => 1],
            ['name' => 'Jilbab',            'description' => 'Modest jilbab sets and two-piece abaya styles.', 'sort_order' => 2],
            ['name' => 'Casual Abaya',      'description' => 'Everyday comfortable abaya for daily wear.', 'sort_order' => 3],
            ['name' => 'Formal Abaya',      'description' => 'Luxurious embroidered and embellished abayas for special occasions.', 'sort_order' => 4],
            ['name' => 'Kids Abaya',        'description' => 'Modest and stylish abaya dresses for girls.', 'sort_order' => 5],
            ['name' => 'Inner Dress',       'description' => 'Comfortable under-abaya inner dresses and slips.', 'sort_order' => 6],
            ['name' => 'Innerwear Top',     'description' => 'Modest innerwear tops, undershirts, and camisoles.', 'sort_order' => 7],
            ['name' => 'Innerwear Bottom',  'description' => 'Full-coverage leggings, petticoats, and underskirts.', 'sort_order' => 8],
        ];

        // Remove categories that no longer apply
        $validSlugs = collect($categories)->map(fn($c) => Str::slug($c['name']))->toArray();
        Category::whereNotIn('slug', $validSlugs)->delete();

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
