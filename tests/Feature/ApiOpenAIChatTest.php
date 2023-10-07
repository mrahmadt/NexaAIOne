<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use App\Models\App;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

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
        // $test = DB::table('services')->get();
        // dd($test);
        // $this->seed();
    }
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function create_app_api($options_values = [], $options_apiOptions = [], $options_notApiOptions = [])
    {
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

    function detectSensitiveKeys ($array) {
      $total = 0;
      foreach ($array as $key => $value) {
          if (is_array($value)) {
              // Recursive call if value is another array
              $this->detectSensitiveKeys($value);
          } else {
              // Check if the key contains the word "key" or "auth"
              if (stripos($key, 'key') !== false || stripos($key, 'auth') !== false) {
                  if($value != '***'){
                    $total++;
                  }
              }
          }
      }
      return $total;
    }
    // app and api creation
    public function test_app_api_creation()
    {
        $this->create_app_api();
        $this->assertEquals($this->api->name, 'test');
    }

    // allow only api options
    public function test_allow_only_api_options()
    {
        $this->create_app_api([], ['cachingPeriod', 'session', 'model', 'enableMemory', 'systemMessage', 'updateSystemMessage', 'userMessage'], ['cacheScope']);
        $this->assertEquals($this->api->name, 'test');
        $this->assertEquals($this->api->options['Caching']['cachingPeriod']['isApiOption'], true);
        $this->assertEquals($this->api->options['Caching']['cacheScope']['isApiOption'], false);
    }
    // no auth
    public function test_no_auth()
    {
        $this->create_app_api();
        $response = $this->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'userMessage' => 'Response with your model name',
        ]);
        $response->assertStatus(403);
        $response->assertJson(['message' => 'No Token']);
    }

    // wrong auth
    public function test_wrong_auth()
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
    public function test_api_not_mapped_to_app()
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
    public function test_no_memory_set_system_custom_message()
    {
        $this->create_app_api([], ['returnMemory','cachingPeriod','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
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
    public function test_usage()
    {
        $this->create_app_api([], ['returnMemory','cachingPeriod','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
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

    public function test_must_return_cached_response_scope_session_and_global_and_clear_cache()
    {
        $this->create_app_api([], ['returnMemory','cachingPeriod', 'cacheScope','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
        $session = uniqid();
        $apiCall1 = [];
        $apiCall2 = [];
        foreach(['session','global'] as $cacheScope) {
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
    public function test_send_messages()
    {
        $this->create_app_api([], ['returnMemory','messages','cachingPeriod','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
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
    

    // check none api options
    public function test_none_api_option()
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

    // enableMemory long memory saved in database
    // clearMemory long memory (db)
    public function test_long_memory_no_optimization()
    {
        $this->create_app_api([], ['fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryOptimization','fakeLLMOutput','userMessage']);
        $session = uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'session' => $session,
            'enableMemory' => 'longMemory',
            'returnMemory' => true,
            'memoryOptimization' => 'noOptimization',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'User Message',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response');
        $response->assertJsonPath('memory.1.role', 'user');
        $response->assertJsonPath('memory.1.content', 'User Message');
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');

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
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response 2');
        $response->assertJsonPath('memory.1.role', 'user');
        $response->assertJsonPath('memory.1.content', 'User Message');
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');
        $response->assertJsonPath('memory.3.role', 'user');
        $response->assertJsonPath('memory.3.content', 'User Message 2');
        $response->assertJsonPath('memory.4.role', 'assistant');
        $response->assertJsonPath('memory.4.content', 'My Fake response 2');

        $this->assertDatabaseCount('memories', 1);
        $this->assertDatabaseHas('memories', [
          'app_id' => $this->myApp->id,
          'api_id' => $this->api->id,
        ]);

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'clearMemory' => 1,
          'enableMemory' => 'longMemory',
          'session' => $session,
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Memory cleared');
    }
    // enableMemory short memory saved in memory
    // clearMemory short memory
    public function test_short_memory_no_optimization()
    {
        $this->create_app_api([], ['fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryOptimization','fakeLLMOutput','userMessage']);
        $session = uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'session' => $session,
            'enableMemory' => 'shortMemory',
            'returnMemory' => true,
            'memoryOptimization' => 'noOptimization',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'User Message',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response');
        $response->assertJsonPath('memory.1.role', 'user');
        $response->assertJsonPath('memory.1.content', 'User Message');
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'fakeLLM' => true,
          'session' => $session,
          'enableMemory' => 'shortMemory',
          'returnMemory' => true,
          'memoryOptimization' => 'noOptimization',
          'fakeLLMOutput' => 'My Fake response 2',
          'userMessage' => 'User Message 2',
        ]);
        $response->assertStatus(200);

        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response 2');
        $response->assertJsonPath('memory.1.role', 'user');
        $response->assertJsonPath('memory.1.content', 'User Message');
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');
        $response->assertJsonPath('memory.3.role', 'user');
        $response->assertJsonPath('memory.3.content', 'User Message 2');
        $response->assertJsonPath('memory.4.role', 'assistant');
        $response->assertJsonPath('memory.4.content', 'My Fake response 2');

        $this->assertDatabaseCount('memories', 0);

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'clearMemory' => 1,
          'enableMemory' => 'shortMemory',
          'session' => $session,
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Memory cleared');
    }

    
    // updateSystemMessage
    public function test_system_update()
    {
        $this->create_app_api([], ['updateSystemMessage','fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryOptimization','fakeLLMOutput','userMessage']);
        $session = uniqid();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'session' => $session,
            'enableMemory' => 'shortMemory',
            'returnMemory' => true,
            'memoryOptimization' => 'noOptimization',
            'fakeLLMOutput' => 'My Fake response',
            'userMessage' => 'User Message',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('message.content', 'My Fake response');
        $response->assertJsonPath('memory.1.role', 'user');
        $response->assertJsonPath('memory.1.content', 'User Message');
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'fakeLLM' => true,
          'session' => $session,
          'enableMemory' => 'shortMemory',
          'returnMemory' => true,
          'memoryOptimization' => 'noOptimization',
          'fakeLLMOutput' => 'My Fake response 2',
          'updateSystemMessage' => 'system Message 2',
          'userMessage' => 'User Message 2',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('status', true);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('memory.3.role', 'system');
        $response->assertJsonPath('memory.3.content', 'system Message 2');
    }

        // Test model
        public function test_model()
        {
            $model = 'gpt-3.5-turbo';
            $this->create_app_api([], ['fakeLLM','model']);
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->myApp->authToken,
            ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
                'fakeLLM' => true,
                'model' => $model,
                'userMessage' => 'Hello',
            ]);
            $response->assertJsonPath('serviceResponse.model', $model);
            $response->assertStatus(200);
        }
        
    
            
    
        // using memory optimization truncate
        public function test_using_memory_optimization_truncate()
        {
            $session = uniqid();
            $memoryMaxTokenPercentage = 30;
            $memoryPeriod = 46;
            $enableMemory = 'truncate';
            $model = 'gpt-3.5-turbo';
            $this->create_app_api([], ['updateSystemMessage','fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryPeriod','memoryMaxTokenPercentage', 'memoryOptimization','fakeLLMOutput','userMessage','model']);
            $session = uniqid();
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $this->myApp->authToken,
            ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
                'fakeLLM' => true,
                'model' => $model,
                'session' => $session,
                'enableMemory' => 'shortMemory',
                'returnMemory' => true,
                'memoryOptimization' => $enableMemory,
                'memoryMaxTokenPercentage' => $memoryMaxTokenPercentage,
                'fakeLLMOutput' => 'My Fake response',
                'memoryPeriod' => $memoryPeriod,
                'userMessage' => file_get_contents(public_path('examples/MSGSphere.txt')),
                //'debug' => true,
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $response->assertJsonPath('serviceResponse.fakeLLM', true);
            $response->assertJsonPath('memory.2.role', 'assistant');
            $response->assertJsonPath('memory.2.content', 'My Fake response');
            $response->assertSeeText('The Sphere under construction in September 2022');
            $response = $this->withHeaders([
              'Authorization' => 'Bearer ' . $this->myApp->authToken,
          ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
              'fakeLLM' => true,
              'model' => $model,
              'session' => $session,
              'enableMemory' => 'shortMemory',
              'returnMemory' => true,
              'memoryOptimization' => $enableMemory,
              'memoryMaxTokenPercentage' => $memoryMaxTokenPercentage,
              'fakeLLMOutput' => 'My Fake response',
              'memoryPeriod' => $memoryPeriod,
              'userMessage' => 'Remove old history',
          ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $response->assertJsonPath('memoryOptimization', 'Truncate');
            $response->assertJsonPath('serviceResponse.fakeLLM', true);
            $response->assertJsonPath('memory.1.role', 'assistant');
            $response->assertJsonPath('memory.1.content', 'My Fake response');
            $response->assertJsonPath('memory.2.role', 'user');
            $response->assertJsonPath('memory.2.content', 'Remove old history');
            $response->assertJsonPath('memory.3.role', 'assistant');
            $response->assertJsonPath('memory.3.content', 'My Fake response');
        }

        public function test_return_debug()
        {
            $this->create_app_api([], ['returnMemory','messages','debug','session','model','enableMemory','systemMessage','userMessage', 'fakeLLM','fakeLLMOutput']);
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
            $totalSensitiveKeys = $this->detectSensitiveKeys($response['debug']);
            $this->assertTrue($totalSensitiveKeys == 0);
        }
    
    // using memory optimization summarization
    public function test_using_memory_optimization_summarization()
    {
        $session = uniqid();
        $memoryMaxTokenPercentage = 30;
        $memoryPeriod = 46;
        $enableMemory = 'summarization';
        $model = 'gpt-3.5-turbo';
        $this->create_app_api([], ['updateSystemMessage','fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryPeriod','memoryMaxTokenPercentage', 'memoryOptimization','fakeLLMOutput','userMessage','model']);
        $session = uniqid();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'fakeLLM' => true,
            'model' => $model,
            'session' => $session,
            'enableMemory' => 'shortMemory',
            'returnMemory' => true,
            'memoryOptimization' => $enableMemory,
            'memoryMaxTokenPercentage' => $memoryMaxTokenPercentage,
            'fakeLLMOutput' => 'My Fake response',
            'memoryPeriod' => $memoryPeriod,
            'userMessage' => file_get_contents(public_path('examples/MSGSphere.txt')),
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('memory.2.role', 'assistant');
        $response->assertJsonPath('memory.2.content', 'My Fake response');
        $response->assertSeeText('The Sphere under construction in September 2022');
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->myApp->authToken,
      ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
          'fakeLLM' => true,
          'model' => $model,
          'session' => $session,
          'enableMemory' => 'shortMemory',
          'returnMemory' => true,
          'memoryOptimization' => $enableMemory,
          'memoryMaxTokenPercentage' => $memoryMaxTokenPercentage,
          'fakeLLMOutput' => 'My Fake response',
          'memoryPeriod' => $memoryPeriod,
          'userMessage' => 'Remove old history',
      ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertJsonPath('memoryOptimization', 'Summarization');
        $response->assertJsonPath('serviceResponse.fakeLLM', true);
        $response->assertJsonPath('memory.1.role', 'system');
        $response->assertJsonPath('memory.1.content', 'Previous context:My Fake response');
        $response->assertJsonPath('memory.2.role', 'user');
        $response->assertJsonPath('memory.2.content', 'Remove old history');
        $response->assertJsonPath('memory.3.role', 'assistant');
        $response->assertJsonPath('memory.3.content', 'My Fake response');
    }
    // openai
    public function test_openai_api_stream()
    {
        $model = 'gpt-3.5-turbo';
        $this->create_app_api([], ['stream','updateSystemMessage','fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryPeriod','memoryMaxTokenPercentage', 'memoryOptimization','fakeLLMOutput','userMessage','model']);
        ob_start();
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])
        ->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'model' => $model,
            'enableMemory' => 'disabled',
            'returnMemory' => true,
            'stream' => true,
            'userMessage' => 'My name is Ice tea. What is my name?',
        ]);
        $response->assertStatus(200);
        $response->assertSeeText('StreamedMessage');
        $ob = ob_get_contents();
        $this->assertTrue(strpos($ob, '"index":0,"delta":{"role":"assistant","content":""') !== false);
        // dd($ob);
        ob_end_flush();
        ob_get_clean();
    }

    // openai
    public function test_openai_api()
    {
        $model = 'gpt-3.5-turbo';
        $this->create_app_api([], ['updateSystemMessage','fakeLLM','session','clearMemory','returnMemory','enableMemory','memoryPeriod','memoryMaxTokenPercentage', 'memoryOptimization','fakeLLMOutput','userMessage','model']);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->myApp->authToken,
        ])->post('/api/v1/app/'.$this->myApp->id.'/' .$this->api->id.'/' . $this->api->name, [
            'model' => $model,
            'enableMemory' => 'disabled',
            'returnMemory' => true,
            'userMessage' => 'My name is Ice tea. What is my name?',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Ice tea');
    }

    // RAG
    public function test_rag()
    {
        //api id = 7
        //get api with endpoint = HRSupportAgent
        $api = Api::where(['endpoint'=>'HRSupportAgent'])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'userMessage' => 'How many vacation days I can carry over?',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('25 days');
    }
    // stream
}
