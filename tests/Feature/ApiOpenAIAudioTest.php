<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use App\Models\App;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class ApiOpenAIAudioTest extends TestCase
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
    }
    public function tearDown(): void {
        parent::tearDown();
    }

    // file
    // fileURL
    // response_format
    // fakeLLM
    // fakeLLMOutput
    public function test_transcribe_local_file() {
        //api id = 7
        //get api with endpoint = HRSupportAgent
        $api = Api::where(['service_id'=>1])->first();
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
