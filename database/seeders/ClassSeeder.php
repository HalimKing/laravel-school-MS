<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $data = [
            [
                'name' => 'Class 1',
                'description' => 'This is Class 1',
            ],
            [
                'name' => 'Class 2',
                'description' => 'This is Class 2',
            ],
            [
                'name' => 'Class 3',
                'description' => 'This is Class 3',
            ],
            [
                'name' => 'Class 4',
                'description' => 'This is Class 4',
            ],
            [
                'name' => 'Class 5',
                'description' => 'This is Class 5',
            ],
            [
                'name' => 'Class 6',
                'description' => 'This is Class 6',
            ],
            [
                'name' => 'Class 7',
                'description' => 'This is Class 7',
            ],
            [
                'name' => 'Class 8',
                'description' => 'This is Class 8',
            ],
            [
                'name' => 'Class 9',
                'description' => 'This is Class 9',
            ],
            [
                'name' => 'Class 10',
                'description' => 'This is Class 10',
            ],
        ];

        foreach ($data as $class) {
            \App\Models\ClassModel::create($class);
        }
    }
}
