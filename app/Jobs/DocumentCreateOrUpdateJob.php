<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use App\Models\Document;
use Illuminate\Support\Facades\Cache;

class DocumentCreateOrUpdateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     *
     * @var int
     */
    public $maxExceptions = 3;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;

    protected $args;

    public function __construct($args)
    {
        /*
        'jobID' => $jobID, or null 
        'document_id' => $document_id, // or null
        'collection_id' => $request->collection_id,
        'content' => //or null
        'contentFile' => //or null
        'meta' => $meta, // or null
        */
        $this->args = $args;
        // if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Document Create/Update: Queed', 3600);
    }

    public function handle(): void
    {

        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Document Create/Update: In progress', 3600);
        if (isset($this->args['document_id'])) {
            $document = Document::where(['id' => $this->args['document_id']])->first();
            if ($document) {
                
                if (isset($this->args['content']) && $this->args['content'] != ''){
                    $document->content = $this->args['content'];
                }elseif (isset($this->args['contentFile']) && $this->args['contentFile'] != ''){
                    $document->content = file_get_contents($this->args['contentFile']);
                }

                if (isset($this->args['meta'])) $document->meta = $this->args['meta'];
                $document->save();
            }
        }else{
            $data = [ 
                'collection_id' => $this->args['collection_id'],
            ];

            if(isset($this->args['content']) && $this->args['content'] != ''){
                $data['content'] = $this->args['content'];
            }elseif (isset($this->args['contentFile']) && $this->args['contentFile'] != ''){
                $data['content'] = file_get_contents($this->args['contentFile']);
            }

            if (isset($this->args['meta'])) $data['meta'] = $this->args['meta'];
            $document = Document::create($data);
        }

        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Document Create/Update: Completed ' . $document->id, 3600);
    }

    /**
    * Handle a job failure.
    */
    public function failed(Throwable $exception): void
    {
        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Document Create/Update: Failed', 3600);
    }
}
