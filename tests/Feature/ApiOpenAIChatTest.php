<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use App\Models\App;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiOpenAIChatTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;
    protected $api;
    protected $service_id = 3;
    protected $serviceModel;
    protected $serviceClass;
    protected $myApp;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function create_app_api($options_values = [], $options_apiOptions = [], $options_notApiOptions = []){
      $this->serviceModel = Service::find($this->service_id);
      $className = 'App\Services\\' . $this->serviceModel->className;
      $this->serviceClass = new $className(false);

      $optionSchema = $this->serviceClass->getOptionSchema($this->serviceModel);
      // Loop through the schema to make modifications
      foreach ($optionSchema as $group => $options) {
        foreach ($options as $key => $option) {
            // Change the 'value' if it's specified in $options_values
            if (array_key_exists($key, $options_values)) {
                $optionSchema[$group][$key]['default'] = $options_values[$key];
            }
            // Change 'isApiOption' to true if it's specified in $options_apiOptions
            if (in_array($key, $options_apiOptions)) {
                $optionSchema[$group][$key]['isApiOption'] = true;
            }
            // Change 'isApiOption' to false if it's specified in $options_notApiOptions
            if (in_array($key, $options_notApiOptions)) {
                $optionSchema[$group][$key]['isApiOption'] = false;
            }
        }
      }
      $attributes = [
          'name' => 'test',
          'description' => 'Test API',
          'endpoint' => 'testAPI',
          'enableUsage' => 1,
          'options' => $optionSchema,
          'isActive' => 1,
          'service_id' => $this->service_id,
      ];
      $this->api = Api::create($attributes);
      $this->myApp = App::factory()->create();
      $this->myApp->apis()->attach($this->api->id);
    }

    // app and api creation
    public function x_test_app_api_creation()
    {
        $this->create_app_api();
        $this->assertEquals($this->api->name, 'test');
    }

    // allow only api options
    public function x_test_allow_only_api_options()
    {
        $this->create_app_api([], ['cachingPeriod', 'session', 'model', 'enableMemory', 'systemMessage', 'updateSystemMessage', 'userMessage'], ['cacheScope']);
        $this->assertEquals($this->api->name, 'test');
        $this->assertEquals($this->api->options['cachingPeriod']['isApiOption'], true);
        $this->assertEquals($this->api->options['cacheScope']['isApiOption'], false);
    }
    // no auth
    public function x_test_no_auth()
    {
        $this->create_app_api();
        $response = $this->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'userMessage' => 'Response with your model name',
        ]);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'No Token']);
    }

    // wrong auth
    public function x_test_wrong_auth()
    {
        $this->create_app_api();
        $response = $this->withHeaders([
          'Authorization' => 'Bearer authToken',
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'userMessage' => 'Response with your model name',
        ]);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Invalid token']);
    }

    // api not mapped to app
    public function x_test_api_not_mapped_to_app()
    {
      $this->create_app_api();

        $attributes = [
            'name' => 'test 2',
            'description' => 'Test API',
            'endpoint' => 'testAPI2',
            'enableUsage' => 1,
            'options' => [],
            'isActive' => 1,
            'service_id' => $this->service_id,
        ];
        $wrong_api = Api::create($attributes);
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$wrong_api->id.'/' . $wrong_api->name, [
            'userMessage' => 'Response with your model name',
        ]);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'Access Denied']);
    }

    // userMessage
    // systemMessage with variable
    // enableMemory = disabled
    // fake LLM
    public function x_test_no_memory_set_system_custom_message()
    {
        $this->create_app_api([],['returnMemory','cachingPeriod','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
        // dd($this->api->options);
        $session = uniqid();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'returnMemory' => true,
            'session' => $session,
            'enableMemory' => 'disabled',
            'customSystemVar' => 'Custom12',
            'systemMessage' => 'My System Messages {{customSystemVar}}',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'My userMessage response',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response');
        $response->assertJsonPath('memory.0.role', 'system');
        $response->assertJsonPath('memory.0.content', 'My System Messages Custom12');
        $response->assertJsonPath('memory.1.content', 'My userMessage response');


        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'fakeLLM' => true,
          'returnMemory' => true,
          'session' => $session,
          'enableMemory' => 'disabled',
          'fakeLLMOutput' => 'My Fake response',
          'userMessage' => 'My userMessage response',
        ]);
        $response->assertJsonPath('status', true);
        $this->assertTrue(count($response['memory']) == 3);
    }


    // enable usage
    public function x_test_usage()
    {
        $this->create_app_api([],['returnMemory','cachingPeriod','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'returnMemory' => true,
            'enableMemory' => 'disabled',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'My userMessage response',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'fakeLLM' => true,
          'returnMemory' => true,
          'enableMemory' => 'disabled',
          'fakeLLMOutput' => 'My Fake response',
          'userMessage' => 'My userMessage response',
        ]);
        $this->assertDatabaseHas('usages', [
          'app_id' => $this->myApp->id,
          'api_id' => $this->api->id,
          'hits' => 2,
        ]);
    }

    public function x_test_must_return_cached_response_scope_session_and_global_and_clear_cache()
    {
        $this->create_app_api([],['returnMemory','cachingPeriod', 'cacheScope','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
        $session = uniqid();
        $apiCall1 = [];
        $apiCall2 = [];
        foreach(['session','global'] as $cacheScope){
                $randString = uniqid();
                $response = $this->withHeaders([
                  'Authorization' => 'Bearer ' . $this->myApp->authToken,
                ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
                  'fakeLLM' => true,
                  'enableMemory' => 'disabled',
                  'fakeLLMOutput' => $randString,
                  'cachingPeriod' => 5,
                  'cacheScope' => $cacheScope,
                  'session' => $session,
                  'userMessage' => 'Give me a random string',
                ]);
                $response->assertStatus(200);
                $response->assertJsonPath('status', true);
                
                $apiCall1[$cacheScope] = json_decode($response->getContent());

                $response = $this->withHeaders([
                    'Authorization' => 'Bearer ' . $this->myApp->authToken,
                  ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
                    'fakeLLM' => true,
                    'enableMemory' => 'disabled',
                    'fakeLLMOutput' => 'My Fake response 1122',
                    'cachingPeriod' => 5,
                    'cacheScope' => $cacheScope,
                    'session' => $session,
                    'userMessage' => 'Give me a random string',
                ]);
                $response->assertStatus(200);
                $response->assertJsonPath('status', true);

                $apiCall2[$cacheScope] = json_decode($response->getContent());
                $this->assertTrue($apiCall2[$cacheScope]->message->content == $apiCall1[$cacheScope]->message->content);

                $response->assertJsonPath('cached', true);
                $response->assertJsonPath('cacheScope', $cacheScope);

                $response = $this->withHeaders([
                  'Authorization' => 'Bearer ' . $this->myApp->authToken,
                ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
                  'cachingPeriod' => 5,
                  'clearCache' => true,
                  'cacheScope' => $cacheScope,
                  'session' => $session,
                  'enableMemory' => 'disabled',
                ]);
                $response->assertJsonPath('status', true);
                $response->assertStatus(200);
                $response->assertSeeText('Cache cleared');
        }
    }

    // send messages
    public function x_test_send_messages()
    {
        $this->create_app_api([],['returnMemory','messages','cachingPeriod','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'cachingPeriod' => 0,
            'fakeLLM' => true,
            'returnMemory' => true,
            'enableMemory' => 'disabled',
            'fakeLLMOutput' => 'My Fake response',
            'messages' => json_encode([
                [
                    'role' => 'system',
                    'content' => 'My System Messages',
                ],
                [
                    'role' => 'user',
                    'content' => 'My userMessage response',
                ],
            ]),
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response');
        $response->assertJsonPath('memory.0.role', 'system');
        $response->assertJsonPath('memory.0.content', 'My System Messages');
        $response->assertJsonPath('memory.1.role', 'user');
        $response->assertJsonPath('memory.1.content', 'My userMessage response');
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');
    }
    
    public function x_test_return_debug()
    {
        $this->create_app_api([],['returnMemory','messages','debug','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'debug' => true,
            'enableMemory' => 'disabled',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'ABC 123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $this->assertTrue(count($response['debug'] ?? []) > 0);
    }

    // check none api options
    public function x_test_none_api_option()
    {
        $this->create_app_api();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'debug' => true,
            'enableMemory' => 'disabled',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'ABC 123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $this->assertTrue(count($response['debug'] ?? []) == 0);
    }

    
    // optimizeMemoryEmbeddings
    // enableMemory short memory Short memory will be saved in Memory
    // enableMemory short memory period then clear
    // clearMemory short memory (memory)
    // memory session
    // memoryMaxTokenPercentage

    //xtest_call_openai_chat_api_using_memory_no_optimization
    //xtest_call_openai_chat_api_using_memory_optimization_truncate
    //xtest_call_openai_chat_api_using_memory_optimization_summary
    //openai options
    //stream
    // RAG
    // updateSystemMessage


    // enableMemory long memory saved in database
    // clearMemory long memory (db)
    public function test_long_memory_no_optimization(){
      $this->create_app_api([],['fakeLLM','session','returnMemory','enableMemory','memoryOptimization','fakeLLMOutput','userMessage']);
      $session = uniqid();

      $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
      ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'fakeLLM' => true,
          'session' => $session,
          'enableMemory' => 'longMemory',
          'returnMemory' => false,
          'memoryOptimization' => 'noOptimization',
          'fakeLLMOutput' => 'My Fake response',
          'userMessage' => 'User Message',
      ]);
      $response->assertStatus(200);
      $response->assertJsonPath('status', true);

      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->myApp->authToken,
      ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
        'fakeLLM' => true,
        'session' => $session,
        'enableMemory' => 'longMemory',
        'returnMemory' => true,
        'memoryOptimization' => 'noOptimization',
        'fakeLLMOutput' => 'My Fake response 2',
        'userMessage' => 'User Message 2',
      ]);
      $response->assertStatus(200);
      $response->assertJsonPath('status', true);

      $this->assertDatabaseHas('usages', [
        'app_id' => $this->myApp->id,
        'api_id' => $this->api->id,
        'hits' => 2,
      ]);

      // $this->assertTrue(count($response['debug'] ?? []) == 0);
    }


    public function xtest_call_openai_chat_api_using_memory_no_optimization()
    {
        
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

    public function xtest_call_openai_chat_api_using_memory_optimization_truncate()
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