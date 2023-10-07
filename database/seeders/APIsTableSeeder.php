<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use Illuminate\Support\Str;
 
class APIsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $ChatGPT_serviceModel = Service::where(['id'=>3, 'isActive'=>true])->first();
        $className = '\App\Services\\' . $ChatGPT_serviceModel->className;
        $ChatGPT_service = new $className();
        $ChatGPT_options = $ChatGPT_service->getOptionSchema($ChatGPT_serviceModel);
        

        $options = $ChatGPT_options;
        $options['Messages']['systemMessage']['isApiOption'] = true;
        $options['Messages']['updateSystemMessage']['isApiOption'] = true;
        $options['Debugging']['debug']['isApiOption'] = false;
        DB::table('apis')->insert([
            'name' => 'ChatGPT',
            'description' => 'Chat with OpenAI GPT',
            'endpoint' => 'chatgpt',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);


        $options = $ChatGPT_options;
        $options['OpenAI']['model']['default'] = "gpt-3.5-turbo";
        $options['OpenAI']['model']['isApiOption'] = false;
        $options['Caching']['cachingPeriod']['default'] = 60;
        $options['Caching']['cacheScope']['default'] = 'global';
        $options['Caching']['clearCache']['isApiOption'] = false;
        $options['Messages']['systemMessage']['isApiOption'] = false;
        $options['Messages']['updateSystemMessage']['isApiOption'] = false;
        $options['Messages']['messages']['isApiOption'] = false;
        $options['Debugging']['debug']['isApiOption'] = false;
        $options['Memory']['enableMemory']['default'] = 'disabled';
        $options['Memory']['enableMemory']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['default'] = 'noOptimization';
        $options['Memory']['clearMemory']['isApiOption'] = false;
        $options['General']['session']['default'] = 'global';
        $options['General']['session']['isApiOption'] = false;
        $options['Messages']['systemMessage']['default'] = 'Translate the following from {{TranslateFrom}} to {{TranslateTo}} and provide explanations and examples.';
        foreach(['TranslateFrom','TranslateTo'] as $key){
            $options['Custom'][$key]['name'] = $key;
            $options['Custom'][$key]['type'] = 'text';
            $options['Custom'][$key]['required'] = true;
            $options['Custom'][$key]['isApiOption'] = true;
            $options['Custom'][$key]['_group'] = 'Custom';

            if($key == 'TranslateFrom'){
                $options['Custom'][$key]['desc'] = 'Translate from';
                $options['Custom'][$key]['default'] = 'English';
            }else{
                $options['Custom'][$key]['desc'] = 'Translate to';
                $options['Custom'][$key]['default'] = 'Spanish';
            }
        }
        DB::table('apis')->insert([
            'name' => 'TranslateGPT',
            'description' => 'Translate with OpenAI GPT',
            'endpoint' => 'translateGPT',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);



        $options = $ChatGPT_options;
        $options['OpenAI']['model']['default'] = "gpt-3.5-turbo";
        $options['OpenAI']['model']['isApiOption'] = false;
        $options['Caching']['cachingPeriod']['default'] = 60;
        $options['Caching']['cacheScope']['default'] = 'session';
        $options['Caching']['clearCache']['isApiOption'] = false;
        $options['Messages']['systemMessage']['isApiOption'] = false;
        $options['Messages']['updateSystemMessage']['isApiOption'] = false;
        $options['Messages']['messages']['isApiOption'] = false;
        $options['Debugging']['debug']['isApiOption'] = false;
        $options['Memory']['enableMemory']['default'] = 'shortMemory';
        $options['Memory']['enableMemory']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['default'] = 'truncate';
        $options['Memory']['clearMemory']['isApiOption'] = false;
        $options['General']['session']['default'] = 'session';
        $options['General']['session']['isApiOption'] = true;
        $options['Messages']['systemMessage']['default'] = "You are a helpful support agent for company Mint Telco. Always rely on the context within the user’s message to determine your response. If the answer is not apparent from the context, or if the user expresses dissatisfaction with your answer or explicitly asks to connect to an agent or open a ticket, then you should proceed to open a ticket. When opening a ticket, your response should adhere strictly to the following JSON schema and nothing else:

            {  \"ticket\": true,  \"question\": \"[Insert user's question here]\",  \"category\": \"[Classify the user's question and insert classification here]\"}
            
            CONTEXT: {{context}}
            ";
        foreach(['context'] as $key){
            $options['Custom'][$key]['name'] = $key;
            $options['Custom'][$key]['type'] = 'text';
            $options['Custom'][$key]['required'] = true;
            $options['Custom'][$key]['isApiOption'] = true;
            $options['Custom'][$key]['_group'] = 'Custom';
            $options['Custom'][$key]['desc'] = 'context to be used for answering support questions';
            $options['Custom'][$key]['default'] = null;
        }
        DB::table('apis')->insert([
            'name' => 'Support Agent',
            'description' => 'Support Agent can return open ticket action.',
            'endpoint' => 'supportAgent',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);



        $options = $ChatGPT_options;
        $options['OpenAI']['model']['default'] = "gpt-3.5-turbo";
        $options['OpenAI']['model']['isApiOption'] = false;
        $options['Caching']['cachingPeriod']['default'] = 60;
        $options['Caching']['cacheScope']['default'] = 'session';
        $options['Caching']['clearCache']['isApiOption'] = false;
        $options['Messages']['systemMessage']['isApiOption'] = false;
        $options['Messages']['updateSystemMessage']['isApiOption'] = false;
        $options['Messages']['messages']['isApiOption'] = false;
        $options['Debugging']['debug']['isApiOption'] = false;
        $options['Memory']['enableMemory']['default'] = 'disabled';
        $options['Memory']['enableMemory']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['default'] = 'noOptimization';
        $options['Memory']['clearMemory']['isApiOption'] = false;
        $options['General']['session']['default'] = 'session';
        $options['General']['session']['isApiOption'] = true;


        $options['Messages']['systemMessage']['default'] = "Based on the provided user message, please classify it into the most appropriate category.\n\nLIST OF CATEGORIES: {{categories}}}\n\nPlease return the response in this JSON format: {\"question\":\"[user's message]\", \"category\":\"[chosen category]\"}. If the user's message doesn't align with any of the specific categories, classify it as 'other'.";
        
        foreach(['categories'] as $key){
            $options['Custom'][$key]['name'] = $key;
            $options['Custom'][$key]['type'] = 'text';
            $options['Custom'][$key]['required'] = true;
            $options['Custom'][$key]['isApiOption'] = true;
            $options['Custom'][$key]['_group'] = 'Custom';
            $options['Custom'][$key]['desc'] = 'List of categories delimited by comma to be used for classifying user messages';
            $options['Custom'][$key]['default'] = null;
        }
        DB::table('apis')->insert([
            'name' => 'Classify user message',
            'description' => 'Classify user message into the most appropriate category.',
            'endpoint' => 'ClassifyMessage',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);


        $options = $ChatGPT_options;
        $options['OpenAI']['model']['default'] = "gpt-3.5-turbo";
        $options['OpenAI']['model']['isApiOption'] = false;
        $options['Caching']['cachingPeriod']['default'] = 60;
        $options['Caching']['cacheScope']['default'] = 'session';
        $options['Caching']['clearCache']['isApiOption'] = false;
        $options['Messages']['systemMessage']['isApiOption'] = false;
        $options['Messages']['updateSystemMessage']['isApiOption'] = false;
        $options['Messages']['messages']['isApiOption'] = false;
        $options['Debugging']['debug']['isApiOption'] = false;
        $options['Memory']['enableMemory']['default'] = 'disabled';
        $options['Memory']['enableMemory']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['default'] = 'noOptimization';
        $options['Memory']['clearMemory']['isApiOption'] = false;
        $options['General']['session']['default'] = 'session';
        $options['General']['session']['isApiOption'] = true;
        $options['Messages']['systemMessage']['default'] = "Given the following text, produce a concise summary that captures the main points.";
        DB::table('apis')->insert([
            'name' => 'Summarize Text',
            'description' => 'Summarize text into the most important points.',
            'endpoint' => 'SummarizeText',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);



        $options = $ChatGPT_options;
        $options['OpenAI']['model']['default'] = "gpt-3.5-turbo";
        $options['OpenAI']['model']['isApiOption'] = false;
        $options['Caching']['cachingPeriod']['default'] = 60;
        $options['Caching']['cacheScope']['default'] = 'session';
        $options['Caching']['clearCache']['isApiOption'] = false;
        $options['Messages']['systemMessage']['isApiOption'] = false;
        $options['Messages']['updateSystemMessage']['isApiOption'] = false;
        $options['Messages']['messages']['isApiOption'] = false;
        $options['Debugging']['debug']['isApiOption'] = false;
        $options['Memory']['enableMemory']['default'] = 'disabled';
        $options['Memory']['enableMemory']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['default'] = 'noOptimization';
        $options['Memory']['clearMemory']['isApiOption'] = false;
        $options['General']['session']['default'] = 'session';
        $options['General']['session']['isApiOption'] = true;
        $options['Messages']['systemMessage']['default'] = "Given the user's message, analyze its sentiment and categorize it as either \"positive\", \"neutral\", or \"negative\". Return the analysis in the following JSON format.\nJSON Schema: { \"userMessage\": \"string (original user message)\", \"sentiment\": \"string (either 'positive', 'neutral', or 'negative')\" }\n\nPlease provide the sentiment analysis for the below message in accordance with the specified JSON schema.";
        DB::table('apis')->insert([
            'name' => 'Sentiment analysis',
            'description' => 'Analyze the sentiment of a user message.',
            'endpoint' => 'SentimentAnalysis',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);



        $options = $ChatGPT_options;
        $options['OpenAI']['model']['default'] = "gpt-3.5-turbo";
        $options['OpenAI']['model']['isApiOption'] = false;
        $options['Caching']['cachingPeriod']['default'] = 60;
        $options['Caching']['cacheScope']['default'] = 'session';
        $options['Caching']['clearCache']['isApiOption'] = false;
        $options['Messages']['systemMessage']['isApiOption'] = false;
        $options['Messages']['updateSystemMessage']['isApiOption'] = false;
        $options['Messages']['messages']['isApiOption'] = false;
        $options['Debugging']['debug']['isApiOption'] = false;
        $options['Memory']['enableMemory']['default'] = 'shortMemory';
        $options['Memory']['enableMemory']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['isApiOption'] = false;
        $options['Memory']['memoryOptimization']['default'] = 'truncate';
        $options['Memory']['clearMemory']['isApiOption'] = false;
        $options['General']['session']['default'] = 'session';
        $options['General']['session']['isApiOption'] = true;
        $options['Messages']['systemMessage']['default'] = "You are HR support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.";
        DB::table('apis')->insert([
            'name' => 'HR Support Agent',
            'description' => 'HR Support Agent answer based on HR Policies.',
            'endpoint' => 'HRSupportAgent',
            'service_id' => 3,
            'collection_id' => 1,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);


        //OpenAI Audio API
        foreach([1,2] as $id){
            $serviceModel = Service::where(['id'=>$id, 'isActive'=>true])->first();
            $className = '\App\Services\\' . $serviceModel->className;
            $service = new $className();
            $options = $service->getOptionSchema($serviceModel);
            $options['OpenAI']['model']['default'] = "whisper-1";
            $options['OpenAI']['model']['isApiOption'] = false;
            $options['Caching']['cachingPeriod']['default'] = 60;
            $options['Caching']['cacheScope']['default'] = 'session';
            $options['Caching']['clearCache']['isApiOption'] = true;
            $options['Debugging']['debug']['isApiOption'] = false;
            $options['General']['session']['default'] = 'session';
            $options['General']['session']['isApiOption'] = true;
            DB::table('apis')->insert([
                'name' => $serviceModel->name,
                'description' => $serviceModel->description,
                'endpoint' => Str::slug($serviceModel->name),
                'service_id' => $serviceModel->id,
                'collection_id' => null,
                'enableUsage' => true,
                'isActive' => true,
                'options' => json_encode($options),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        
        //OpenAI Image API
        foreach([4,5,6] as $id){
            $serviceModel = Service::where(['id'=>$id, 'isActive'=>true])->first();
            $className = '\App\Services\\' . $serviceModel->className;
            $service = new $className();
            $options = $service->getOptionSchema($serviceModel);
            $options['Caching']['cachingPeriod']['default'] = 60;
            $options['Caching']['cacheScope']['default'] = 'session';
            $options['Caching']['clearCache']['isApiOption'] = true;
            $options['Debugging']['debug']['isApiOption'] = false;
            $options['General']['session']['default'] = 'session';
            $options['General']['session']['isApiOption'] = true;
            DB::table('apis')->insert([
                'name' => $serviceModel->name,
                'description' => $serviceModel->description,
                'endpoint' => Str::slug($serviceModel->name),
                'service_id' => $serviceModel->id,
                'collection_id' => null,
                'enableUsage' => true,
                'isActive' => true,
                'options' => json_encode($options),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
