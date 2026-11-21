<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SizeChart;
use Illuminate\Database\Seeder;

class SizeChartSeeder extends Seeder
{
    public function run(): void
    {
        $charts = [
            [
                'name' => 'T-Shirt / Polo (inches)',
                'name_bn' => 'টি-শার্ট / পোলো (ইঞ্চি)',
                'headers' => ['Size', 'Chest', 'Length', 'Shoulder'],
                'rows' => [
                    ['S', '36-38', '26-27', '16-17'],
                    ['M', '38-40', '27-28', '17-18'],
                    ['L', '40-42', '28-29', '17-18'],
                    ['XL', '42-44', '29-30', '18-19'],
                    ['XXL', '44-46', '30-31', '18-19'],
                    ['3XL', '46-48', '31-32', '19-20'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Shirt (inches)',
                'name_bn' => 'শার্ট (ইঞ্চি)',
                'headers' => ['Size', 'Chest', 'Length', 'Shoulder', 'Sleeve'],
                'rows' => [
                    ['S', '38-40', '28-29', '17-18', '24-25'],
                    ['M', '40-42', '29-30', '18-19', '25-26'],
                    ['L', '42-44', '30-31', '19-20', '25-26'],
                    ['XL', '44-46', '31-32', '20-21', '26-27'],
                    ['XXL', '46-48', '31-32', '21-22', '26-27'],
                    ['3XL', '48-50', '33-34', '22-23', '27-28'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Pants / Jeans (inches)',
                'name_bn' => 'প্যান্ট / জিনস (ইঞ্চি)',
                'headers' => ['Size', 'Waist', 'Hip', 'Inseam'],
                'rows' => [
                    ['28', '28-29', '36-37', '31-32'],
                    ['30', '30-31', '38-39', '31-32'],
                    ['32', '32-33', '40-41', '31-32'],
                    ['34', '34-35', '42-43', '31-32'],
                    ['36', '36-37', '44-45', '31-32'],
                    ['38', '38-39', '46-47', '31-32'],
                    ['40', '40-41', '48-49', '31-32'],
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Panjabi (inches)',
                'name_bn' => 'পঞ্জাবি (ইঞ্চি)',
                'headers' => ['Size', 'Chest', 'Length'],
                'rows' => [
                    ['38', '38', '42'],
                    ['40', '40', '43'],
                    ['42', '42', '44'],
                    ['44', '44', '45'],
                ],
                'is_active' => true,
            ],
        ];

        foreach ($charts as $chart) {
            SizeChart::updateOrCreate(
                ['name' => $chart['name']],
                $chart
            );
        }

        $this->command?->info('Seeded '.count($charts).' size charts.');
    }
}
