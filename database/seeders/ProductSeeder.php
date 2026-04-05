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
            // Electronics
            [
                'category' => 'electronics',
                'name'     => 'Samsung Galaxy A54',
                'desc'     => 'A versatile mid-range smartphone with 50MP camera, 5000mAh battery, and 6.4" Super AMOLED display.',
                'price'    => 14999, 'sale' => 12999, 'stock' => 25, 'featured' => true,
            ],
            [
                'category' => 'electronics',
                'name'     => 'Apple AirPods Pro',
                'desc'     => 'Active noise cancellation, Transparency mode, and adaptive audio for immersive listening.',
                'price'    => 8999, 'sale' => null, 'stock' => 15, 'featured' => true,
            ],
            [
                'category' => 'electronics',
                'name'     => 'Lenovo IdeaPad Laptop 15"',
                'desc'     => 'Intel Core i5, 8GB RAM, 512GB SSD, Windows 11 — perfect for work and study.',
                'price'    => 32999, 'sale' => 29999, 'stock' => 8, 'featured' => true,
            ],
            [
                'category' => 'electronics',
                'name'     => 'Smart LED TV 43"',
                'desc'     => '4K UHD resolution, Android TV, built-in Chromecast, Dolby Audio.',
                'price'    => 19500, 'sale' => 17000, 'stock' => 12, 'featured' => false,
            ],
            // Clothing
            [
                'category' => 'clothing',
                'name'     => 'Ethiopian Traditional Habesha Kemis',
                'desc'     => 'Hand-woven pure cotton Habesha dress with intricate border embroidery. Available in multiple colors.',
                'price'    => 1800, 'sale' => null, 'stock' => 40, 'featured' => true,
            ],
            [
                'category' => 'clothing',
                'name'     => 'Men\'s Classic Formal Shirt',
                'desc'     => '100% cotton formal shirt available in white, blue, and grey. Perfect for office wear.',
                'price'    => 650, 'sale' => 499, 'stock' => 60, 'featured' => false,
            ],
            [
                'category' => 'clothing',
                'name'     => 'Women\'s Leather Jacket',
                'desc'     => 'Premium faux leather jacket with quilted lining. Slim fit, available in black and brown.',
                'price'    => 2200, 'sale' => 1850, 'stock' => 20, 'featured' => true,
            ],
            // Food & Drinks
            [
                'category' => 'food-drinks',
                'name'     => 'Ethiopian Coffee Gift Set',
                'desc'     => 'Premium single-origin Yirgacheffe coffee beans, 500g. Freshly roasted with rich aroma.',
                'price'    => 450, 'sale' => null, 'stock' => 100, 'featured' => true,
            ],
            [
                'category' => 'food-drinks',
                'name'     => 'Organic Honey — 1kg',
                'desc'     => 'Pure raw honey sourced from Ethiopian highlands. No additives, rich in natural enzymes.',
                'price'    => 380, 'sale' => 330, 'stock' => 75, 'featured' => false,
            ],
            [
                'category' => 'food-drinks',
                'name'     => 'Berbere Spice Mix',
                'desc'     => 'Authentic Ethiopian berbere blend with 20+ spices. Perfect for traditional dishes.',
                'price'    => 120, 'sale' => null, 'stock' => 200, 'featured' => false,
            ],
            // Beauty
            [
                'category' => 'beauty',
                'name'     => 'Organic Shea Butter Cream',
                'desc'     => 'Pure unrefined shea butter moisturizer. Nourishes and hydrates all skin types.',
                'price'    => 299, 'sale' => 249, 'stock' => 80, 'featured' => true,
            ],
            [
                'category' => 'beauty',
                'name'     => 'Argan Oil Hair Treatment',
                'desc'     => 'Cold-pressed Moroccan argan oil for smooth, shiny, and frizz-free hair.',
                'price'    => 480, 'sale' => null, 'stock' => 45, 'featured' => false,
            ],
            [
                'category' => 'beauty',
                'name'     => 'Natural Charcoal Face Mask',
                'desc'     => 'Deep cleansing activated charcoal mask. Removes impurities and unclogs pores.',
                'price'    => 199, 'sale' => 149, 'stock' => 120, 'featured' => false,
            ],
            // Home & Living
            [
                'category' => 'home-living',
                'name'     => 'Handwoven Basket Set (3 pcs)',
                'desc'     => 'Traditional Ethiopian handwoven decorative baskets. Eco-friendly and durable.',
                'price'    => 850, 'sale' => null, 'stock' => 30, 'featured' => true,
            ],
            [
                'category' => 'home-living',
                'name'     => 'Stainless Steel Cookware Set',
                'desc'     => '7-piece non-stick stainless steel cookware. Dishwasher safe, compatible with all hobs.',
                'price'    => 3200, 'sale' => 2799, 'stock' => 15, 'featured' => false,
            ],
            // Sports
            [
                'category' => 'sports',
                'name'     => 'Yoga Mat — Premium Anti-Slip',
                'desc'     => '6mm thick TPE eco-friendly yoga mat with alignment lines. Lightweight and portable.',
                'price'    => 550, 'sale' => 450, 'stock' => 50, 'featured' => false,
            ],
            [
                'category' => 'sports',
                'name'     => 'Adjustable Dumbbell Set',
                'desc'     => 'Quick-lock adjustable dumbbells from 5kg to 32kg. Space-saving home gym solution.',
                'price'    => 5500, 'sale' => null, 'stock' => 10, 'featured' => true,
            ],
            // Books
            [
                'category' => 'books',
                'name'     => 'Amharic-English Dictionary',
                'desc'     => 'Comprehensive Amharic-English / English-Amharic dictionary with over 50,000 entries.',
                'price'    => 320, 'sale' => null, 'stock' => 60, 'featured' => false,
            ],
            [
                'category' => 'books',
                'name'     => 'The Business of Tomorrow',
                'desc'     => 'A practical guide to entrepreneurship and innovation for East African markets.',
                'price'    => 280, 'sale' => 220, 'stock' => 35, 'featured' => false,
            ],
            // Toys & Kids
            [
                'category' => 'toys-kids',
                'name'     => 'Educational STEM Building Blocks',
                'desc'     => '200-piece colorful building blocks for ages 3+. Develops creativity and motor skills.',
                'price'    => 650, 'sale' => 499, 'stock' => 40, 'featured' => true,
            ],
            [
                'category' => 'toys-kids',
                'name'     => 'Kids Wooden Puzzle Set',
                'desc'     => 'Set of 5 educational wooden puzzles featuring Ethiopian animals. Ages 2-6.',
                'price'    => 380, 'sale' => null, 'stock' => 55, 'featured' => false,
            ],
            // Jewelry
            [
                'category' => 'jewelry',
                'name'     => 'Gold-Plated Ethiopian Cross Pendant',
                'desc'     => 'Handcrafted gold-plated Ethiopian Orthodox cross pendant with 18" chain.',
                'price'    => 1200, 'sale' => null, 'stock' => 20, 'featured' => true,
            ],
            [
                'category' => 'jewelry',
                'name'     => 'Silver Filigree Earrings',
                'desc'     => 'Intricate sterling silver filigree earrings. Traditional Harari design, gift-boxed.',
                'price'    => 780, 'sale' => 650, 'stock' => 25, 'featured' => false,
            ],
            // Furniture
            [
                'category' => 'furniture',
                'name'     => 'Solid Wood Coffee Table',
                'desc'     => 'Handcrafted solid Wanza wood coffee table with carved legs. 120x60cm, natural finish.',
                'price'    => 6500, 'sale' => 5800, 'stock' => 5, 'featured' => false,
            ],
            [
                'category' => 'furniture',
                'name'     => 'Ergonomic Office Chair',
                'desc'     => 'Adjustable lumbar support, breathable mesh back, 360° swivel. Max load 150kg.',
                'price'    => 7200, 'sale' => null, 'stock' => 8, 'featured' => true,
            ],
            // More Electronics
            [
                'category' => 'electronics',
                'name'     => 'Wireless Bluetooth Speaker',
                'desc'     => 'Waterproof portable speaker with 20hr battery, 360° surround sound.',
                'price'    => 1999, 'sale' => 1599, 'stock' => 30, 'featured' => false,
            ],
            [
                'category' => 'electronics',
                'name'     => 'USB-C Fast Charger 65W',
                'desc'     => 'GaN technology 65W USB-C fast charger. Compatible with laptops, phones, and tablets.',
                'price'    => 699, 'sale' => null, 'stock' => 70, 'featured' => false,
            ],
            // More Clothing
            [
                'category' => 'clothing',
                'name'     => 'Men\'s Running Shoes',
                'desc'     => 'Lightweight breathable mesh running shoes with cushioned sole. Sizes 38-46.',
                'price'    => 1350, 'sale' => 1099, 'stock' => 45, 'featured' => false,
            ],
            [
                'category' => 'clothing',
                'name'     => 'Women\'s Summer Dress',
                'desc'     => 'Floral print cotton summer dress, midi length. Sizes XS-3XL.',
                'price'    => 899, 'sale' => null, 'stock' => 35, 'featured' => false,
            ],
            // More Home
            [
                'category' => 'home-living',
                'name'     => 'Ethiopian Coffee Ceremony Set',
                'desc'     => 'Complete traditional coffee ceremony set: jebena, cups, tray, and incense holder.',
                'price'    => 1450, 'sale' => 1200, 'stock' => 18, 'featured' => true,
            ],
        ];

        foreach ($products as $p) {
            $category = Category::where('slug', $p['category'])->first();
            if (!$category) continue;

            $slug     = Str::slug($p['name']);
            $baseSlug = $slug;
            $counter  = 1;
            while (Product::where('slug', $slug)->exists()) {
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
