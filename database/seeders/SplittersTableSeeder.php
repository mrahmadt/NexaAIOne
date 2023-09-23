<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SplittersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    /*
    https://js.langchain.com/docs/modules/data_connection/document_transformers/text_splitters/contextual_chunk_headers
    https://python.langchain.com/docs/modules/data_connection/document_transformers/text_splitters/split_by_token
    
    */
    public function run(): void
    {
        DB::table('splitters')->insert([
            'name' => 'Recursively split by character',
            'description' => 'This text splitter is the recommended one for generic text. It is parameterized by a list of characters. It tries to split on them in order until the chunks are small enough.',
            'className' => 'RecursivelySplitByCharacter',
            'options' => json_encode([
                'separator' => "\n\n",
                'chunk_size' => 1000,
                'chunk_overlap'  => 200,    
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('splitters')->insert([
            'name' => 'Split by character',
            'description' => 'This is the simplest method. This splits based on characters (by default "\n\n") and measure chunk length by number of characters.',
            'className' => 'SplitByCharacter',
            'options' => json_encode([
                'separator' => "\n\n",
                'chunk_size' => 1000,
                'chunk_overlap'  => 200,    
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('splitters')->insert([
            'name' => 'Split by tokens',
            'description' => 'Language models have a token limit. You should not exceed the token limit. When you split your text into chunks it is therefore a good idea to count the number of tokens',
            'className' => 'SplitByTokens',
            'options' => json_encode([
                'separator' => "\n\n",
                'chunk_size' => 500,
                'chunk_overlap'  => 0,    
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
