<?php

namespace App\Jobs;

use App\Models\Splitter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Illuminate\Support\Facades\Cache;
use App\Models\Splitters;

class DocumentSplitterJob implements ShouldQueue
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
        'collection_id' => $request->collection_id,
        'contentFile' => $contentFile, //or null
        'meta' => $meta, // or null
        'splitter_id' => $splitter_id // number
        */
        $this->args = $args;
        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Text Splitter: Queed', 3600);
    }

    public function handle(): void {
        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Text Splitter: In progress', 3600);

        if(!$this->args['contentFile'] && $this->args['contentFile'] != '') return;

        $content = file_get_contents($this->args['contentFile']);
        if(!$content && $content != '') return;

        $splitter = Splitter::where(['id'=>$this->args['splitter_id']])->first();
        if(!$splitter) throw new \Exception('No Splitter found.');
        $className = '\App\Splitters\\' . $splitter->className;
        $splitterService = new $className($splitter->options);
        
        $response = $splitterService->splitText($content);

        $chunks = $response['content'] ?? [];

        $index = 0;
        foreach($chunks as $chunk){
            $index++;
            $meta = [
                '__index' => $index,
            ];
            if(isset($this->args['meta'])) { 
                $meta = array_merge($meta, $response['extraMetadata'], $this->args['meta']); 
            }
            $jobArgs = [
                'jobID' => $this->args['jobID'] ?? null,
                'collection_id' => $this->args['collection_id'],
                'content' => $chunk,
                'meta' => $meta,
            ];
            DocumentCreateOrUpdateJob::dispatch($jobArgs);
        }
    }

    /**
    * Handle a job failure.
    */
    public function failed(Throwable $exception): void
    {
        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Text Splitter: Failed', 3600);
    }
}
