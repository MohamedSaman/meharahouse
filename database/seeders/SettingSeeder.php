<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name'         => 'Meharahouse',
            'site_tagline'      => 'Quality You Can Trust',
            'currency'          => 'ETB',
            'currency_symbol'   => 'ETB',
            'currency_position' => 'before',
            'tax_rate'          => '15',
            'free_shipping_min' => '500',
            'shipping_fee'      => '50',
            'contact_email'     => 'support@meharahouse.com',
            'contact_phone'     => '+251 911 000 000',
            'address'           => 'Bole Road, Addis Ababa, Ethiopia',
            'facebook_url'      => '#',
            'twitter_url'       => '#',
            'instagram_url'     => '#',
            'meta_description'  => 'Meharahouse — Ethiopia\'s premier online store for quality products at unbeatable prices.',
            'meta_keywords'     => 'meharahouse, ethiopia, online shop, electronics, clothing, beauty',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
