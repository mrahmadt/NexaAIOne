<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiEndPointsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ai_end_points')->insert([
            'name' => 'Create Transcription',
            'description' => 'OpenAI Transcribes audio into the input language',
            'className' => 'OpenAITranscriptionService',
            'ApiReference'=>'https://platform.openai.com/docs/api-reference/audio/createTranscription',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('ai_end_points')->insert([
            'name' => 'Create Translation',
            'description' => 'OpenAI Translates audio into English',
            'className' => 'OpenAITranslationService',
            'ApiReference'=>'https://platform.openai.com/docs/api-reference/audio/createTranslation',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('ai_end_points')->insert([
            'name' => 'Create Chat Completion',
            'description' => 'OpenAI Creates a model response for the given chat conversation',
            'ApiReference' => 'https://platform.openai.com/docs/api-reference/chat/create',
            'className' => 'OpenAIChatCompletionService',
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
