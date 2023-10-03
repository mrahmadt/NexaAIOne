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
        
        $ChatGPT_options['Messages']['systemMessage']['isApiOption'] = true;
        $ChatGPT_options['Messages']['updateSystemMessage']['isApiOption'] = true;
        DB::table('apis')->insert([
            'name' => 'ChatGPT',
            'description' => 'Chat with OpenAI GPT',
            'endpoint' => 'chatgpt',
            'service_id' => 3,
            'collection_id' => null,
            'enableUsage' => true,
            'isActive' => true,
            'options' => json_encode($ChatGPT_options),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
