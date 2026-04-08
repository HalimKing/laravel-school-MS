<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the permission seeder first
        $this->call(PermissionSeeder::class);

        // Create test user and assign super-admin role
        $user = User::firstOrCreate(
            ['email' => 'admin@schoolms.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Assign super-admin role
        if (!$user->hasRole('super-admin')) {
            $user->assignRole('super-admin');
        }

        // Run academic year and period seeders
        $this->call(AcademicYearSeeder::class);
        $this->call(AcademicPeriodSeeder::class);

        // Run class seeder
        $this->call(ClassSeeder::class);
    }
}
