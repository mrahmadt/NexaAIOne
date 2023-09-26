<?php
namespace App\Features;

use App\Models\Debug;

trait HasDebug
{
        
    /**
     * An array that stores the debug information.
     *
     * @var array
     */
    protected $debug = [];

    /**
     * An array that defines the options for debugging.
     *
     * @var array
     */
    protected static $debugOptions = [
        "debug" => [
            "name" => "debug",
            "type" => "boolean",
            "required" => false,
            "desc" => "Retains all request and response data, facilitating issue troubleshooting and prompt optimization",
            "default" => 0,
            "_group" => 'Debugging',
        ],
    ];

    /**
     * Adds debug information to the `$debug` array.
     *
     * @param string $function
     * @param mixed $data
     * @return void
     */
    private function debug($function, $data){
        if(!isset($this->options['debug']) || $this->options['debug'] == false) {
            return;
        }
        if(in_array($function, ['input', 'output'])){
            $this->debug[$function] = array_merge($this->debug[$function] ?? [], $data);
        }else{
            $this->debug['backtrace'][] = [$function => $data];
        }

    }

    private function replaceSensitiveKeys(&$array) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                // Recurse into the array
                $this->replaceSensitiveKeys($value);
            } else {
                // Check if the key is 'abc' or 'xyz'
                if ($key === 'openaiApiKey' || $key === 'Authorization') {
                    $value = '***';
                }
            }
        }
    }
    /**
     * Saves the debug information to the database and a log file.
     *
     * @return void
     */
    private function saveDebug(){
        if(!isset($this->options['debug']) || $this->options['debug'] == false) {
            return;
        }
        $debug = [
            'api_id' => $this->api_id,
            'session' => $this->options['session']  ?? 'global',
            'input' => $this->debug['input'] ?? null,
            'output' => $this->debug['output'] ?? null,
            'backtrace' => $this->debug['backtrace'] ?? null,
        ];
        $this->replaceSensitiveKeys($debug['input']);
        $this->replaceSensitiveKeys($debug['backtrace']);
        Debug::create($debug);
        file_put_contents(storage_path('logs/'.time().'.json'), json_encode($debug, JSON_PRETTY_PRINT) . "\n");
        //filter keys and short messages?
    }
}