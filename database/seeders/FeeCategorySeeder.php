<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FeeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            'Admission Fee',
            'Registration Fee',
            'Library Fee',
            'Transport Fee',
            'Other Fee',
            'PTP Fee',
            'Exam Fee',
        ];

        foreach ($data as $value) {
            \App\Models\FeeCategory::create([
                'name' => $value,
            ]);
        }
    }
}
