<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Area;
use App\Models\District;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    public function run(): void
    {
        $dhaka = District::where('name', 'Dhaka')->first();
        $gazipur = District::where('name', 'Gazipur')->first();
        $narayanganj = District::where('name', 'Narayanganj')->first();

        if (! $dhaka || ! $gazipur || ! $narayanganj) {
            $this->command?->error('Required districts not found. Run DistrictSeeder first.');

            return;
        }

        $areas = [
            // Dhaka - inside city
            ['district_id' => $dhaka->id, 'name' => 'Mirpur', 'name_bn' => 'মিরপুর', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Uttara', 'name_bn' => 'উত্তরা', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Dhanmondi', 'name_bn' => 'ধানমন্ডি', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Mohammadpur', 'name_bn' => 'মোহাম্মদপুর', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Gulshan', 'name_bn' => 'গুলশান', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Banani', 'name_bn' => 'বনানী', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Baridhara', 'name_bn' => 'বরিধারা', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Badda', 'name_bn' => 'বাড্ডা', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Jatrabari', 'name_bn' => 'যাত্রাবাড়ী', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Motijheel', 'name_bn' => 'মতিঝিল', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Shahbagh', 'name_bn' => 'শাহবাগ', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Ramna', 'name_bn' => 'রমনা', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Tejgaon', 'name_bn' => 'তেজগাঁও', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Kafrul', 'name_bn' => 'কাফরুল', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Pallabi', 'name_bn' => 'পল্লবী', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Shah Ali', 'name_bn' => 'শাহ আলী', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Khilgaon', 'name_bn' => 'খিলগাঁও', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Sabujbagh', 'name_bn' => 'সবুজবাগ', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Demra', 'name_bn' => 'ডেমরা', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Shyampur', 'name_bn' => 'শ্যামপুর', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Hazaribagh', 'name_bn' => 'হাজারীবাগ', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Lalbagh', 'name_bn' => 'লালবাগ', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Kotwali', 'name_bn' => 'কোতোয়ালি', 'is_inside_city' => true],
            ['district_id' => $dhaka->id, 'name' => 'Sutrapur', 'name_bn' => 'সুত্রাপুর', 'is_inside_city' => true],

            // Dhaka - outside city
            ['district_id' => $dhaka->id, 'name' => 'Savar', 'name_bn' => 'সাভার', 'is_inside_city' => false],
            ['district_id' => $dhaka->id, 'name' => 'Keraniganj', 'name_bn' => 'কেরানীগঞ্জ', 'is_inside_city' => false],
            ['district_id' => $dhaka->id, 'name' => 'Dohar', 'name_bn' => 'দোহার', 'is_inside_city' => false],
            ['district_id' => $dhaka->id, 'name' => 'Nawabganj', 'name_bn' => 'নবাবগঞ্জ', 'is_inside_city' => false],

            // Gazipur
            ['district_id' => $gazipur->id, 'name' => 'Gazipur Sadar', 'name_bn' => 'গাজীপুর সদর', 'is_inside_city' => false],
            ['district_id' => $gazipur->id, 'name' => 'Tongi', 'name_bn' => 'টঙ্গি', 'is_inside_city' => false],
            ['district_id' => $gazipur->id, 'name' => 'Kaliakair', 'name_bn' => 'কালিয়াকৈর', 'is_inside_city' => false],
            ['district_id' => $gazipur->id, 'name' => 'Kaliganj', 'name_bn' => 'কালীগঞ্জ', 'is_inside_city' => false],
            ['district_id' => $gazipur->id, 'name' => 'Kapasia', 'name_bn' => 'কাপাসিয়া', 'is_inside_city' => false],
            ['district_id' => $gazipur->id, 'name' => 'Sreepur', 'name_bn' => 'শ্রীপুর', 'is_inside_city' => false],

            // Narayanganj
            ['district_id' => $narayanganj->id, 'name' => 'Narayanganj Sadar', 'name_bn' => 'নারায়ণগঞ্জ সদর', 'is_inside_city' => false],
            ['district_id' => $narayanganj->id, 'name' => 'Bandar', 'name_bn' => 'বন্দর', 'is_inside_city' => false],
            ['district_id' => $narayanganj->id, 'name' => 'Sonargaon', 'name_bn' => 'সোনারগাঁও', 'is_inside_city' => false],
            ['district_id' => $narayanganj->id, 'name' => 'Rupganj', 'name_bn' => 'রূপগঞ্জ', 'is_inside_city' => false],
            ['district_id' => $narayanganj->id, 'name' => 'Araihazar', 'name_bn' => 'আড়াইহাজার', 'is_inside_city' => false],
        ];

        foreach ($areas as $area) {
            Area::updateOrInsert(
                ['district_id' => $area['district_id'], 'name' => $area['name']],
                $area
            );
        }

        $this->command?->info('Seeded '.count($areas).' areas for Dhaka, Gazipur, Narayanganj.');
    }
}
