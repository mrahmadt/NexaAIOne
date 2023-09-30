<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CollectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('collections')->insert([
            'name' => 'Recursively split by character',
            'description' => 'This text splitter is the recommended one for generic text. It is parameterized by a list of characters. It tries to split on them in order until the chunks are small enough.',
            'authToken' => bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(5)),
            'defaultTotalReturnDocuments' => 2,
            'loader_id' => 1,
            'splitter_id' => 1,
            'embedder_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
