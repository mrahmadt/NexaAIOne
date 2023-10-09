<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;
class EmbeddersTableSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('embedders')->insert([
            'name' => 'OpenAI Embeddings',
            'description' => 'Get a vector representation of a given input using OpenAI Embeddings',
            'className' => 'OpenAIEmbedding',
            'options' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
