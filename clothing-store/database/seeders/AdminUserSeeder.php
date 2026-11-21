<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command?->warn('ADMIN_EMAIL and ADMIN_PASSWORD must be set in .env to create admin user. Skipping.');

            return;
        }

        if (app()->isProduction() && $password === 'password123') {
            $this->command?->error('Refusing to seed admin user with default password in production. Set a secure ADMIN_PASSWORD in .env.');

            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'email' => $email,
                'password' => Hash::make($password),
                'is_active' => true,
            ]
        );

        $this->command?->info("Admin user created/updated: {$email}");
    }
}
