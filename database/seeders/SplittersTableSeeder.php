<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;
class SplittersTableSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        DB::table('splitters')->insert([
            'name' => 'Recursively split by character',
            'description' => 'This text splitter is the recommended one for generic text. It is parameterized by a list of characters. It tries to split on them in order until the chunks are small enough.',
            'className' => 'RecursiveCharacterTextSplitter',
            'options' => json_encode([
                'separators' => ["\n\n", "\n", " ", ""],
                'chunk_size' => 4000,
                'chunk_overlap'  => 200,
                'keep_separator'  => 0,
                'strip_whitespace'  => 1,
                'is_separator_regex'  => 0,
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('splitters')->insert([
            'name' => 'Split by character',
            'description' => 'This is the simplest method. This splits based on characters (by default "\n\n") and measure chunk length by number of characters.',
            'className' => 'CharacterTextSplitter',
            'options' => json_encode([
                'separator' => "\n\n",
                'chunk_size' => 4000,
                'chunk_overlap'  => 200,
                'keep_separator'  => 0,
                'strip_whitespace'  => 1,
                'is_separator_regex'  => 0,
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('splitters')->insert([
            'name' => 'Split by tokens',
            'description' => 'Language models have a token limit. You should not exceed the token limit. When you split your text into chunks it is therefore a good idea to count the number of tokens',
            'className' => 'TokenTextSplitter',
            'options' => json_encode([
                'encoding_name' => "p50k_base",
                'chunk_size' => 500,
                'chunk_overlap'  => 60,
                'strip_whitespace'  => 1,
            ]),
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
