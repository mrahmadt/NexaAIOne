<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Faker\Factory;

use Yethee\Tiktoken\EncoderProvider;

class testController extends Controller
{

    protected static $encoding_name = 'cl100k_base'; // Encodings specify how text is converted into tokens https://github.com/openai/openai-cookbook/blob/main/examples/How_to_count_tokens_with_tiktoken.ipynb

    public function test(Request $request) : JsonResponse
    {
        $data = file_get_contents(base_path('tests/Feature/data/MSGSphere.txt'));
        $startTime = microtime(true);
        $tokenCount = $this->countTokens($data);
        $endTime = microtime(true);
    
        $totalSeconds = $endTime - $startTime;
    
        dd($tokenCount, $totalSeconds);
    
        return  response()->json('test');
    }

    public function countTokens($content){
        $provider = new EncoderProvider();
        $encoder = $provider->setVocabCache(storage_path('encoders'));
        $encoder = $provider->get(self::$encoding_name);
        $tokens = $encoder->encode($content);
        return count($tokens);
    }


}