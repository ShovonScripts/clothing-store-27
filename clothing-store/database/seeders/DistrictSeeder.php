<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            // Inside Dhaka
            ['name' => 'Dhaka', 'name_bn' => 'ঢাকা', 'zone' => 'inside_dhaka', 'sort_order' => 1],
            ['name' => 'Gazipur', 'name_bn' => 'গাজীপুর', 'zone' => 'inside_dhaka', 'sort_order' => 2],
            ['name' => 'Narayanganj', 'name_bn' => 'নারায়ণগঞ্জ', 'zone' => 'inside_dhaka', 'sort_order' => 3],
            ['name' => 'Narsingdi', 'name_bn' => 'নরসিংদী', 'zone' => 'inside_dhaka', 'sort_order' => 4],
            ['name' => 'Munshiganj', 'name_bn' => 'মুন্সিগঞ্জ', 'zone' => 'inside_dhaka', 'sort_order' => 5],
            ['name' => 'Manikganj', 'name_bn' => 'মানিকগঞ্জ', 'zone' => 'inside_dhaka', 'sort_order' => 6],

            // Outside Dhaka
            ['name' => 'Faridpur', 'name_bn' => 'ফরিদপুর', 'zone' => 'outside_dhaka', 'sort_order' => 7],
            ['name' => 'Rajbari', 'name_bn' => 'রাজবাড়ী', 'zone' => 'outside_dhaka', 'sort_order' => 8],
            ['name' => 'Madaripur', 'name_bn' => 'মাদারীপুর', 'zone' => 'outside_dhaka', 'sort_order' => 9],
            ['name' => 'Shariatpur', 'name_bn' => 'শরীয়তপুর', 'zone' => 'outside_dhaka', 'sort_order' => 10],
            ['name' => 'Gopalganj', 'name_bn' => 'গোপালগঞ্জ', 'zone' => 'outside_dhaka', 'sort_order' => 11],

            ['name' => 'Tangail', 'name_bn' => 'টাঙ্গাইল', 'zone' => 'outside_dhaka', 'sort_order' => 12],
            ['name' => 'Kishoreganj', 'name_bn' => 'কিশোরগঞ্জ', 'zone' => 'outside_dhaka', 'sort_order' => 13],
            ['name' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'zone' => 'outside_dhaka', 'sort_order' => 14],
            ['name' => 'Netrokona', 'name_bn' => 'নেত্রকোনা', 'zone' => 'outside_dhaka', 'sort_order' => 15],
            ['name' => 'Sherpur', 'name_bn' => 'শেরপুর', 'zone' => 'outside_dhaka', 'sort_order' => 16],
            ['name' => 'Jamalpur', 'name_bn' => 'জামালপুর', 'zone' => 'outside_dhaka', 'sort_order' => 17],

            ['name' => 'Comilla', 'name_bn' => 'কুমিল্লা', 'zone' => 'outside_dhaka', 'sort_order' => 18],
            ['name' => 'Brahmanbaria', 'name_bn' => 'ব্রাহ্মণবাড়িয়া', 'zone' => 'outside_dhaka', 'sort_order' => 19],
            ['name' => 'Chandpur', 'name_bn' => 'চাঁদপুর', 'zone' => 'outside_dhaka', 'sort_order' => 20],
            ['name' => 'Lakshmipur', 'name_bn' => 'লক্ষ্মীপুর', 'zone' => 'outside_dhaka', 'sort_order' => 21],
            ['name' => 'Noakhali', 'name_bn' => 'নোয়াখালী', 'zone' => 'outside_dhaka', 'sort_order' => 22],
            ['name' => 'Feni', 'name_bn' => 'ফেনী', 'zone' => 'outside_dhaka', 'sort_order' => 23],

            ['name' => 'Chattogram', 'name_bn' => 'চট্টগ্রাম', 'zone' => 'outside_dhaka', 'sort_order' => 24],
            ['name' => 'Cox\'s Bazar', 'name_bn' => 'কক্সবাজার', 'zone' => 'outside_dhaka', 'sort_order' => 25],
            ['name' => 'Khagrachhari', 'name_bn' => 'খাগড়াছড়ি', 'zone' => 'outside_dhaka', 'sort_order' => 26],
            ['name' => 'Rangamati', 'name_bn' => 'রাঙ্গামাটি', 'zone' => 'outside_dhaka', 'sort_order' => 27],
            ['name' => 'Bandarban', 'name_bn' => 'বান্দরবান', 'zone' => 'outside_dhaka', 'sort_order' => 28],

            ['name' => 'Sylhet', 'name_bn' => 'সিলেট', 'zone' => 'outside_dhaka', 'sort_order' => 29],
            ['name' => 'Moulvibazar', 'name_bn' => 'মৌলভীবাজার', 'zone' => 'outside_dhaka', 'sort_order' => 30],
            ['name' => 'Habiganj', 'name_bn' => 'হবিগঞ্জ', 'zone' => 'outside_dhaka', 'sort_order' => 31],
            ['name' => 'Sunamganj', 'name_bn' => 'সুনামগঞ্জ', 'zone' => 'outside_dhaka', 'sort_order' => 32],

            ['name' => 'Rajshahi', 'name_bn' => 'রাজশাহী', 'zone' => 'outside_dhaka', 'sort_order' => 33],
            ['name' => 'Natore', 'name_bn' => 'নাটোর', 'zone' => 'outside_dhaka', 'sort_order' => 34],
            ['name' => 'Naogaon', 'name_bn' => 'নওগাঁ', 'zone' => 'outside_dhaka', 'sort_order' => 35],
            ['name' => 'Chapainawabganj', 'name_bn' => 'চাঁপাইনবাবগঞ্জ', 'zone' => 'outside_dhaka', 'sort_order' => 36],
            ['name' => 'Pabna', 'name_bn' => 'পাবনা', 'zone' => 'outside_dhaka', 'sort_order' => 37],
            ['name' => 'Sirajganj', 'name_bn' => 'সিরাজগঞ্জ', 'zone' => 'outside_dhaka', 'sort_order' => 38],

            ['name' => 'Bogra', 'name_bn' => 'বগুড়া', 'zone' => 'outside_dhaka', 'sort_order' => 39],
            ['name' => 'Joypurhat', 'name_bn' => 'জয়পুরহাট', 'zone' => 'outside_dhaka', 'sort_order' => 40],
            ['name' => 'Dinajpur', 'name_bn' => 'দিনাজপুর', 'zone' => 'outside_dhaka', 'sort_order' => 41],
            ['name' => 'Thakurgaon', 'name_bn' => 'ঠাকুরগাঁও', 'zone' => 'outside_dhaka', 'sort_order' => 42],
            ['name' => 'Panchagarh', 'name_bn' => 'পঞ্চগড়', 'zone' => 'outside_dhaka', 'sort_order' => 43],

            ['name' => 'Rangpur', 'name_bn' => 'রংপুর', 'zone' => 'outside_dhaka', 'sort_order' => 44],
            ['name' => 'Gaibandha', 'name_bn' => 'গাইবান্ধা', 'zone' => 'outside_dhaka', 'sort_order' => 45],
            ['name' => 'Kurigram', 'name_bn' => 'কুড়িগ্রাম', 'zone' => 'outside_dhaka', 'sort_order' => 46],
            ['name' => 'Lalmonirhat', 'name_bn' => 'লালমনিরহাট', 'zone' => 'outside_dhaka', 'sort_order' => 47],
            ['name' => 'Nilphamari', 'name_bn' => 'নীলফামারী', 'zone' => 'outside_dhaka', 'sort_order' => 48],

            ['name' => 'Khulna', 'name_bn' => 'খুলনা', 'zone' => 'outside_dhaka', 'sort_order' => 49],
            ['name' => 'Bagerhat', 'name_bn' => 'বাগেরহাট', 'zone' => 'outside_dhaka', 'sort_order' => 50],
            ['name' => 'Satkhira', 'name_bn' => 'সাতক্ষীরা', 'zone' => 'outside_dhaka', 'sort_order' => 51],
            ['name' => 'Jessore', 'name_bn' => 'যশোর', 'zone' => 'outside_dhaka', 'sort_order' => 52],
            ['name' => 'Narail', 'name_bn' => 'নড়াইল', 'zone' => 'outside_dhaka', 'sort_order' => 53],
            ['name' => 'Magura', 'name_bn' => 'মাগুরা', 'zone' => 'outside_dhaka', 'sort_order' => 54],
            ['name' => 'Jhenaidah', 'name_bn' => 'ঝিনাইদহ', 'zone' => 'outside_dhaka', 'sort_order' => 55],
            ['name' => 'Chuadanga', 'name_bn' => 'চুয়াডাঙ্গা', 'zone' => 'outside_dhaka', 'sort_order' => 56],
            ['name' => 'Meherpur', 'name_bn' => 'মেহেরপুর', 'zone' => 'outside_dhaka', 'sort_order' => 57],
            ['name' => 'Kushtia', 'name_bn' => 'কুষ্টিয়া', 'zone' => 'outside_dhaka', 'sort_order' => 58],

            ['name' => 'Barishal', 'name_bn' => 'বরিশাল', 'zone' => 'outside_dhaka', 'sort_order' => 59],
            ['name' => 'Barguna', 'name_bn' => 'বরগুনা', 'zone' => 'outside_dhaka', 'sort_order' => 60],
            ['name' => 'Bhola', 'name_bn' => 'ভোলা', 'zone' => 'outside_dhaka', 'sort_order' => 61],
            ['name' => 'Jhalokati', 'name_bn' => 'ঝালকাঠি', 'zone' => 'outside_dhaka', 'sort_order' => 62],
            ['name' => 'Patuakhali', 'name_bn' => 'পটুয়াখালী', 'zone' => 'outside_dhaka', 'sort_order' => 63],
            ['name' => 'Pirojpur', 'name_bn' => 'পিরোজপুর', 'zone' => 'outside_dhaka', 'sort_order' => 64],
        ];

        foreach ($districts as $district) {
            District::updateOrInsert(
                ['name' => $district['name']],
                $district
            );
        }

        $this->command?->info('Seeded '.count($districts).' districts.');
    }
}
