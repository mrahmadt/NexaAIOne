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
            'name' => 'HR Policies',
            'description' => 'Example of HR Policies.',
            'authToken' => bin2hex(openssl_random_pseudo_bytes(16)). bin2hex(random_bytes(5)),
            'context_prompt' => "Answer the following Question based on the Context only. Only answer from the Context. When you want to refer to the context provided, call it 'HR Policy' not just 'context'. Try to provide a reference to the HR Policy number. If you don't know the answer, say \"I don't know\"\nCONTEXT: {{context}}\n\nnQuestion:{{userMessage}}",
            'defaultTotalReturnDocuments' => 2,
            'loader_id' => 1,
            'splitter_id' => 1,
            'embedder_id' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
