<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use App\Services\OpenAIChatCompletionService;

class ApiOpenAIChatTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;
    private $api;
    private $service_id = 3;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function create_api($options = []){
        $OpenAIChatCompletionService = new OpenAIChatCompletionService(false, false);
        $optionSchema = $OpenAIChatCompletionService->getOptionSchema($this->service_id);
        $options = array_merge($optionSchema, $options);


        $attributes = [
            'name' => 'test',
            'description' => 'Test API for OpenAI Chatbot',
            'endpoint' => 'chat',
            'enableUsage' => 1,
            'options' => $options,
            'isActive' => 1,
            'service_id' => $this->service_id,
        ];
        $api = Api::create($attributes);
        $this->api = $api;
    }

    public function xtest_api_creation()
    {
        
        $attributes = [
            'name' => 'test',
            'description' => 'Test API for OpenAI Chatbot',
            'endpoint' => 'chat',
            'enableUsage' => 1,
            'options' => json_encode(['test' => 'test']),
            'isActive' => 1,
            'service_id' => $this->service_id,
        ];
        $api = Api::create($attributes);
        $this->assertDatabaseHas('apis', $attributes);
        $this->assertEquals($attributes['name'], $api->name);
        $this->assertEquals($attributes['description'], $api->description);
        $this->assertEquals($attributes['endpoint'], $api->endpoint);
        $this->assertEquals($attributes['enableUsage'], $api->enableUsage);
        $this->assertEquals($attributes['options'], $api->options);
        $this->assertEquals($attributes['isActive'], $api->isActive);
        $this->assertEquals($attributes['service_id'], $api->service_id);
    }

    public function xtest_call_openai_chat_api_no_memory_and_set_system_message()
    {

        //systemMessage is respected 
        //http status is 200
        //status is true
        //content is AI: gpt-3.5-turbo
        //role is assistant
        //No memory
        //second request will NOT respect the systemMessage (no memory)

        $this->create_api([
            "cachingPeriod" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "How long should a message response be cached, in minutes? If the same message is submitted again, the response will be retrieved from the cache if available. Set to 0 to disable caching.",
                "type" => "number",
                "default" => 1440,
                "required" => false,
                "isApiOption" => true,
                "name" => "cachingPeriod",
            ],
            "session" => [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Unique session id for this conversation.",
                "type" => "string",
                "default" => "global",
                "required" => false,
                "isApiOption" => true,
                "name" => "session",
            ],
            "model" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "The LLM model to use.",
                "type" => "multiselect",
                "default" => "gpt-4,gpt-4-32k,gpt-3.5-turbo,gpt-3.5-turbo-16k",
                "required" => false,
                "isApiOption" => true,
                "name" => "model",
                "options" => [
                  "gpt-4" => "GPT-4",
                  "gpt-4-32k" => "GPT-4 32k",
                  "gpt-3.5-turbo" => "GPT-3.5 Turbo",
                  "gpt-3.5-turbo-16k" => "GPT-3.5 Turbo 16k",
                ],
                "maxTokens" => [
                  "gpt-4" => 8192,
                  "gpt-4-32k" => 32768,
                  "gpt-3.5-turbo" => 4097,
                  "gpt-3.5-turbo-16k" => 16385,
                ]
                ],
                "enableMemory" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.",
                    "type" => "select",
                    "default" => true,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "enableMemory",
                    "options" => [
                      "disable" => "Disable",
                      "noOptimization" => "No memory optimization",
                      "truncate" => "Truncate",
                      "summary" => "Summary",
                      "embeddings" => "Embeddings",
                    ]
                ],
                "systemMessage" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
                    "type" => "text",
                    "default" => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "systemMessage",
                ],
                  "updateSystemMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                    "type" => "text",
                    "default" => null,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "updateSystemMessage",
                  ],
                  "userMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                    "type" => "string",
                    "default" => null,
                    "required" => true,
                    "isApiOption" => true,
                    "name" => "userMessage",
                  ],
        ]);
        // dd($this->api->options);
        $session = uniqid();
        $model = 'gpt-3.5-turbo';
        $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
            'cachingPeriod' => 0,
            'session' => $session,
            'model' => $model,
            'enableMemory' => 'disable',
            'systemMessage' => 'Always start every response with "AI:", this is a must!',
            'userMessage' => 'Response with your model name, your reply should be just "'. $model. '" or "Other"',
        ]);

        $response->assertJsonPath('status', true);
        $response->assertJsonFragment([
            'content' => 'AI: '. $model,
            'role' => 'assistant',
        ]);
        $response->assertStatus(200);
        // print_r($response->getContent());


        $response = $this->withHeaders([
            'X-GET-MESSAGES' => 1,
        ])->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
            'cachingPeriod' => 0,
            'session' => $session,
            'model' => $model,
            'enableMemory' => 'disable',
            'userMessage' => 'What was my last message?, your reply should with my last message OR YOU MUST say "I do not know".',
        ]);

        $response->assertSeeText("I do not know");
        $response->assertSeeText('You are a support agent');
        $response->assertJsonPath('status', true);

        $response->assertStatus(200);
        // print_r($response->getContent());


        /*
        [{"__messages":[{"content":"I do not know. Could you please repeat your last message?","role":"assistant"},{"content":"What was my last message?, your reply should with my last message OR just say \"I do not know\".","role":"user"},{"content":"You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.","role":"system"}],"message":{"content":"I do not know. Could you please repeat your last message?","role":"assistant"},"serviceResponse":{"choices":[{"finishReason":"stop","index":0,"message":{"content":"I do not know. Could you please repeat your last message?","functionCall":null,"role":"assistant"}}],"created":1694817203,"id":"chatcmpl-7zBfHyoFjyAUOaL5t7kHGiShGFEEi","model":"gpt-3.5-turbo-0613","object":"chat.completion","usage":{"completionTokens":13,"promptTokens":77,"totalTokens":90}},"status":true}].


        "{"status":true,"message":{"role":"assistant","content":"AI: gpt-3.5-turbo"},"serviceResponse":{"id":"chatcmpl-7zB2ayGg9DGt8SYui6rtJUHbLm6MO","object":"chat.completion","created":1694814804,"model":"gpt-3.5-turbo-0613","choices":[{"index":0,"message":{"role":"assistant","content":"AI: gpt-3.5-turbo","functionCall":null},"finishReason":"stop"}],"usage":{"promptTokens":46,"completionTokens":11,"totalTokens":57}}}" // tests/Feature/ApiOpenAIChatTest.php:181
        */
    }

    public function xtest_call_openai_chat_api_must_return_cached_response_scope_session_and_global()
    {
        $this->create_api([
            "cachingPeriod" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "How long should a message response be cached, in minutes? If the same message is submitted again, the response will be retrieved from the cache if available. Set to 0 to disable caching.",
                "type" => "number",
                "default" => 1440,
                "required" => false,
                "isApiOption" => true,
                "name" => "cachingPeriod",
            ],
            "cacheScope" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Is the caching scope set per session, or is it global (across all sessions)? set 'session' for individual session-based caching or 'global' for caching that spans across all sessions.",
                "type" => "select",
                "default" => "session",
                "required" => false,
                "isApiOption" => true,
                "name" => "cacheScope",
                "options" => [
                  "session" => "Per Session",
                  "global" => "Global",
                ]
            ],
            "session" => [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Unique session id for this conversation.",
                "type" => "string",
                "default" => "global",
                "required" => false,
                "isApiOption" => true,
                "name" => "session",
            ],
            "model" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "The LLM model to use.",
                "type" => "multiselect",
                "default" => "gpt-4,gpt-4-32k,gpt-3.5-turbo,gpt-3.5-turbo-16k",
                "required" => false,
                "isApiOption" => true,
                "name" => "model",
                "options" => [
                  "gpt-4" => "GPT-4",
                  "gpt-4-32k" => "GPT-4 32k",
                  "gpt-3.5-turbo" => "GPT-3.5 Turbo",
                  "gpt-3.5-turbo-16k" => "GPT-3.5 Turbo 16k",
                ],
                "maxTokens" => [
                  "gpt-4" => 8192,
                  "gpt-4-32k" => 32768,
                  "gpt-3.5-turbo" => 4097,
                  "gpt-3.5-turbo-16k" => 16385,
                ]
                ],
                "enableMemory" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.",
                    "type" => "select",
                    "default" => true,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "enableMemory",
                    "options" => [
                      "disable" => "Disable",
                      "noOptimization" => "No memory optimization",
                      "truncate" => "Truncate",
                      "summary" => "Summary",
                      "embeddings" => "Embeddings",
                    ]
                ],
                "systemMessage" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
                    "type" => "text",
                    "default" => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "systemMessage",
                ],
                  "updateSystemMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                    "type" => "text",
                    "default" => null,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "updateSystemMessage",
                  ],
                  "userMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                    "type" => "string",
                    "default" => null,
                    "required" => true,
                    "isApiOption" => true,
                    "name" => "userMessage",
                  ],
        ]);

        $session = uniqid();
        $model = 'gpt-3.5-turbo';
        $apiCall1 = [];
        $apiCall2 = [];
        foreach(['session','global'] as $cacheScope){
            $message = 'Give me a random number between 1 and 10';
            $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
                'cachingPeriod' => 5,
                'cacheScope' => $cacheScope,
                'session' => $session,
                'model' => $model,
                'enableMemory' => 'disable',
                'userMessage' => $message,
            ]);

            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $apiCall1[$cacheScope] = json_decode($response->getContent());

            $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
                'cachingPeriod' => 5,
                'cacheScope' => $cacheScope,
                'session' => $session,
                'model' => $model,
                'enableMemory' => 'disable',
                'userMessage' => $message,
            ]);

            $response->assertJsonPath('status', true);
            $response->assertStatus(200);

            $apiCall2[$cacheScope] = json_decode($response->getContent());
            $this->assertTrue($apiCall2[$cacheScope]->message->content == $apiCall1[$cacheScope]->message->content);
            $response->assertJsonPath('cached', true);
            $response->assertJsonPath('cacheScope', $cacheScope);


            $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
                'cachingPeriod' => 5,
                'clearCache' => true,
                'cacheScope' => $cacheScope,
                'session' => $session,
                'enableMemory' => 'disable',
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $response->assertSeeText('Cache cleared');
        }
    }


    public function xtest_call_openai_chat_api_using_memory_no_optimization()
    {
        $this->create_api([
            "cachingPeriod" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "How long should a message response be cached, in minutes? If the same message is submitted again, the response will be retrieved from the cache if available. Set to 0 to disable caching.",
                "type" => "number",
                "default" => 1440,
                "required" => false,
                "isApiOption" => true,
                "name" => "cachingPeriod",
            ],
            "cacheScope" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Is the caching scope set per session, or is it global (across all sessions)? set 'session' for individual session-based caching or 'global' for caching that spans across all sessions.",
                "type" => "select",
                "default" => "session",
                "required" => false,
                "isApiOption" => true,
                "name" => "cacheScope",
                "options" => [
                  "session" => "Per Session",
                  "global" => "Global",
                ]
            ],
            "session" => [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Unique session id for this conversation.",
                "type" => "string",
                "default" => "global",
                "required" => false,
                "isApiOption" => true,
                "name" => "session",
            ],
            "model" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "The LLM model to use.",
                "type" => "multiselect",
                "default" => "gpt-4,gpt-4-32k,gpt-3.5-turbo,gpt-3.5-turbo-16k",
                "required" => false,
                "isApiOption" => true,
                "name" => "model",
                "options" => [
                  "gpt-4" => "GPT-4",
                  "gpt-4-32k" => "GPT-4 32k",
                  "gpt-3.5-turbo" => "GPT-3.5 Turbo",
                  "gpt-3.5-turbo-16k" => "GPT-3.5 Turbo 16k",
                ],
                "maxTokens" => [
                  "gpt-4" => 8192,
                  "gpt-4-32k" => 32768,
                  "gpt-3.5-turbo" => 4097,
                  "gpt-3.5-turbo-16k" => 16385,
                ]
                ],
                "enableMemory" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.",
                    "type" => "select",
                    "default" => true,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "enableMemory",
                    "options" => [
                      "disable" => "Disable",
                      "noOptimization" => "No memory optimization",
                      "truncate" => "Truncate",
                      "summary" => "Summary",
                      "embeddings" => "Embeddings",
                    ]
                ],
                "systemMessage" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
                    "type" => "text",
                    "default" => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "systemMessage",
                ],
                  "updateSystemMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                    "type" => "text",
                    "default" => null,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "updateSystemMessage",
                  ],
                  "userMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                    "type" => "string",
                    "default" => null,
                    "required" => true,
                    "isApiOption" => true,
                    "name" => "userMessage",
                  ],
                  "memoryPeriod" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "How long, in minutes, should the conversation be retained in memory? If no new messages are received within this duration, the conversation history will be cleared",
                    "type" => "number",
                    "default" => 60,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "memoryPeriod",
                  ],
                  "memoryMaxTokenPercentage" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Defines the threshold, as a percentage of the LLM Model's max tokens, at which memory optimization will be triggered. When memory token usage reaches this specified percentage, optimization measures specified in the enableMemory variable will be enacted.",
                    "type" => "number",
                    "default" => 50,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "memoryMaxTokenPercentage",
                  ],
                  "clearMemory" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Clear memory for this session.",
                    "type" => "boolean",
                    "default" => false,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "clearMemory",
                  ]
                
        ]);
        $model = 'gpt-3.5-turbo';
        foreach(['Ahmad','Fahad'] as $myname){
            $session = uniqid();

            $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
                'cachingPeriod' => 0,
                'session' => $session,
                'model' => $model,
                'enableMemory' => 'noOptimization',
                'memoryPeriod' => 5,
                'userMessage' => 'My name is ' . $myname,
            ]);

            $response->assertJsonPath('status', true);
            $response->assertStatus(200);

            $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
                'cachingPeriod' => 0,
                'cacheScope' => 'session',
                'session' => $session,
                'model' => $model,
                'enableMemory' => 'noOptimization',
                'memoryPeriod' => 5,
                'userMessage' => "What is my name?",
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $response->assertSeeText($myname);


            $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
                'cachingPeriod' => 0,
                'session' => $session,
                'enableMemory' => 'noOptimization',
                'memoryPeriod' => 5,
                'clearMemory' => true,
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $response->assertSeeText('Memory cleared');

        }
    }

    public function test_call_openai_chat_api_using_memory_optimization_truncate()
    {

        $this->create_api([
            "cachingPeriod" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "How long should a message response be cached, in minutes? If the same message is submitted again, the response will be retrieved from the cache if available. Set to 0 to disable caching.",
                "type" => "number",
                "default" => 0,
                "required" => false,
                "isApiOption" => true,
                "name" => "cachingPeriod",
            ],
            "cacheScope" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Is the caching scope set per session, or is it global (across all sessions)? set 'session' for individual session-based caching or 'global' for caching that spans across all sessions.",
                "type" => "select",
                "default" => "session",
                "required" => false,
                "isApiOption" => true,
                "name" => "cacheScope",
                "options" => [
                  "session" => "Per Session",
                  "global" => "Global",
                ]
            ],
            "session" => [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "Unique session id for this conversation.",
                "type" => "string",
                "default" => "global",
                "required" => false,
                "isApiOption" => true,
                "name" => "session",
            ],
            "model" =>  [
                "_mandatory" => true,
                "_allowApiOption" => true,
                "_noDefaultValue" => false,
                "desc" => "The LLM model to use.",
                "type" => "multiselect",
                "default" => "gpt-4,gpt-4-32k,gpt-3.5-turbo,gpt-3.5-turbo-16k",
                "required" => false,
                "isApiOption" => true,
                "name" => "model",
                "options" => [
                  "gpt-4" => "GPT-4",
                  "gpt-4-32k" => "GPT-4 32k",
                  "gpt-3.5-turbo" => "GPT-3.5 Turbo",
                  "gpt-3.5-turbo-16k" => "GPT-3.5 Turbo 16k",
                ],
                "maxTokens" => [
                  "gpt-4" => 8192,
                  "gpt-4-32k" => 32768,
                  "gpt-3.5-turbo" => 4097,
                  "gpt-3.5-turbo-16k" => 16385,
                ]
                ],
                "enableMemory" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.",
                    "type" => "select",
                    "default" => true,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "enableMemory",
                    "options" => [
                      "disable" => "Disable",
                      "noOptimization" => "No memory optimization",
                      "truncate" => "Truncate",
                      "summary" => "Summary",
                      "embeddings" => "Embeddings",
                    ]
                ],
                "systemMessage" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
                    "type" => "text",
                    "default" => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "systemMessage",
                ],
                  "updateSystemMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
                    "type" => "text",
                    "default" => null,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "updateSystemMessage",
                  ],
                  "userMessage" => [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
                    "type" => "string",
                    "default" => null,
                    "required" => true,
                    "isApiOption" => true,
                    "name" => "userMessage",
                  ],
                  "memoryPeriod" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "How long, in minutes, should the conversation be retained in memory? If no new messages are received within this duration, the conversation history will be cleared",
                    "type" => "number",
                    "default" => 60,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "memoryPeriod",
                  ],
                  "memoryMaxTokenPercentage" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Defines the threshold, as a percentage of the LLM Model's max tokens, at which memory optimization will be triggered. When memory token usage reaches this specified percentage, optimization measures specified in the enableMemory variable will be enacted.",
                    "type" => "number",
                    "default" => 50,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "memoryMaxTokenPercentage",
                  ],
                  "clearMemory" =>  [
                    "_mandatory" => true,
                    "_allowApiOption" => true,
                    "_noDefaultValue" => false,
                    "desc" => "Clear memory for this session.",
                    "type" => "boolean",
                    "default" => false,
                    "required" => false,
                    "isApiOption" => true,
                    "name" => "clearMemory",
                  ]
                
        ]);

        $model = 'gpt-3.5-turbo';
        $session = uniqid();
        $memoryMaxTokenPercentage = 46;
        $memoryPeriod = 46;
        $enableMemory = 'truncate';

        $fullData = [];

        $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
            'llmDryRun' => true,
            'debug' => true,
            'session' => $session,
            'model' => $model,
            'enableMemory' => $enableMemory,
            'memoryMaxTokenPercentage' => $memoryMaxTokenPercentage,
            'memoryPeriod' => $memoryPeriod,
            'userMessage' => file_get_contents(base_path('tests/Feature/data/MSGSphere.txt')),
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        // $fullData[] = json_decode($response->getContent());


        $response = $this->post('/api/v1/call/'.$this->api->id.'/' . $this->api->name, [
            'llmDryRun' => true,
            'debug' => true,
            'session' => $session,
            'model' => $model,
            'enableMemory' => $enableMemory,
            'memoryMaxTokenPercentage' => $memoryMaxTokenPercentage,
            'memoryPeriod' => $memoryPeriod,
            'userMessage' => 'Remove old history',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);

        $fullData[] = json_decode($response->getContent());


        //small requests, don't remove
        //large requests, remove text

        //summary same thing

        dd($fullData);

    }
}