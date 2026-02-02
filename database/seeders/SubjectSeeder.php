<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'code' => 'MATH',
                'name' => 'Mathematics',
                'type' => 'core',
            ],
            [
                'code' => 'ENG',
                'name' => 'English',
                'type' => 'core',
            ],
            [
                'code' => 'SCI',
                'name' => 'Science',
                'type' => 'core',
            ],
            [
                'code' => 'SST',
                'name' => 'Social Studies',
                'type' => 'core',
            ],
            [
                'code' => 'BIO',
                'name' => 'Biology',
                'type' => 'elective',
            ],
            [
                'code' => 'CHEM',
                'name' => 'Chemistry',
                'type' => 'elective',
            ],
        ];

        foreach ($data as $subject) {
            \App\Models\Subject::create($subject);
        }
    }
}
