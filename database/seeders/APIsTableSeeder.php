<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Service;

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
        $options['Messages']['systemMessage']['default'] = 'Translate the following from {{TranslateFrom}} to {{TranslateTo}} and provide some explination or examples.';
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
            'endpoint' => 'chatgpt',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($options),
            'created_at' => now(),
            'updated_at' => now()
        ]);

    }
}
