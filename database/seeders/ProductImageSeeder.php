<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        // Unsplash images mapped by product name.
        // Each product gets 2–3 images (main + alternate views).
        $images = [

            // ── Abaya ──────────────────────────────────────────────────────
            'Classic Black Abaya' => [
                'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1596783074918-c84cb06531ca?auto=format&fit=crop&w=600&q=80',
            ],
            'Butterfly Abaya — Navy Blue' => [
                'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=600&q=80',
            ],
            'Open Front Abaya — Olive Green' => [
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=600&q=80',
            ],
            'Striped Abaya — Black & White' => [
                'https://images.unsplash.com/photo-1567401893414-76b7b1e5a7a5?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Jilbab ─────────────────────────────────────────────────────
            'Two-Piece Jilbab Set — Black' => [
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=600&q=80',
            ],
            'Jersey Jilbab — Dusty Rose' => [
                'https://images.unsplash.com/photo-1551232864-3f0890e580d9?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=600&q=80',
            ],
            'Nida Jilbab — Dark Grey' => [
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Casual Abaya ───────────────────────────────────────────────
            'Linen Casual Abaya — Beige' => [
                'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600&q=80',
            ],
            'Cotton Casual Abaya — White' => [
                'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=600&q=80',
            ],
            'Denim Abaya — Indigo' => [
                'https://images.unsplash.com/photo-1546961342-ea5f62d5a27b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1469334031218-e382a71b716b?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Formal Abaya ───────────────────────────────────────────────
            'Embroidered Abaya — Midnight Black' => [
                'https://images.unsplash.com/photo-1607522370275-f6fd4adb1f34?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1596783074918-c84cb06531ca?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1509631179647-0177331693ae?auto=format&fit=crop&w=600&q=80',
            ],
            'Velvet Formal Abaya — Burgundy' => [
                'https://images.unsplash.com/photo-1624913503273-5f9c4e980dba?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1551232864-3f0890e580d9?auto=format&fit=crop&w=600&q=80',
            ],
            'Lace Overlay Abaya — Ivory' => [
                'https://images.unsplash.com/photo-1614676471928-2ed0ad1061a4?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Kids Abaya ─────────────────────────────────────────────────
            'Girls Abaya — Pink Floral' => [
                'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?auto=format&fit=crop&w=600&q=80',
            ],
            'Girls Casual Abaya — Black' => [
                'https://images.unsplash.com/photo-1476234251651-f353703a034d?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?auto=format&fit=crop&w=600&q=80',
            ],
            'Girls Embroidered Abaya — Lavender' => [
                'https://images.unsplash.com/photo-1515488042361-ee00e0ddd4e4?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1476234251651-f353703a034d?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Inner Dress ────────────────────────────────────────────────
            'Full-Length Inner Slip Dress — White' => [
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?auto=format&fit=crop&w=600&q=80',
            ],
            'Full-Length Inner Slip Dress — Black' => [
                'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1582719508461-905c673771fd?auto=format&fit=crop&w=600&q=80',
            ],
            'Long Inner Dress with Sleeves — Nude' => [
                'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Innerwear Top ──────────────────────────────────────────────
            'Modest Camisole — White' => [
                'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=600&q=80',
            ],
            'Long-Sleeve Undershirt — Black' => [
                'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?auto=format&fit=crop&w=600&q=80',
            ],
            'Cotton Modest Undershirt — Skin' => [
                'https://images.unsplash.com/photo-1591369822096-ffd140ec948f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?auto=format&fit=crop&w=600&q=80',
            ],

            // ── Innerwear Bottom ───────────────────────────────────────────
            'Full-Length Leggings — Black' => [
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=600&q=80',
            ],
            'Underskirt Petticoat — White' => [
                'https://images.unsplash.com/photo-1614676471928-2ed0ad1061a4?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=600&q=80',
            ],
            'Wide-Leg Undertrousers — Nude' => [
                'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?auto=format&fit=crop&w=600&q=80',
                'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?auto=format&fit=crop&w=600&q=80',
            ],
        ];

        $updated = 0;
        foreach ($images as $productName => $urls) {
            $rows = Product::where('name', $productName)->update(['images' => $urls]);
            $updated += $rows;
        }

        $this->command->info("Product images updated: {$updated} products.");
    }
}
