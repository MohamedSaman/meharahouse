<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Abaya
            [
                'category' => 'abaya',
                'name'     => 'Classic Black Abaya',
                'desc'     => 'Elegant full-length black abaya made from premium crepe fabric. Flowy and comfortable for everyday wear.',
                'price'    => 1200, 'sale' => 999, 'stock' => 50, 'featured' => true,
            ],
            [
                'category' => 'abaya',
                'name'     => 'Butterfly Abaya — Navy Blue',
                'desc'     => 'Stylish butterfly-cut abaya in navy blue with subtle side embroidery. Lightweight and breathable.',
                'price'    => 1450, 'sale' => null, 'stock' => 35, 'featured' => true,
            ],
            [
                'category' => 'abaya',
                'name'     => 'Open Front Abaya — Olive Green',
                'desc'     => 'Modern open-front abaya in olive green nida fabric. Belt included for a fitted look.',
                'price'    => 1350, 'sale' => 1150, 'stock' => 40, 'featured' => false,
            ],
            [
                'category' => 'abaya',
                'name'     => 'Striped Abaya — Black & White',
                'desc'     => 'Trendy striped abaya with bell sleeves and a relaxed fit. Perfect for casual outings.',
                'price'    => 1100, 'sale' => null, 'stock' => 30, 'featured' => false,
            ],

            // Jilbab
            [
                'category' => 'jilbab',
                'name'     => 'Two-Piece Jilbab Set — Black',
                'desc'     => 'Classic two-piece jilbab set with matching khimar. Made from high-quality medina silk.',
                'price'    => 1600, 'sale' => 1399, 'stock' => 25, 'featured' => true,
            ],
            [
                'category' => 'jilbab',
                'name'     => 'Jersey Jilbab — Dusty Rose',
                'desc'     => 'Soft jersey jilbab in dusty rose. Breathable, non-iron fabric, full coverage.',
                'price'    => 1250, 'sale' => null, 'stock' => 30, 'featured' => false,
            ],
            [
                'category' => 'jilbab',
                'name'     => 'Nida Jilbab — Dark Grey',
                'desc'     => 'Premium nida fabric jilbab in dark grey. Modest, wrinkle-resistant, and easy to wear.',
                'price'    => 1400, 'sale' => 1200, 'stock' => 20, 'featured' => false,
            ],

            // Casual Abaya
            [
                'category' => 'casual-abaya',
                'name'     => 'Linen Casual Abaya — Beige',
                'desc'     => 'Relaxed-fit linen abaya in beige. Ideal for daily wear, shopping, and casual outings.',
                'price'    => 950, 'sale' => 799, 'stock' => 60, 'featured' => true,
            ],
            [
                'category' => 'casual-abaya',
                'name'     => 'Cotton Casual Abaya — White',
                'desc'     => 'Lightweight white cotton abaya with pocket slits. Simple, clean, and comfortable.',
                'price'    => 890, 'sale' => null, 'stock' => 55, 'featured' => false,
            ],
            [
                'category' => 'casual-abaya',
                'name'     => 'Denim Abaya — Indigo',
                'desc'     => 'Trendy denim-style abaya in indigo. Modern look with button placket and side pockets.',
                'price'    => 1300, 'sale' => 1099, 'stock' => 25, 'featured' => true,
            ],

            // Formal Abaya
            [
                'category' => 'formal-abaya',
                'name'     => 'Embroidered Abaya — Midnight Black',
                'desc'     => 'Luxurious black abaya with gold floral embroidery on cuffs and hem. Perfect for weddings.',
                'price'    => 2800, 'sale' => 2499, 'stock' => 15, 'featured' => true,
            ],
            [
                'category' => 'formal-abaya',
                'name'     => 'Velvet Formal Abaya — Burgundy',
                'desc'     => 'Rich burgundy velvet abaya with crystal button detailing. Elegant for special events.',
                'price'    => 3200, 'sale' => null, 'stock' => 10, 'featured' => true,
            ],
            [
                'category' => 'formal-abaya',
                'name'     => 'Lace Overlay Abaya — Ivory',
                'desc'     => 'Beautiful ivory abaya with delicate lace overlay and satin inner lining.',
                'price'    => 2600, 'sale' => 2299, 'stock' => 12, 'featured' => false,
            ],

            // Kids Abaya
            [
                'category' => 'kids-abaya',
                'name'     => 'Girls Abaya — Pink Floral',
                'desc'     => 'Adorable pink floral print abaya for girls ages 3-12. Soft, comfortable fabric.',
                'price'    => 650, 'sale' => 499, 'stock' => 45, 'featured' => true,
            ],
            [
                'category' => 'kids-abaya',
                'name'     => 'Girls Casual Abaya — Black',
                'desc'     => 'Simple black everyday abaya for girls ages 5-14. Easy to put on with button front.',
                'price'    => 580, 'sale' => null, 'stock' => 50, 'featured' => false,
            ],
            [
                'category' => 'kids-abaya',
                'name'     => 'Girls Embroidered Abaya — Lavender',
                'desc'     => 'Sweet lavender abaya with floral embroidery at the hem. Perfect for Eid celebrations.',
                'price'    => 850, 'sale' => 699, 'stock' => 30, 'featured' => false,
            ],

            // Inner Dress
            [
                'category' => 'inner-dress',
                'name'     => 'Full-Length Inner Slip Dress — White',
                'desc'     => 'Smooth white under-abaya slip dress. Prevents see-through, non-static, soft jersey fabric.',
                'price'    => 450, 'sale' => 380, 'stock' => 100, 'featured' => true,
            ],
            [
                'category' => 'inner-dress',
                'name'     => 'Full-Length Inner Slip Dress — Black',
                'desc'     => 'Classic black inner slip dress. Lightweight, breathable, and comfortable all day.',
                'price'    => 450, 'sale' => null, 'stock' => 120, 'featured' => false,
            ],
            [
                'category' => 'inner-dress',
                'name'     => 'Long Inner Dress with Sleeves — Nude',
                'desc'     => 'Long-sleeved inner dress in nude. Ideal under open-front or sheer abayas.',
                'price'    => 550, 'sale' => 480, 'stock' => 80, 'featured' => false,
            ],

            // Innerwear Top
            [
                'category' => 'innerwear-top',
                'name'     => 'Modest Camisole — White',
                'desc'     => 'Full-coverage modest camisole in white. High neckline, longer length, soft cotton blend.',
                'price'    => 250, 'sale' => 199, 'stock' => 150, 'featured' => false,
            ],
            [
                'category' => 'innerwear-top',
                'name'     => 'Long-Sleeve Undershirt — Black',
                'desc'     => 'Thin, seamless long-sleeve undershirt. Perfect for layering under any abaya.',
                'price'    => 299, 'sale' => null, 'stock' => 130, 'featured' => true,
            ],
            [
                'category' => 'innerwear-top',
                'name'     => 'Cotton Modest Undershirt — Skin',
                'desc'     => 'Skin-tone undershirt with crew neck. Breathable cotton, anti-static, machine washable.',
                'price'    => 280, 'sale' => 230, 'stock' => 110, 'featured' => false,
            ],

            // Innerwear Bottom
            [
                'category' => 'innerwear-bottom',
                'name'     => 'Full-Length Leggings — Black',
                'desc'     => 'Thick, opaque full-length leggings. Comfortable waistband, no see-through guarantee.',
                'price'    => 350, 'sale' => 299, 'stock' => 200, 'featured' => true,
            ],
            [
                'category' => 'innerwear-bottom',
                'name'     => 'Underskirt Petticoat — White',
                'desc'     => 'Flared white petticoat underskirt. Adds modesty and volume under abayas and dresses.',
                'price'    => 400, 'sale' => null, 'stock' => 90, 'featured' => false,
            ],
            [
                'category' => 'innerwear-bottom',
                'name'     => 'Wide-Leg Undertrousers — Nude',
                'desc'     => 'Loose, wide-leg modest undertrousers in nude. Breathable and cool for all-day wear.',
                'price'    => 380, 'sale' => 320, 'stock' => 85, 'featured' => false,
            ],
        ];

        // Remove products from old unrelated categories
        $validCategorySlugs = [
            'abaya', 'jilbab', 'casual-abaya', 'formal-abaya',
            'kids-abaya', 'inner-dress', 'innerwear-top', 'innerwear-bottom',
        ];
        $validCategoryIds = Category::whereIn('slug', $validCategorySlugs)->pluck('id');
        Product::whereNotIn('category_id', $validCategoryIds)->delete();

        foreach ($products as $p) {
            $category = Category::where('slug', $p['category'])->first();
            if (!$category) continue;

            $slug     = Str::slug($p['name']);
            $baseSlug = $slug;
            $counter  = 1;
            while (Product::where('slug', $slug)->whereNot('name', $p['name'])->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            $sku = 'MH-' . strtoupper(Str::random(6));

            Product::updateOrCreate(
                ['name' => $p['name']],
                [
                    'category_id' => $category->id,
                    'slug'        => $slug,
                    'description' => $p['desc'],
                    'sku'         => $sku,
                    'price'       => $p['price'],
                    'sale_price'  => $p['sale'],
                    'stock'       => $p['stock'],
                    'images'      => [],
                    'is_featured' => $p['featured'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
