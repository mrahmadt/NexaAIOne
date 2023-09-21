<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use Faker\Factory;

use Yethee\Tiktoken\EncoderProvider;
use Illuminate\Support\Str;

class testController extends Controller
{

    protected static $encoding_name = 'cl100k_base'; // Encodings specify how text is converted into tokens https://github.com/openai/openai-cookbook/blob/main/examples/How_to_count_tokens_with_tiktoken.ipynb

    public function test(Request $request) : JsonResponse
    {
        /*
        "08bf407066770a4771adf2ac1176c377" // app/Http/Controllers/testController.php:26
        "7359da254a27f6710962b07b88d6684f" // app/Http/Controllers/testController.php:26
        "223f6f45-f9f9-4cec-a1e6-33549ba46ceb" // app/Http/Controllers/testController.php:26
        "D4csJh4zNAXCfznM15VTtHo6ZsSoeKIp" // app/Http/Controllers/testController.php:26
        */
        
        $tokenSSL = bin2hex(openssl_random_pseudo_bytes(16)); // 32 characters
        $tokenRandom = bin2hex(random_bytes(16)); // 32 characters
        $tokenStrUUID = (string) Str::uuid(); // Using UUID
        $tokenStrRandom = Str::random(32); // 32 characters
        dd($tokenSSL, $tokenRandom, $tokenStrUUID, $tokenStrRandom);

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