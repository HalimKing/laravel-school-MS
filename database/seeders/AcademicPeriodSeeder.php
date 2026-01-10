<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data =[
            'First Term',
            'Second Term',
            'Third Term',
        ];

        foreach ($data as $name) {
            \App\Models\AcademicPeriod::create([
                'name' => $name,
            ]);
        }
    }
}
