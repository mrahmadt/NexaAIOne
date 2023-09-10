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
            'requestSchema' => json_encode([
                [
                    "name" => "file",
                    "type" => "file",
                    "required" => true,
                    "desc" => "The audio file object (not file name) to transcribe, in one of these formats: flac, mp3, mp4, mpeg, mpga, m4a, ogg, wav, or webm.",
                ],
                [
                    "name" => "prompt",
                    "type" => "string",
                    "required" => false,
                    "desc" => "An optional text to guide the model's style or continue a previous audio segment. The prompt should match the audio language.",
                ],
                [
                    "name" => "response_format",
                    "type" => "string",
                    "required" => false,
                    "desc" => "The format of the transcript output, in one of these options: json, text, srt, verbose_json, or vtt.",
                    "default" => "text",
                ],
                [
                    "name" => "temperature",
                    "type" => "number",
                    "required" => false,
                    "desc" => "The sampling temperature, between 0 and 1. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. If set to 0, the model will use log probability to automatically increase the temperature until certain thresholds are hit.",
                    "default" => 0,
                ],
                [
                    "name" => "language",
                    "type" => "string",
                    "required" => false,
                    "desc" => "The language of the input audio. Supplying the input language in ISO-639-1 format will improve accuracy and latency.",
                ]
            ]),
            'supportCaching' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);


        DB::table('ai_end_points')->insert([
            'name' => 'Create Translation',
            'description' => 'OpenAI Translates audio into English',
            'className' => 'OpenAITranslationService',
            'ApiReference'=>'https://platform.openai.com/docs/api-reference/audio/createTranslation',
            'requestSchema' => json_encode([
                [
                    "name" => "file",
                    "type" => "file",
                    "required" => true,
                    "desc" => "The audio file object (not file name) to translate, in one of these formats: flac, mp3, mp4, mpeg, mpga, m4a, ogg, wav, or webm.",
                ],
                [
                    "name" => "prompt",
                    "type" => "string",
                    "required" => false,
                    "desc" => "An optional text to guide the model's style or continue a previous audio segment. The prompt should be in English.",
                ],
                [
                    "name" => "response_format",
                    "type" => "string",
                    "required" => false,
                    "desc" => "The format of the transcript output, in one of these options: json, text, srt, verbose_json, or vtt.",
                    "default" => "text",
                ],
                [
                    "name" => "temperature",
                    "type" => "number",
                    "required" => false,
                    "desc" => "The sampling temperature, between 0 and 1. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic. If set to 0, the model will use log probability to automatically increase the temperature until certain thresholds are hit.",
                    "default" => 0,
                    "inputName" => "temperature"
                ]
            ]),
            'supportCaching' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('ai_end_points')->insert([
            'name' => 'Create chat completion',
            'description' => 'OpenAI Creates a model response for the given chat conversation',
            'ApiReference' => 'https://platform.openai.com/docs/api-reference/chat/create',
            'className' => 'OpenAIChatCompletionService',
            'requestSchema' => json_encode([

                [
                    "name" => "session",
                    "type" => "string",
                    "required" => false,
                    "desc" => "Unique session id for this conversation.",
                    "default" => "global",
                ],
                [
                    "name" => "system_message",
                    "type" => "string",
                    "required" => false,
                    "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'update_system_message' parameter.",
                ],
                [
                    "name" => "preSystemMessage",
                    "type" => "string",
                    "required" => false,
                    "desc" => "An optional instruction or context provided prior to the main system message (will be used only if system_message provided as API option), setting the foundational tone or guidelines for the ensuing conversation",
                    "if-api-option" => "system_message",
                ],
                [
                    "name" => "postParamSystemMessage",
                    "type" => "string",
                    "required" => false,
                    "desc" => "An optional instruction or context appended after the main system message (will be used only if system_message provided as API option), used to fine-tune or further direct the model's responses in the conversation.",
                    "if-api-option" => "system_message",
                ],
                [
                    "name" => "update_system_message",
                    "type" => "string",
                    "required" => false,
                    "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                ],

                [
                    "name" => "user_message",
                    "type" => "string",
                    "required" => true,
                    "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                ],
                ["name" => "max_tokens", "type" => "integer", "required" => false, "desc" => "The maximum number of tokens to generate in the chat completion."],
                [
                    "name" => "stream",
                    "type" => "boolean",
                    "required" => false,
                    "desc" => "If set, partial message deltas will be sent, like in ChatGPT. Tokens will be sent as data-only server-sent events as they become available, with the stream terminated by a data: [DONE] message.",
                    "default" => false,
                ],
                [
                    "name" => "debug",
                    "type" => "boolean",
                    "required" => false,
                    "desc" => "Retains all request and response data, facilitating issue troubleshooting and prompt optimization",
                    "default" => false,
                ],
                [
                    "name" => "model",
                    "type" => "string",
                    "required" => false,
                    "desc" => "The LLM model to use.",
                ],
                [
                    "name" => "messages",
                    "type" => "json",
                    "required" => false,
                    "desc" => "A list of messages comprising the conversation so far. check https://platform.openai.com/docs/api-reference/chat/create#messages",
                ],
                ["name" => "temperature", "type" => "number", "required" => false, "desc" => "What sampling temperature to use, between 0 and 2. Higher values like 0.8 will make the output more random, while lower values like 0.2 will make it more focused and deterministic.

We generally recommend altering this or top_p but not both.", "default" => 1],

                ["name" => "top_p", "type" => "number", "required" => false, "desc" => "An alternative to sampling with temperature, called nucleus sampling, where the model considers the results of the tokens with top_p probability mass. So 0.1 means only the tokens comprising the top 10% probability mass are considered.

                We generally recommend altering this or temperature but not both.", "default" => 1],

                ["name" => "chat_choices", "type" => "number", "required" => false, "desc" => "How many chat completion choices to generate for each input message.", "default" => 1, 'apiName'=>'n'],

                ["name" => "stop_sequences", "type" => "string / array / null", "required" => false, "desc" => "Up to 4 sequences where the API will stop generating further tokens.", "default" => null, 'apiName'=>'stop'],

                ["name" => "presence_penalty", "type" => "number", "required" => false, "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on whether they appear in the text so far, increasing the model's likelihood to talk about new topics.", "default" => 0],

                ["name" => "frequency_penalty", "type" => "number", "required" => false, "desc" => "Number between -2.0 and 2.0. Positive values penalize new tokens based on their existing frequency in the text so far, decreasing the model's likelihood to repeat the same line verbatim.", "default" => 0],

                ["name" => "logit_bias", "type" => "json", "required" => false, "desc" => "Modify the likelihood of specified tokens appearing in the completion.

                Accepts a json object that maps tokens (specified by their token ID in the tokenizer) to an associated bias value from -100 to 100. Mathematically, the bias is added to the logits generated by the model prior to sampling. The exact effect will vary per model, but values between -1 and 1 should decrease or increase likelihood of selection; values like -100 or 100 should result in a ban or exclusive selection of the relevant token.", "default" => null],

                ["name" => "user", "type" => "string", "required" => false, "desc" => "A unique identifier representing your end-user, which can help OpenAI to monitor and detect abuse.", "default" => null]
            ]),
            'supportHistory' => true,
            'supportCaching' => true,
            'created_at' => now(),
            'updated_at' => now()

        ]);
    }
}
