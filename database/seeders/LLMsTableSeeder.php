<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LLMsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('llms')->insert([
            [
                'name' => 'GPT-4',
                'description' => 'More capable than any GPT-3.5 model, able to do more complex tasks, and optimized for chat. Will be updated with our latest model iteration 2 weeks after it is released',
                'modelName' => 'gpt-4',
                'ownedBy' => 'OpenAI',
                'maxTokens' => 8192,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GPT-4 32k',
                'description' => 'Same capabilities as the standard gpt-4 mode but with 4x the context length. Will be updated with our latest model iteration.',
                'modelName' => 'gpt-4-32k',
                'ownedBy' => 'OpenAI',
                'maxTokens' => 32768,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GPT-3.5 Turbo',
                'description' => 'Most capable GPT-3.5 model and optimized for chat at 1/10th the cost of text-davinci-003. Will be updated with our latest model iteration 2 weeks after it is released.',
                'modelName' => 'gpt-3.5-turbo',
                'ownedBy' => 'OpenAI',
                'maxTokens' => 4097,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'GPT-3.5 Turbo 16k',
                'description' => 'Same capabilities as the standard gpt-3.5-turbo model but with 4 times the context.',
                'modelName' => 'gpt-3.5-turbo-16k',
                'ownedBy' => 'OpenAI',
                'maxTokens' => 16385,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Whisper',
                'description' => 'Whisper is a general-purpose speech recognition model. It is trained on a large dataset of diverse audio and is also a multi-task model that can perform multilingual speech recognition as well as speech translation and language identification.',
                'modelName' => 'whisper-1',
                'ownedBy' => 'OpenAI',
                'maxTokens' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
