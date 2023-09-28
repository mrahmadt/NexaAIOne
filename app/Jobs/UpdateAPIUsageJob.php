<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Usage;

class UpdateAPIUsageJob implements ShouldQueue
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

    protected $usage;

    public function __construct($usage)
    {

        /*
            app_id
            api_id
            promptTokens
            completionTokens
            totalTokens
        */
        
        /*
            date
            counter total hits 
        */
        $this->usage = $usage;
    }

    public function handle(): void
    {
        $usage = Usage::where(
            ['app_id' => $this->usage['app_id'], 'api_id' => $this->usage['api_id'] , 'date' => now()->toDateString()]
        )->first();
        if($usage){
            $usage->hits++;
            $usage->promptTokens += $this->usage['promptTokens'] ?? 0;
            $usage->completionTokens += $this->usage['completionTokens'] ?? 0;
            $usage->totalTokens += $this->usage['totalTokens'] ?? 0;
            $usage->save();
        }else{
            $data = [
                'app_id' => $this->usage['app_id'],
                'api_id' => $this->usage['api_id'],
                'hits' => 1,
                'promptTokens' => $this->usage['promptTokens'] ?? 0,
                'completionTokens' => $this->usage['completionTokens'] ?? 0,
                'totalTokens' => $this->usage['totalTokens'] ?? 0,
                'date' => now()->toDateString(),
            ];
            Usage::create($data);
        }
    }

}
