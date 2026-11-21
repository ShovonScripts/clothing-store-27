<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SizeChart;
use Illuminate\Database\Seeder;

class DemoProductSeeder extends Seeder
{
    public function run(): void
    {
        // Create size charts
        $tshirtChart = SizeChart::updateOrCreate(
            ['name' => 'T-Shirt / Polo (inches)'],
            [
                'table_json' => [
                    'columns' => ['Size', 'Chest', 'Length', 'Shoulder'],
                    'rows' => [
                        ['S', '36-38', '26-27', '16-17'],
                        ['M', '38-40', '27-28', '17-18'],
                        ['L', '40-42', '28-29', '18-19'],
                        ['XL', '42-44', '29-30', '19-20'],
                        ['XXL', '44-46', '30-31', '20-21'],
                        ['3XL', '46-48', '31-32', '21-22'],
                    ],
                ],
                'note' => 'Measure over light clothing',
            ]
        );

        $shirtChart = SizeChart::updateOrCreate(
            ['name' => 'Shirt (inches)'],
            [
                'table_json' => [
                    'columns' => ['Size', 'Chest', 'Length', 'Shoulder', 'Sleeve'],
                    'rows' => [
                        ['S', '38-40', '28-29', '17-18', '24-25'],
                        ['M', '40-42', '29-30', '18-19', '25-26'],
                        ['L', '42-44', '30-31', '19-20', '26-27'],
                        ['XL', '44-46', '31-32', '20-21', '27-28'],
                        ['XXL', '46-48', '32-33', '21-22', '28-29'],
                        ['3XL', '48-50', '33-34', '22-23', '29-30'],
                    ],
                ],
                'note' => 'Formal shirt measurements',
            ]
        );

        $pantsChart = SizeChart::updateOrCreate(
            ['name' => 'Pants / Jeans (inches)'],
            [
                'table_json' => [
                    'columns' => ['Size', 'Waist', 'Hip', 'Inseam'],
                    'rows' => [
                        ['28', '28-29', '36-37', '31-32'],
                        ['30', '30-31', '38-39', '31-32'],
                        ['32', '32-33', '40-41', '31-32'],
                        ['34', '34-35', '42-43', '31-32'],
                        ['36', '36-37', '44-45', '31-32'],
                        ['38', '38-39', '46-47', '31-32'],
                        ['40', '40-41', '48-49', '31-32'],
                    ],
                ],
                'note' => 'Bottom wear measurements',
            ]
        );

        $panjabiChart = SizeChart::updateOrCreate(
            ['name' => 'Panjabi (inches)'],
            [
                'table_json' => [
                    'columns' => ['Size', 'Chest', 'Length'],
                    'rows' => [
                        ['38', '38', '42'],
                        ['40', '40', '43'],
                        ['42', '42', '44'],
                        ['44', '44', '45'],
                    ],
                ],
                'note' => 'Panjabi chest and length',
            ]
        );

        $this->command?->info('Size charts seeded.');

        // Get colors
        $colors = Color::all()->keyBy('name');

        // Get categories
        $categories = Category::all()->keyBy('slug');

        // Product data: [name, slug, category_slug, price, compare_at, description, fabric, fit, care, size_chart, colors[], variants]
        $products = [
            // T-Shirts
            [
                'name' => 'Classic Cotton Crew Neck T-Shirt',
                'name_bn' => 'ক্লাসিক কটন ক্রู নেক টি-শার্ট',
                'slug' => 'classic-cotton-crew-neck-t-shirt',
                'category' => 't-shirts',
                'price' => 850,
                'compare_at_price' => 1100,
                'description' => 'Premium 100% cotton crew neck t-shirt. Soft, breathable, and perfect for everyday wear.',
                'fabric' => '100% Cotton',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Black', 'White', 'Navy', 'Grey', 'Olive'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => true,
                'is_new' => true,
                'stock_per_variant' => 25,
            ],
            [
                'name' => 'V-Neck Premium T-Shirt',
                'name_bn' => 'ভি-নেক প্রিমিয়াম টি-শার্ট',
                'slug' => 'v-neck-premium-t-shirt',
                'category' => 't-shirts',
                'price' => 950,
                'compare_at_price' => 1250,
                'description' => 'Slim fit V-neck t-shirt in premium cotton blend. Perfect for layering.',
                'fabric' => '95% Cotton, 5% Elastane',
                'fit' => 'slim',
                'care' => 'Machine wash cold, hang dry',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Black', 'White', 'Charcoal', 'Navy'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 20,
            ],
            [
                'name' => 'Graphic Print T-Shirt',
                'name_bn' => 'গ্রাফিক প্রিন্ট টি-শার্ট',
                'slug' => 'graphic-print-t-shirt',
                'category' => 't-shirts',
                'price' => 1050,
                'compare_at_price' => 1400,
                'description' => 'Trendy graphic print on soft cotton. Express your style.',
                'fabric' => '100% Cotton',
                'fit' => 'regular',
                'care' => 'Machine wash cold, iron inside out',
                'size_chart' => $tshirtChart->id,
                'colors' => ['White', 'Black', 'Olive', 'Beige'],
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 15,
            ],

            // Polo Shirts
            [
                'name' => 'Classic Pique Polo Shirt',
                'name_bn' => 'ক্লাসিক পিক্যো পোলো শার্ট',
                'slug' => 'classic-pique-polo-shirt',
                'category' => 'polo-shirts',
                'price' => 1350,
                'compare_at_price' => 1700,
                'description' => 'Classic pique cotton polo with ribbed collar and cuffs. Smart casual essential.',
                'fabric' => '100% Cotton Pique',
                'fit' => 'regular',
                'care' => 'Machine wash cold, iron medium',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Navy', 'Black', 'White', 'Sky Blue', 'Maroon'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 30,
            ],
            [
                'name' => 'Slim Fit Performance Polo',
                'name_bn' => 'স্লিম ফিট পারফরম্যান্স পোলো',
                'slug' => 'slim-fit-performance-polo',
                'category' => 'polo-shirts',
                'price' => 1550,
                'compare_at_price' => 1950,
                'description' => 'Moisture-wicking performance polo with stretch. Ideal for active days.',
                'fabric' => '88% Polyester, 12% Elastane',
                'fit' => 'slim',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Black', 'Navy', 'Charcoal', 'Olive'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 20,
            ],

            // Casual Shirts
            [
                'name' => 'Oxford Button-Down Shirt',
                'name_bn' => 'অক্সফোর্ড বাটন-ডাউন শার্ট',
                'slug' => 'oxford-button-down-shirt',
                'category' => 'casual-shirts',
                'price' => 1750,
                'compare_at_price' => 2200,
                'description' => 'Classic Oxford cloth button-down. Versatile for work or weekend.',
                'fabric' => '100% Cotton Oxford',
                'fit' => 'regular',
                'care' => 'Machine wash cold, iron while damp',
                'size_chart' => $shirtChart->id,
                'colors' => ['White', 'Sky Blue', 'Navy', 'Olive'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 25,
            ],
            [
                'name' => 'Linen Blend Casual Shirt',
                'name_bn' => 'লিনেন ব্লেন্ড ক্যাজুয়াল শার্ট',
                'slug' => 'linen-blend-casual-shirt',
                'category' => 'casual-shirts',
                'price' => 1950,
                'compare_at_price' => 2500,
                'description' => 'Lightweight linen blend shirt. Breathable and stylish for hot days.',
                'fabric' => '55% Linen, 45% Cotton',
                'fit' => 'relaxed',
                'care' => 'Hand wash or gentle cycle, line dry',
                'size_chart' => $shirtChart->id,
                'colors' => ['White', 'Beige', 'Sky Blue', 'Olive'],
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 15,
            ],
            [
                'name' => 'Flannel Check Shirt',
                'name_bn' => 'ফ্ল্যানেল চেক শার্ট',
                'slug' => 'flannel-check-shirt',
                'category' => 'casual-shirts',
                'price' => 1650,
                'compare_at_price' => 2100,
                'description' => 'Warm flannel shirt in classic check pattern. Perfect for cooler weather.',
                'fabric' => '100% Cotton Flannel',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $shirtChart->id,
                'colors' => ['Navy', 'Maroon', 'Olive', 'Charcoal'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 20,
            ],

            // Formal Shirts
            [
                'name' => 'Premium Poplin Formal Shirt',
                'name_bn' => 'প্রিমিয়াম পপলিন ফরম্যাল শার্ট',
                'slug' => 'premium-poplin-formal-shirt',
                'category' => 'formal-shirts',
                'price' => 2150,
                'compare_at_price' => 2800,
                'description' => 'Crisp poplin cotton formal shirt. Non-iron finish for all-day sharpness.',
                'fabric' => '100% Cotton Poplin',
                'fit' => 'slim',
                'care' => 'Machine wash cold, iron medium',
                'size_chart' => $shirtChart->id,
                'colors' => ['White', 'Sky Blue', 'Navy'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL', '3XL'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 30,
            ],
            [
                'name' => 'Twill Texture Formal Shirt',
                'name_bn' => 'টোয়াল টেক্সচার ফরম্যাল শার্ট',
                'slug' => 'twill-texture-formal-shirt',
                'category' => 'formal-shirts',
                'price' => 2350,
                'compare_at_price' => 3000,
                'description' => 'Subtle twill texture adds sophistication. Wrinkle-resistant finish.',
                'fabric' => '100% Cotton Twill',
                'fit' => 'regular',
                'care' => 'Machine wash cold, iron medium',
                'size_chart' => $shirtChart->id,
                'colors' => ['White', 'Sky Blue', 'Charcoal'],
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 20,
            ],

            // Panjabi
            [
                'name' => 'Classic Cotton Panjabi',
                'name_bn' => 'ক্লাসিক কটন পঞ্জাবি',
                'slug' => 'classic-cotton-panjabi',
                'category' => 'panjabi',
                'price' => 2550,
                'compare_at_price' => 3200,
                'description' => 'Traditional cotton panjabi with mandarin collar. Perfect for Eid and occasions.',
                'fabric' => '100% Cotton',
                'fit' => 'regular',
                'care' => 'Dry clean recommended',
                'size_chart' => $panjabiChart->id,
                'colors' => ['White', 'Cream', 'Beige', 'Sky Blue'],
                'sizes' => ['38', '40', '42', '44'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 15,
            ],
            [
                'name' => 'Silk Blend Embroidered Panjabi',
                'name_bn' => 'সিল্ক ব্লেন্ড এমব্রয়েডার্ড পঞ্জাবি',
                'slug' => 'silk-blend-embroidered-panjabi',
                'category' => 'panjabi',
                'price' => 4250,
                'compare_at_price' => 5500,
                'description' => 'Luxury silk blend with subtle embroidery. For special celebrations.',
                'fabric' => '70% Silk, 30% Cotton',
                'fit' => 'regular',
                'care' => 'Dry clean only',
                'size_chart' => $panjabiChart->id,
                'colors' => ['Cream', 'Beige', 'Maroon', 'Navy'],
                'sizes' => ['38', '40', '42', '44'],
                'is_featured' => true,
                'is_new' => true,
                'stock_per_variant' => 10,
            ],
            [
                'name' => 'Linen Panjabi',
                'name_bn' => 'লিনেন পঞ্জাবি',
                'slug' => 'linen-panjabi',
                'category' => 'panjabi',
                'price' => 2850,
                'compare_at_price' => 3600,
                'description' => 'Breathable linen panjabi for summer occasions. Relaxed elegant fit.',
                'fabric' => '100% Linen',
                'fit' => 'relaxed',
                'care' => 'Hand wash, line dry',
                'size_chart' => $panjabiChart->id,
                'colors' => ['White', 'Beige', 'Sky Blue', 'Olive'],
                'sizes' => ['38', '40', '42', '44'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 12,
            ],

            // Pants & Trousers
            [
                'name' => 'Chino Trousers',
                'name_bn' => 'চিনো ট্রাউজার্স',
                'slug' => 'chino-trousers',
                'category' => 'pants-trousers',
                'price' => 1850,
                'compare_at_price' => 2400,
                'description' => 'Classic chino trousers with subtle stretch. Office to weekend versatile.',
                'fabric' => '97% Cotton, 3% Elastane',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $pantsChart->id,
                'colors' => ['Navy', 'Charcoal', 'Olive', 'Beige', 'Black'],
                'sizes' => ['30', '32', '34', '36', '38'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 25,
            ],
            [
                'name' => 'Formal Wool-Blend Trousers',
                'name_bn' => 'ফরম্যাল উল-ব্লেন্ড ট্রাউজার্স',
                'slug' => 'formal-wool-blend-trousers',
                'category' => 'pants-trousers',
                'price' => 2450,
                'compare_at_price' => 3100,
                'description' => 'Premium wool-blend trousers for business formal. Sharp crease retention.',
                'fabric' => '70% Wool, 30% Polyester',
                'fit' => 'regular',
                'care' => 'Dry clean only',
                'size_chart' => $pantsChart->id,
                'colors' => ['Charcoal', 'Navy', 'Black'],
                'sizes' => ['30', '32', '34', '36', '38'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 15,
            ],
            [
                'name' => 'Cargo Pants',
                'name_bn' => 'কার্গো প্যান্টস',
                'slug' => 'cargo-pants',
                'category' => 'pants-trousers',
                'price' => 1750,
                'compare_at_price' => 2200,
                'description' => 'Functional cargo pants with multiple pockets. Durable cotton twill.',
                'fabric' => '100% Cotton Twill',
                'fit' => 'relaxed',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $pantsChart->id,
                'colors' => ['Olive', 'Charcoal', 'Beige', 'Navy'],
                'sizes' => ['30', '32', '34', '36', '38'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 20,
            ],

            // Jeans
            [
                'name' => 'Slim Fit Dark Wash Jeans',
                'name_bn' => 'স্লিম ফিট ডার্ক واش জিনস',
                'slug' => 'slim-fit-dark-wash-jeans',
                'category' => 'jeans',
                'price' => 2250,
                'compare_at_price' => 2800,
                'description' => 'Classic dark indigo slim fit jeans. Stretch denim for comfort.',
                'fabric' => '98% Cotton, 2% Elastane',
                'fit' => 'slim',
                'care' => 'Machine wash cold inside out, hang dry',
                'size_chart' => $pantsChart->id,
                'colors' => ['Navy'],
                'sizes' => ['30', '32', '34', '36', '38'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 30,
            ],
            [
                'name' => 'Regular Fit Mid Wash Jeans',
                'name_bn' => 'রেগুলার ফিট মিড ওয়াশ জিনস',
                'slug' => 'regular-fit-mid-wash-jeans',
                'category' => 'jeans',
                'price' => 2150,
                'compare_at_price' => 2700,
                'description' => 'Comfortable regular fit in medium blue wash. Everyday essential.',
                'fabric' => '100% Cotton Denim',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $pantsChart->id,
                'colors' => ['Sky Blue'],
                'sizes' => ['30', '32', '34', '36', '38', '40'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 25,
            ],
            [
                'name' => 'Black Skinny Jeans',
                'name_bn' => 'ব্ল্যাক স্কিনি জিনস',
                'slug' => 'black-skinny-jeans',
                'category' => 'jeans',
                'price' => 2350,
                'compare_at_price' => 3000,
                'description' => 'Sleek black skinny jeans with high stretch. Modern sharp look.',
                'fabric' => '90% Cotton, 8% Polyester, 2% Elastane',
                'fit' => 'slim',
                'care' => 'Machine wash cold inside out, hang dry',
                'size_chart' => $pantsChart->id,
                'colors' => ['Black'],
                'sizes' => ['30', '32', '34', '36'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 20,
            ],

            // Hoodies & Sweatshirts
            [
                'name' => 'Heavyweight Cotton Hoodie',
                'name_bn' => 'হевиওয়েট কটন হуди',
                'slug' => 'heavyweight-cotton-hoodie',
                'category' => 'hoodies-sweatshirts',
                'price' => 2450,
                'compare_at_price' => 3100,
                'description' => 'Premium heavyweight cotton hoodie. Brushed fleece interior for warmth.',
                'fabric' => '100% Cotton Fleece (320 GSM)',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Black', 'Charcoal', 'Navy', 'Olive', 'Maroon'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 20,
            ],
            [
                'name' => 'Crew Neck Sweatshirt',
                'name_bn' => 'ক্রিউ নেক সুয়েটশার্ট',
                'slug' => 'crew-neck-sweatshirt',
                'category' => 'hoodies-sweatshirts',
                'price' => 1950,
                'compare_at_price' => 2500,
                'description' => 'Classic crew neck sweatshirt. Soft brushed interior, ribbed cuffs and hem.',
                'fabric' => '80% Cotton, 20% Polyester Fleece',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Black', 'Grey', 'Navy', 'Maroon'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 25,
            ],
            [
                'name' => 'Zip-Up Hoodie',
                'name_bn' => 'জিপ-আপ হуди',
                'slug' => 'zip-up-hoodie',
                'category' => 'hoodies-sweatshirts',
                'price' => 2650,
                'compare_at_price' => 3300,
                'description' => 'Full-zip hoodie with side pockets. Easy on/off for layering.',
                'fabric' => '100% Cotton Fleece (300 GSM)',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $tshirtChart->id,
                'colors' => ['Black', 'Charcoal', 'Navy', 'Olive'],
                'sizes' => ['S', 'M', 'L', 'XL'],
                'is_featured' => true,
                'is_new' => true,
                'stock_per_variant' => 15,
            ],

            // Jackets & Winter
            [
                'name' => 'Bomber Jacket',
                'name_bn' => 'বমবার জ্যাকেট',
                'slug' => 'bomber-jacket',
                'category' => 'jackets-winter',
                'price' => 3450,
                'compare_at_price' => 4300,
                'description' => 'Classic bomber jacket with ribbed collar, cuffs, and hem. Water-resistant.',
                'fabric' => '100% Nylon Shell, Polyester Fill',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $shirtChart->id,
                'colors' => ['Black', 'Olive', 'Navy', 'Charcoal'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => true,
                'is_new' => false,
                'stock_per_variant' => 15,
            ],
            [
                'name' => 'Denim Trucker Jacket',
                'name_bn' => 'ডেনিম ট্রাকার জ্যাকেট',
                'slug' => 'denim-trucker-jacket',
                'category' => 'jackets-winter',
                'price' => 2850,
                'compare_at_price' => 3600,
                'description' => 'Classic denim trucker jacket. Ages beautifully with wear.',
                'fabric' => '100% Cotton Denim',
                'fit' => 'regular',
                'care' => 'Machine wash cold, hang dry',
                'size_chart' => $shirtChart->id,
                'colors' => ['Sky Blue', 'Navy', 'Black'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => true,
                'stock_per_variant' => 20,
            ],
            [
                'name' => 'Puffer Vest',
                'name_bn' => 'পাফার ভেস্ট',
                'slug' => 'puffer-vest',
                'category' => 'jackets-winter',
                'price' => 2250,
                'compare_at_price' => 2900,
                'description' => 'Lightweight puffer vest with synthetic insulation. Packable and warm.',
                'fabric' => 'Nylon Ripstop, Polyester Fill',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => $shirtChart->id,
                'colors' => ['Black', 'Navy', 'Olive', 'Maroon'],
                'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 18,
            ],

            // Innerwear & Basics
            [
                'name' => 'Pack of 3 Boxer Briefs',
                'name_bn' => '৩টি বক্সার ব্রিফের প্যাক',
                'slug' => 'pack-of-3-boxer-briefs',
                'category' => 'innerwear-basics',
                'price' => 750,
                'compare_at_price' => 950,
                'description' => 'Soft cotton blend boxer briefs. Elastic waistband, no-ride-up legs.',
                'fabric' => '95% Cotton, 5% Elastane',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => null,
                'colors' => ['Black', 'Navy', 'Grey'],
                'sizes' => ['M', 'L', 'XL', 'XXL'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 50,
            ],
            [
                'name' => 'Pack of 5 Crew Socks',
                'name_bn' => '৫টি ক্রিউ সকসের প্যাক',
                'slug' => 'pack-of-5-crew-socks',
                'category' => 'innerwear-basics',
                'price' => 550,
                'compare_at_price' => 700,
                'description' => 'Comfortable cotton crew socks with reinforced heel and toe.',
                'fabric' => '75% Cotton, 22% Polyester, 3% Elastane',
                'fit' => 'regular',
                'care' => 'Machine wash cold, tumble dry low',
                'size_chart' => null,
                'colors' => ['Black', 'Navy', 'Charcoal', 'White'],
                'sizes' => ['Free'],
                'is_featured' => false,
                'is_new' => false,
                'stock_per_variant' => 60,
            ],
        ];

        foreach ($products as $productData) {
            $category = $categories[$productData['category']] ?? null;
            if (! $category) {
                $this->command?->warn("Category not found for {$productData['slug']}");

                continue;
            }

            $sizeChartId = $productData['size_chart'] ?? null;

            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'name_bn' => $productData['name_bn'],
                    'sku' => strtoupper(str_replace('-', '', $productData['slug'])),
                    'short_description' => substr(strip_tags($productData['description']), 0, 200),
                    'description' => $productData['description'],
                    'fabric' => $productData['fabric'],
                    'fit' => $productData['fit'],
                    'care' => $productData['care'],
                    'price' => $productData['price'],
                    'compare_at_price' => $productData['compare_at_price'],
                    'cost_price' => (int) ($productData['price'] * 0.45),
                    'is_active' => true,
                    'is_featured' => $productData['is_featured'] ?? false,
                    'is_new' => $productData['is_new'] ?? false,
                    'size_chart_id' => $sizeChartId,
                    'published_at' => now(),
                ]
            );

            $colorNames = $productData['colors'];
            $sizes = $productData['sizes'];
            $stock = $productData['stock_per_variant'];

            foreach ($colorNames as $colorName) {
                $color = $colors[$colorName] ?? null;
                if (! $color) {
                    continue;
                }

                foreach ($sizes as $size) {
                    $variantSku = $product->sku.'-'.strtoupper(substr($color->name, 0, 3)).'-'.$size;

                    ProductVariant::updateOrCreate(
                        ['sku' => $variantSku],
                        [
                            'product_id' => $product->id,
                            'size' => $size,
                            'color_id' => $color->id,
                            'price' => null, // use product price
                            'stock' => $stock,
                            'low_stock_threshold' => 5,
                            'is_active' => true,
                            'sort_order' => 0,
                        ]
                    );
                }
            }
        }

        $this->command?->info('Seeded '.count($products).' demo products with variants.');
    }
}
