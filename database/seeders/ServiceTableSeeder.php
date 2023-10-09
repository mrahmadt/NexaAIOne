<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Kdabrow\SeederOnce\SeederOnce;
class ServiceTableSeeder extends SeederOnce
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            'id' => 1,
            'name' => 'Create Transcription',
            'description' => 'OpenAI Transcribes audio into the input language',
            'className' => 'OpenAITranscriptionService',
            'reference'=>'https://platform.openai.com/docs/api-reference/audio/createTranscription',
            'supportMemory' => false,
            'supportCaching' => true,
            'supportCollection' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('services')->insert([
            'id' => 2,
            'name' => 'Create Translation',
            'description' => 'OpenAI Translates audio into English',
            'className' => 'OpenAITranslationService',
            'reference'=>'https://platform.openai.com/docs/api-reference/audio/createTranslation',
            'supportMemory' => false,
            'supportCaching' => true,
            'supportCollection' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('services')->insert([
            'id' => 3,
            'name' => 'Create Chat Completion',
            'description' => 'OpenAI Creates a model response for the given chat conversation',
            'reference' => 'https://platform.openai.com/docs/api-reference/chat/create',
            'className' => 'OpenAIChatCompletionService',
            'supportMemory' => true,
            'supportCaching' => true,
            'supportCollection' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('services')->insert([
            'id' => 4,
            'name' => 'Create image',
            'description' => 'OpenAI Creates an image given a prompt.',
            'reference' => 'https://platform.openai.com/docs/api-reference/images/create',
            'className' => 'OpenAICreateImageService',
            'supportMemory' => false,
            'supportCaching' => true,
            'supportCollection' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('services')->insert([
            'id' => 5,
            'name' => 'Create image edit',
            'description' => 'Creates an edited or extended image given an original image and a prompt.',
            'reference' => 'https://platform.openai.com/docs/api-reference/images/createEdit',
            'className' => 'OpenAIEditImageService',
            'supportMemory' => false,
            'supportCaching' => true,
            'supportCollection' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        DB::table('services')->insert([
            'id' => 6,
            'name' => 'Create image variation',
            'description' => 'Creates a variation of a given image.',
            'reference' => 'https://platform.openai.com/docs/api-reference/images/createVariation',
            'className' => 'OpenAIVariationImageService',
            'supportMemory' => false,
            'supportCaching' => true,
            'supportCollection' => false,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
