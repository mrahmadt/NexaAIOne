<?php

namespace Tests\Unit\Models;

use App\Models\Api;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Collection;

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

    public function test_document_valid_create(){
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
          'loader_id' => 0,
        ]);
        $response->assertStatus(200); 

        //file test
        // $response = $this->withHeaders([
        //   'Authorization' => 'Bearer ' . $this->collection->authToken,
        // ])->post('api/v1/collections/documents/create',[
        //   'collection_id' => $this->collection->id,
        //   'content' => 'Hello Content1',
        //   'splitter_id' => 0,
        //   'loader_id' => 0,
        // ]);
        // $response->assertStatus(200);


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
        $response->assertJsonPath('documents.2.id', 2);
        $response->assertJsonPath('documents.2.content', 'Hello Content1');
        $response->assertJsonPath('documents.2.meta', null);
    }

    public function __test_document_invalid_create(){
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

    public function __test_document_status(){
      $response = $this->get('api/v1/collections/documents/status/InvalidJob');
      $response->assertStatus(200);
      $response->assertJson(['status' => true]);
    }

}