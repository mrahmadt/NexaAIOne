<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicesLLMsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('llm_service')->insert([
            'service_id' => 3,
            'llm_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('llm_service')->insert([
            'service_id' => 3,
            'llm_id' => 2,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('llm_service')->insert([
            'service_id' => 3,
            'llm_id' => 3,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('llm_service')->insert([
            'service_id' => 3,
            'llm_id' => 4,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('llm_service')->insert([
            'service_id' => 1,
            'llm_id' => 5,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('llm_service')->insert([
            'service_id' => 2,
            'llm_id' => 5,
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
