<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LoadersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('embedders')->insert([
            'name' => 'unstructured',
            'description' => 'The unstructured library aims to simplify and streamline the preprocessing of structured and unstructured documents for downstream tasks',
            'className' => 'unstructured',
            'options' => json_encode(['chunking_strategy'=>true]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
