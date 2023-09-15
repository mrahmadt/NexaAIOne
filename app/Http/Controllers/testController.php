<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\APIEndPoint;

use App\Features\HasOptions;
use App\Features\HasCaching;
use App\Features\HasMemory;

class testController extends Controller
{

    use HasCaching;
    use HasOptions;
    use HasMemory;

    protected $options = [];

    public function options(Request $request) : JsonResponse
    {

        $defaultOptions = [
            "systemMessage" => [
                "name" => "systemMessage",
                "type" => "text",
                "required" => false,
                "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
                'default' => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
                "isApiOption" => false,
            ],
            "updateSystemMessage" => [
                "name" => "updateSystemMessage",
                "type" => "text",
                "required" => false,
                "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                "isApiOption" => false,
                "default" => null,
            ],
            "userMessage" => [
                "name" => "userMessage",
                "type" => "string",
                "required" => true,
                "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                "isApiOption" => true,
                "default" => null,
            ],
            "messages" => [
                "name" => "messages",
                "type" => "json",
                "required" => false,
                "desc" => "A list of messages comprising the conversation so far. check https://platform.openai.com/docs/api-reference/chat/create#messages",
                "isApiOption" => false,
                "default" => null,
            ],
        ];

        $userOptions = [
            "messages" => [
                "name" => "messages",
                "type" => "text",
                "required" => false,
                "desc" => "Changed",
                'default' => "You are bad boy",
                "isApiOption" => false,
            ],
            "xccxxcvx" => [
                "name" => "xccxxcvx",
                "type" => "text",
                "required" => false,
                "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                "isApiOption" => false,
                "default" => null,
            ]
        ];
        dd(array_merge($defaultOptions, $userOptions));
        exit;
        $this->initOptions();
        dd($this->options);
        return response()->json($this->options);
    }

    public function memory(Request $request) : JsonResponse
    {
        return response()->json([]);
    }
    public function caching(Request $request) : JsonResponse
    {
        $output = [];

        $this->options['cachingPeriod'] = 5;
        $this->options['cacheScope'] = 'session';
        // $this->options['cacheScope'] = 'global';
        $this->options['session'] = 'userA';
        $this->options['message'] = 'My Message';
        // $this->setCache(['settings'=>['options'=>4]]);
        $output = $this->getCache('no');
        dd($output);
        return response()->json([$this->cacheKey,$output]);
    }
    private function getMaxTokensForModel(){
        return 4029;
    }
    private function errorMessage($message)
    {
        return response()->json(array_merge($this->responseMessageBase(status: 'error'), [
            'message' => $message,
        ]));
    }

    private function responseMessageBase(?string $status = 'unknown'){
        return [
            'status' => $status,
        ];
    }
}