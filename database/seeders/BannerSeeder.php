<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    public function run(): void
    {
        $banners = [
            [
                'title'      => 'Quality You Can Trust',
                'subtitle'   => 'Shop Ethiopia\'s finest products — electronics, fashion, beauty, and more delivered to your door.',
                'link'       => '/shop',
                'button_text'=> 'Shop Now',
                'sort_order' => 1,
            ],
            [
                'title'      => 'New Season Arrivals',
                'subtitle'   => 'Discover the latest fashion trends and new product collections for this season.',
                'link'       => '/shop?category=clothing',
                'button_text'=> 'Explore Collection',
                'sort_order' => 2,
            ],
            [
                'title'      => 'Up to 40% Off Electronics',
                'subtitle'   => 'Grab amazing deals on smartphones, laptops, audio, and more. Limited time offer!',
                'link'       => '/shop?category=electronics',
                'button_text'=> 'View Deals',
                'sort_order' => 3,
            ],
            [
                'title'      => 'Authentic Ethiopian Products',
                'subtitle'   => 'Celebrate Ethiopian culture with our curated selection of traditional crafts and foods.',
                'link'       => '/shop?category=food-drinks',
                'button_text'=> 'Discover',
                'sort_order' => 4,
            ],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['title' => $banner['title']],
                array_merge($banner, ['is_active' => true])
            );
        }
    }
}
