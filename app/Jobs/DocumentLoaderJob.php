<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;
use Illuminate\Support\Facades\Cache;
use App\Jobs\DocumentSplitterJob;
use App\Jobs\DocumentCreateOrUpdateJob;
use App\Models\Loader;

class DocumentLoaderJob implements ShouldQueue
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
        'content' => $content, //or null
        'url' => $url, //or null
        'file' => $file, //or null
        'meta' => $meta, // or null
        'splitter_id' => $splitter_id // false, number or null
        'loader_id' => $loader_id, // number
        */
        $this->args = $args;
        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Loading Document: Queed', 3600);
        if( isset($this->args['file'])){
            $filearg = $this->args['file'];
            $data = file_get_contents($filearg);
            unset($this->args['file']);
            $tempFile = tempnam(sys_get_temp_dir(), 'docloader_');
            if ($tempFile !== false) {
                $extension = pathinfo($filearg, PATHINFO_EXTENSION);
                $tempFile .= '.' . $extension;
                file_put_contents($tempFile, $data);
                $this->args['file'] = $tempFile;
            }
        }
    }

    public function handle(): void
    {
            if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Loading Document: In progress', 3600);
            $content = null;
        
            if (isset($this->args['content'])) {
                $content = $this->args['content'];
            } elseif ((isset($this->args['url']) || isset($this->args['file'])) && isset($this->args['loader_id'])) {
                $loader = Loader::where(['id'=>$this->args['loader_id']])->first();
                if(!$loader) throw new \Exception('No loader found.');
                $className = '\App\Loaders\\' . $loader->className;
                $loaderService = new $className($loader->options);
                if(isset($this->args['file'])){
                    $response = $loaderService->execute($this->args['file']);
                }else{
                    $response = $loaderService->execute($this->args['url']);
                }
                if(!isset($response['content'])) throw new \Exception('No content found.');
                $content = $response['content'];
            }

            if (!isset($this->args['document_id']) && $content === null) {
                throw new \Exception('No content provided.');
            }elseif (isset($this->args['document_id']) && $content === null && !isset($this->args['meta'])) {
                throw new \Exception('No content/metadata provided.');
            }

            $jobArgs = [
                'jobID' => $this->args['jobID'] ?? null,
                'collection_id' => $this->args['collection_id'],
                'content' => $content,
                'meta' => $this->args['meta'] ?? null,
            ];
            
            if (isset($this->args['document_id']) || !isset($this->args['splitter_id']) || $this->args['splitter_id'] === false || $this->args['splitter_id'] === null) {
                $jobArgs['document_id'] = $this->args['document_id'] ?? null;
                DocumentCreateOrUpdateJob::dispatch($jobArgs);
            }else{
                $jobArgs['splitter_id'] = $this->args['splitter_id'];
                DocumentSplitterJob::dispatch($jobArgs);
            }
    }

    /**
    * Handle a job failure.
    */
    public function failed(Throwable $exception): void
    {
        if (isset($this->args['jobID'])) Cache::put($this->args['jobID'], 'Loading Document: Failed', 3600);
    }
}
