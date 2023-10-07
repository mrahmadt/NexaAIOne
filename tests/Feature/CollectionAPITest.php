<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Collection;
use Illuminate\Support\Facades\DB;

class CollectionAPITest extends TestCase
{
    use RefreshDatabase;
    protected $collection;

    public function setUp(): void {
        parent::setUp();
        $this->seed();
        $this->collection = Collection::factory()->create();
    }

    public function tearDown(): void {
        parent::tearDown();
    }

    public function test_app_collection_create_delete() {
      $api = Api::where(['service_id'=>3])->first();
      $app = DB::table('apps')->where(['id'=>1])->first();

      $response = $this->withHeaders([
          'Authorization' => 'Bearer ssss' . $app->authToken,
      ])->post('/api/v1/appCollection/create', [
          'name' => 'Test Name',
          'app_id' => $app->id,
      ]);
      $response->assertStatus(403);
      $response->assertJsonPath('status', false);

      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $app->authToken,
      ])->post('/api/v1/appCollection/create', [
        'name' => 'Test Name',
        'app_id' => 12221111,
      ]);
      $response->assertStatus(403);
      $response->assertJsonPath('status', false);

      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $app->authToken,
      ])->post('/api/v1/appCollection/create', [
        'app_id' => 12221111,
      ]);
      $response->assertStatus(404);
      $response->assertJsonPath('status', false);

      $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $app->authToken,
      ])->post('/api/v1/appCollection/create', [
          'name' => 'Test Name',
          'app_id' => $app->id,
      ]);
      $response->assertStatus(200);
      $response->assertJsonPath('status', true);
      $response->assertJsonPath('collection_id', 3);
      $response->assertSeeText('authToken');

      $responseData = $response->getOriginalContent();
      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $app->authToken,
      ])->delete('/api/v1/appCollection/delete', [
          'app_id' => $app->id,
          'collection_id' => $responseData['collection_id'],
          'collection_authToken' => $responseData['authToken'],
      ]);
      $response->assertStatus(200);
      $response->assertJsonPath('status', true);
  }


    public function test_document_with_RecursiveCharacterTextSplitter(){
      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
      ])->post('api/v1/collections/documents/create', [
        'collection_id' => $this->collection->id,
        'content' => file_get_contents(public_path('examples/JWST.txt')),
        'splitter_id' => 1,
      ]);
      $response->assertStatus(200);

      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
      ])->get('api/v1/collections/documents/list/' . $this->collection->id);
      $response->assertStatus(200);
      $this->assertTrue($response['status']);
      $this->assertTrue(count($response['documents'])>1);
      // RecursiveCharacterTextSplitter
      //  {"separators":["\n\n","\n"," ",""],"chunk_size":4000,"chunk_overlap":200,"keep_separator":0,"strip_whitespace":1,"is_separator_regex":0}

    }

    public function test_document_with_CharacterTextSplitter(){
      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
      ])->post('api/v1/collections/documents/create', [
        'collection_id' => $this->collection->id,
        'content' => file_get_contents(public_path('examples/JWST.txt')),
        'splitter_id' => 2,
      ]);
      $response->assertStatus(200);

      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
      ])->get('api/v1/collections/documents/list/' . $this->collection->id);
      $response->assertStatus(200);
      $this->assertTrue($response['status']);
      $this->assertTrue(count($response['documents'])>1);
      // CharacterTextSplitter
      //  {"separator":"\n\n","chunk_size":4000,"chunk_overlap":200,"keep_separator":0,"strip_whitespace":1,"is_separator_regex":0}
    }

    public function test_document_with_TokenTextSplitter(){
      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
      ])->post('api/v1/collections/documents/create', [
        'collection_id' => $this->collection->id,
        'content' => file_get_contents(public_path('examples/JWST.txt')),
        'splitter_id' => 3,
      ]);
      $response->assertStatus(200);

      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
      ])->get('api/v1/collections/documents/list/' . $this->collection->id);
      $response->assertStatus(200);
      $this->assertTrue($response['status']);
      $this->assertTrue(count($response['documents'])>1);
      // TokenTextSplitter
      //  {"encoding_name":"p50k_base","chunk_size":500,"chunk_overlap":60,"strip_whitespace":1}
    }

    public function test_document_valid_curd(){
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->post('api/v1/collections/documents/create',[
          'collection_id' => $this->collection->id,
          'content' => 'Hello Content',
          'meta' => json_encode(['hello'=>'Hello Meta']),
          'splitter_id' => 0,
          'loader_id' => 0,
        ]);
        $response->assertStatus(200);

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->post('api/v1/collections/documents/create',[
          'collection_id' => $this->collection->id,
          'content' => 'Hello Content1',
          'splitter_id' => 0,
          'loader_id' => 0,
        ]);
        $response->assertStatus(200);

        //url test
        $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->post('api/v1/collections/documents/create',[
          'collection_id' => $this->collection->id,
          'url' => 'https://raw.githubusercontent.com/mrahmadt/NexaAIOne/main/public/examples/document.txt',
          'splitter_id' => 0,
        ]);
        $response->assertStatus(200); 

        //file test
        $file = UploadedFile::fake()->createWithContent('document2.txt', '2nd document');
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])
        ->post('api/v1/collections/documents/create',[
          'collection_id' => $this->collection->id,
          'file' => $file,
          'splitter_id' => 0,
        ]);
        $response->assertStatus(200);

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->get('api/v1/collections/documents/list/' . $this->collection->id);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);

        // content with meta
        $response->assertJsonPath('documents.0.id', 1);
        $response->assertJsonPath('documents.0.content', 'Hello Content');
        $response->assertJsonPath('documents.0.meta', '{"hello":"Hello Meta"}');

        // content without meta
        $response->assertJsonPath('documents.1.id', 2);
        $response->assertJsonPath('documents.1.content', 'Hello Content1');
        $response->assertJsonPath('documents.1.meta', null);

        // url
        $response->assertJsonPath('documents.2.id', 3);
        $response->assertJsonPath('documents.2.content', 'In principle');
        $response->assertJsonPath('documents.2.meta', null);

        // file
        $response->assertJsonPath('documents.3.id', 4);
        $response->assertJsonPath('documents.3.content', '2nd document');
        $response->assertJsonPath('documents.3.meta', null);



        // UPDATE

        // new content and meta
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->put('api/v1/collections/document/update/1',[
          'collection_id' => $this->collection->id,
          'content' => 'New Content',
          'meta' => json_encode(['hello'=>'New Meta']),
          'splitter_id' => 0,
          'loader_id' => 0,
        ]);
        $response->assertStatus(200);

        // new content and no meta change
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->put('api/v1/collections/document/update/2',[
          'collection_id' => $this->collection->id,
          'content' => 'New Content only',
          'splitter_id' => 0,
          'loader_id' => 0,
        ]);
        $response->assertStatus(200);

        // no content and new meta only
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->put('api/v1/collections/document/update/3',[
          'collection_id' => $this->collection->id,
          'meta' => json_encode(['hello'=>'New meta only']),
          'splitter_id' => 0,
          'loader_id' => 0,
        ]);
        $response->assertStatus(200);

        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->get('api/v1/collections/documents/list/' . $this->collection->id);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);

        // dd($response);
        $response->assertJsonPath('documents.0.id', 1);
        $response->assertJsonPath('documents.0.content', 'New Content');
        $response->assertJsonPath('documents.0.meta', '{"hello":"New Meta"}');

        $response->assertJsonPath('documents.1.id', 2);
        $response->assertJsonPath('documents.1.content', 'New Content only');
        $response->assertJsonPath('documents.1.meta', null);

        $response->assertJsonPath('documents.2.id', 3);
        $response->assertJsonPath('documents.2.content', 'In principle');
        $response->assertJsonPath('documents.2.meta',  '{"hello":"New meta only"}');


        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->get('api/v1/collections/document/get/3',[
          'collection_id' => $this->collection->id,
        ]);
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $response->assertJsonPath('document_id', 3);
        $response->assertJsonPath('content', 'In principle');
        $response->assertJsonPath('meta',  '{"hello":"New meta only"}');

        // delete
        $response = $this->withHeaders([
          'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->delete('api/v1/collections/document/delete/3');
        $response->assertStatus(200);
        $this->assertTrue($response['status']);
        $response->assertJsonPath('message', 'Document deleted successfully');

    }

    public function test_document_invalid_create(){
      $response = $this->post('api/v1/collections/documents/create',['collection_id' => 100]);
      $response->assertStatus(404);

      // no token
      $response = $this->post('api/v1/collections/documents/create',[
        'collection_id' => $this->collection->id,
        'content' => 'Hello Content',
        'meta' => json_encode(['hello'=>'Hello Meta']),
        'splitter_id' => 0,
        'loader_id' => 0,
      ]);
      $response->assertStatus(403);


      // invalid token
      $response = $this->withHeaders([
          'Authorization' => 'Bearer AAA',
        ])->post('api/v1/collections/documents/create',[
          'collection_id' => $this->collection->id,
          'content' => 'Hello Content',
          'meta' => json_encode(['hello'=>'Hello Meta']),
          'splitter_id' => 0,
          'loader_id' => 0,
        ]);
      $response->assertStatus(403);

      //The meta field must be a valid JSON string
      $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->collection->authToken,
        ])->post('api/v1/collections/documents/create',[
        'collection_id' => $this->collection->id,
        'content' => 'Hello Content',
        'meta' => 'Hello Meta',
        'splitter_id' => 0,
        'loader_id' => 0,
      ]);
      $response->assertStatus(404);
    }

    public function test_document_status(){
      $response = $this->get('api/v1/collections/documents/status/InvalidJob');
      $response->assertStatus(200);
      $response->assertJson(['status' => true]);
    }

}