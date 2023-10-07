<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use App\Models\App;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;

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
        $file = public_path('examples/Example.ogg');
        $file = UploadedFile::fake()->createWithContent('Example.ogg', file_get_contents($file));
        $api = Api::where(['service_id'=>1])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'file' => $file,
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('example sound file');
    }

    public function test_transcribe_local_file_fake_llm() {
        $file = public_path('examples/Example.ogg');
        $file = UploadedFile::fake()->createWithContent('Example.ogg', file_get_contents($file));
        $api = Api::where(['service_id'=>1])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'file' => $file,
            'fakeLLM' => true,
            'fakeLLMOutput' => 'Fake LLM output',
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Fake LLM output');
    }

    public function test_translate_local_file() {
        $file = public_path('examples/Example.ogg');
        $file = UploadedFile::fake()->createWithContent('Example.ogg', file_get_contents($file));
        $api = Api::where(['service_id'=>2])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'file' => $file,
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('example sound file');
    }

    public function test_translate_local_file_fake_llm() {
        $file = public_path('examples/Example.ogg');
        $file = UploadedFile::fake()->createWithContent('Example.ogg', file_get_contents($file));
        $api = Api::where(['service_id'=>2])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'file' => $file,
            'fakeLLM' => true,
            'fakeLLMOutput' => 'Fake LLM output',
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Fake LLM output');
    }



    public function test_transcribe_url_file() {
        $api = Api::where(['service_id'=>1])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'fileURL' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/En-us-speeches.ogg',
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('speeches');
    }

    public function test_transcribe_url_file_fake_llm() {
        $api = Api::where(['service_id'=>1])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'fileURL' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/En-us-speeches.ogg',
            'fakeLLM' => true,
            'fakeLLMOutput' => 'Fake LLM output speeches',
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Fake LLM output speeches');
    }
    public function test_translate_url_file() {
        $api = Api::where(['service_id'=>2])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'fileURL' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/En-us-speeches.ogg',
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('speeches');
    }

    public function test_translate_url_file_fake_llm() {
        $api = Api::where(['service_id'=>2])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $app->authToken,
        ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
            'fileURL' => 'https://upload.wikimedia.org/wikipedia/commons/9/91/En-us-speeches.ogg',
            'fakeLLM' => true,
            'fakeLLMOutput' => 'Fake LLM output speeches',
            'response_format' => 'verbose_json',
        ]);
        $response->assertJsonPath('status', true);
        $response->assertStatus(200);
        $response->assertSeeText('Fake LLM output speeches');
    }
}
