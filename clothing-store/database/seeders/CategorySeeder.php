<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Root categories
            [
                'name' => 'T-Shirts',
                'name_bn' => 'টি-শার্ট',
                'slug' => 't-shirts',
                'description' => 'Comfortable cotton t-shirts for everyday wear',
                'sort_order' => 1,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Polo Shirts',
                'name_bn' => 'পোলো শার্ট',
                'slug' => 'polo-shirts',
                'description' => 'Smart casual polo shirts',
                'sort_order' => 2,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Shirts',
                'name_bn' => 'শার্ট',
                'slug' => 'shirts',
                'description' => 'Formal and casual shirts',
                'sort_order' => 3,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Panjabi',
                'name_bn' => 'পঞ্জাবি',
                'slug' => 'panjabi',
                'description' => 'Traditional and modern panjabis',
                'sort_order' => 4,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Fatua',
                'name_bn' => 'ফটুয়া',
                'slug' => 'fatua',
                'description' => 'Traditional fatua for special occasions',
                'sort_order' => 5,
                'show_on_home' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Pants & Trousers',
                'name_bn' => 'প্যান্ট অ্যান্ড ট্রাউজার্স',
                'slug' => 'pants-trousers',
                'description' => 'Formal trousers and casual pants',
                'sort_order' => 6,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Jeans',
                'name_bn' => 'জিনস',
                'slug' => 'jeans',
                'description' => 'Denim jeans in various fits',
                'sort_order' => 7,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Hoodies & Sweatshirts',
                'name_bn' => 'হуди অ্যান্ড সুয়েটশার্ট',
                'slug' => 'hoodies-sweatshirts',
                'description' => 'Warm hoodies and sweatshirts for winter',
                'sort_order' => 8,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Jackets & Winter',
                'name_bn' => 'জ্যাকেট অ্যান্ড উইন্টার',
                'slug' => 'jackets-winter',
                'description' => 'Jackets and winter wear',
                'sort_order' => 9,
                'show_on_home' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Innerwear & Basics',
                'name_bn' => 'ইনারওয়্যার অ্যান্ড বেসিক্স',
                'slug' => 'innerwear-basics',
                'description' => 'Essential innerwear and basics',
                'sort_order' => 10,
                'show_on_home' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Accessories',
                'name_bn' => 'এক্সেসরিজ',
                'slug' => 'accessories',
                'description' => 'Belts, wallets, caps, socks',
                'sort_order' => 11,
                'show_on_home' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Footwear',
                'name_bn' => 'ফুটওয়েয়ার',
                'slug' => 'footwear',
                'description' => 'Shoes and sandals',
                'sort_order' => 12,
                'show_on_home' => false,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        // Add subcategories for Shirts
        $shirts = Category::where('slug', 'shirts')->first();
        if ($shirts) {
            $subCategories = [
                [
                    'parent_id' => $shirts->id,
                    'name' => 'Casual Shirts',
                    'name_bn' => 'ক্যাজুয়াল শার্ট',
                    'slug' => 'casual-shirts',
                    'description' => 'Casual button-down shirts',
                    'sort_order' => 1,
                    'is_active' => true,
                ],
                [
                    'parent_id' => $shirts->id,
                    'name' => 'Formal Shirts',
                    'name_bn' => 'ফরম্যাল শার্ট',
                    'slug' => 'formal-shirts',
                    'description' => 'Formal dress shirts',
                    'sort_order' => 2,
                    'is_active' => true,
                ],
                [
                    'parent_id' => $shirts->id,
                    'name' => 'Linen Shirts',
                    'name_bn' => 'লিনেন শার্ট',
                    'slug' => 'linen-shirts',
                    'description' => 'Breathable linen shirts',
                    'sort_order' => 3,
                    'is_active' => true,
                ],
            ];

            foreach ($subCategories as $sub) {
                Category::updateOrCreate(
                    ['slug' => $sub['slug']],
                    $sub
                );
            }
        }

        $this->command?->info('Seeded categories with subcategories.');
    }
}
