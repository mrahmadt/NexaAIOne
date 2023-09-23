<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Document;
use Illuminate\Support\Facades\Cache;

class CreateDocumentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $args;

    public function __construct($args)
    {
        $this->args = $args;
    }

    public function handle(): void
    {
        try {
            $content = null;
        
            if (isset($this->args['content'])) {
                $content = $this->args['content'];
            } elseif (isset($this->args['url'])) {
                $content = file_get_contents($this->args['url']);
            } elseif (isset($this->args['file'])) {
                $content = file_get_contents($this->args['file']);
            }
            if ($content === null) {
                throw new \Exception('No content provided.');
            }
            // $this->args['disable_splitter']
            // $table->unsignedBigInteger('loader_id')->nullable();
            // $table->unsignedBigInteger('splitter_id')->nullable();;

            Document::create([
                'content' => $content,
                'meta' => $this->args['meta'],
                'collection_id' => $this->args['collection_id'],
            ]);

            Cache::put($this->args['jobID'], 'completed', 60 * 60);

        } catch (\Exception $e) {
            Cache::put($this->args['jobID'], 'error', 60 * 60);
        }
    }
}
