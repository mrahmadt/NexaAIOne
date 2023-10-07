<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use App\Models\App;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;

class ApiOpenAIImageTest extends TestCase
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

    public function test_create_image_return_b64() {
        $api = Api::where(['service_id'=>4])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        foreach([true, false] as $fakeLLM){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $app->authToken,
            ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
                'response_format' => 'b64_json',
                'fakeLLM' => $fakeLLM,
                'prompt' => 'natural green landscape',
            ]);
            $responseJson = json_decode($response->decodeResponseJson()->json);
            $this->assertTrue(isset($responseJson->serviceResponse->data[0]->b64_json));
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
        }
    }

    
    public function test_create_image_return_url() {
        $api = Api::where(['service_id'=>4])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        foreach([true, false] as $fakeLLM){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $app->authToken,
            ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
                'response_format' => 'url',
                'fakeLLM' => $fakeLLM,
                'prompt' => 'natural green landscape',
            ]);
            $responseJson = json_decode($response->decodeResponseJson()->json);
            $this->assertTrue(isset($responseJson->serviceResponse->data[0]->url));
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
        }
    }

    public function test_edit_image_local_file_return_url() {
        $api = Api::where(['service_id'=>5])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $file = public_path('examples/photo.png');
        $file = UploadedFile::fake()->createWithContent('photo.png', file_get_contents($file));
        $fileMask = UploadedFile::fake()->createWithContent('photo.png', file_get_contents($file));
        foreach([true] as $fakeLLM){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $app->authToken,
            ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
                'image'=> $file,
                'mask'=> $fileMask,
                'response_format' => 'url',
                'fakeLLM' => $fakeLLM,
                'prompt' => 'natural green landscape',
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $responseJson = json_decode($response->decodeResponseJson()->json);
            $this->assertTrue(isset($responseJson->serviceResponse->data[0]->url));
        }
    }

    public function test_edit_image_url_file_return_url() {
        $api = Api::where(['service_id'=>5])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        foreach([true] as $fakeLLM){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $app->authToken,
            ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
                'imageURL'=> 'http://localhost/examples/photo.png',
                'maskURL'=> 'http://localhost/examples/photo.png',
                'response_format' => 'url',
                'fakeLLM' => $fakeLLM,
                'prompt' => 'natural green landscape',
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $responseJson = json_decode($response->decodeResponseJson()->json);
            $this->assertTrue(isset($responseJson->serviceResponse->data[0]->url));
        }
    }




    public function test_variation_image_local_file_return_url() {
        $api = Api::where(['service_id'=>6])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        $file = public_path('examples/photo.png');
        $file = UploadedFile::fake()->createWithContent('photo.png', file_get_contents($file));
        foreach([true] as $fakeLLM){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $app->authToken,
            ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
                'image'=> $file,
                'response_format' => 'url',
                'fakeLLM' => $fakeLLM,
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $responseJson = json_decode($response->decodeResponseJson()->json);
            $this->assertTrue(isset($responseJson->serviceResponse->data[0]->url));
        }
    }

    public function test_variation_image_url_file_return_url() {
        $api = Api::where(['service_id'=>6])->first();
        $app = DB::table('apps')->where(['id'=>1])->first();
        foreach([true] as $fakeLLM){
            $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $app->authToken,
            ])->post('/api/v1/app/'.$app->id.'/' .$api->id.'/' . $api->name, [
                'imageURL'=> 'http://localhost/examples/photo.png',
                'response_format' => 'url',
                'fakeLLM' => $fakeLLM,
            ]);
            $response->assertJsonPath('status', true);
            $response->assertStatus(200);
            $responseJson = json_decode($response->decodeResponseJson()->json);
            $this->assertTrue(isset($responseJson->serviceResponse->data[0]->url));
        }
    }
}
