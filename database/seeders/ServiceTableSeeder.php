<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
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

    }
}
