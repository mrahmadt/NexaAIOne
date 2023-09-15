<?php

namespace App\Features;
use Illuminate\Support\Facades\Cache;

trait HasMemory
{
    use HasMessages;
    use HasOptions;

    protected static $memoryOptions = [
        'enableMemory' => [
            "name" => "enableMemory",
            "type" => "select",
            "required" => false,
            "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations.",
            "default" => true,
            "isApiOption" => false,
            "options"=>[
                'disable' => 'Disable',
                'noOptimization' => 'No memory optimization',
                'truncate' => 'Truncate',
                'summary' => 'Summary',
                'embeddings' => 'Embeddings',
            ]
        ],
        'memoryPeriod' => [
            "name" => "memoryPeriod",
            "type" => "number",
            "required" => false,
            "desc" => "How long, in minutes, should the conversation be retained in memory? If no new messages are received within this duration, the conversation history will be cleared",
            "default" => 60,
            "isApiOption" => false,
        ],
        'memoryMaxTokenPercentage' => [
            "name" => "memoryMaxTokenPercentage",
            "type" => "number",
            "required" => false,
            "desc" => "Defines the threshold, as a percentage of the LLM Model's max tokens, at which memory optimization will be triggered. When memory token usage reaches this specified percentage, optimization measures specified in the enableMemory variable will be enacted.",
            "default" => 50,
            "isApiOption" => false,
        ],
        'clearMemory' => [
            "name" => "clearMemory",
            "type" => "boolean",
            "required" => false,
            "desc" => "Clear memory for this session.",
            "isApiOption" => true,
            "default" => false,
        ],
        'session' => [
            "name" => "session",
            "type" => "string",
            "required" => false,
            "desc" => "Unique session id for this conversation.",
            "default" => "global",
            "isApiOption" => true,
        ],
    ];

    protected $messages;
    protected $messagesMeta;

    protected $memoryKey;
    protected $memoryMetakey;

    private function optimizeMemory(){
        if (!$this->__memoryInit() || $this->options['enableMemory'] == 'noOptimization') return false;
        // Calculate % of model Max Tokens
        // FIXME: do we need to reduce memory to be % below memoryMaxTokenPercentage? so we don't have to optimize memory after every message?
        $modelMaxTokens = $this->getMaxTokensForModel();
        $historyMaxTokens = ($this->options['memoryMaxTokenPercentage']/100) * $modelMaxTokens;
        if ($this->messagesMeta['all']['totalTokens'] >= $historyMaxTokens) {
            $lastMessage = array_pop($this->messages);
            $lastMessageMeta = array_pop($this->messagesMeta);

            if ($this->options['enableMemory'] == 'truncate'){
                $this->optimizeMemoryTruncate($historyMaxTokens);

            }elseif ($this->options['enableMemory'] == 'summary'){
                $this->optimizeMemorySummary($historyMaxTokens);

            }elseif ($this->options['enableMemory'] == 'embeddings'){

            }
            $this->messages[] = $lastMessage;
            $this->messagesMeta[] = $lastMessageMeta;
            $this->messagesMeta['all']['totalTokens'] = 0;
            foreach($this->messagesMeta as $meta) {
                if(isset($meta['tokens'])) {
                    $this->messagesMeta['all']['totalTokens'] += $meta['tokens'];
                }
            }
        }
    }

    private function optimizeMemoryTruncate($historyMaxTokens) {
        // Keep system messages, and move them to the top of the message history
        $tokensCounted = 0;
        $messagesToKeep = [];
        $messagesMetaToKeep = [];
        $handleMessage = function($index, $message) use (&$tokensCounted, &$messagesToKeep, &$messagesMetaToKeep) {
            $messagesToKeep[] = $message;
            $messagesMetaToKeep[] = $this->messagesMeta[$index];
            $tokensCounted += $this->messagesMeta[$index]['tokens'];
        };

        foreach (array_reverse($this->messages, true) as $index => $message) {
            if ($tokensCounted < $historyMaxTokens) {
                if($message['role'] == 'system') continue;
                $handleMessage($index, $message);
            } else {
                break;
            }
        }

        foreach (array_reverse($this->messages, true) as $index => $message) {
            if ($message['role'] === 'system') {
                $handleMessage($index, $message);
                break;
            }
        }

        $this->messages = array_reverse($messagesToKeep);
        $this->messagesMeta = array_reverse($messagesMetaToKeep);
    }

    private function optimizeMemorySummary($historyMaxTokens) {
        $prompt_system = [ 'role'=> 'system', 'content'=> config('memory.summarization_prompt') ];
        $totalWords = ($historyMaxTokens / 4) . ' words';
        $totalTokens = $historyMaxTokens . ' tokens';
        $prompt_system['content'] = str_replace(['{{totalTokens}}','{{totalWords}}'], [$totalTokens,$totalWords], $prompt_system['content']);
        $conversationToSummary = null;

        $tokensCounted = 0;
        $messagesToKeep = [];
        $messagesMetaToKeep = [];


        foreach($this->messages as $index => $message) {
            if($message['role'] == 'user'){
                $conversationToSummary .= 'User:' . $message['content'] . "\n";
            }elseif($message['role'] == 'assistant'){
                $conversationToSummary .= 'AI:' . $message['content'] . "\n";
            }elseif($message['role'] == 'system'){
                $messagesToKeep[] = $message;
                $messagesMetaToKeep[] = $this->messagesMeta[$index];
                $tokensCounted += $this->messagesMeta[$index]['tokens'];
            }
        }
        $prompt_user = [ 'role'=> 'user', 'content'=> $conversationToSummary ];
        $ChatCompletionOptions = $this->ChatCompletionOptions;
        $ChatCompletionOptions['model'] = config('memory.summarization_model') ?? $ChatCompletionOptions['model'];
        $response = $this->sendMessageToLLM([$prompt_system, $prompt_user], $ChatCompletionOptions, true);

        $messagesToKeep[] = ['role'=>'system', 'content'=> 'Previous context:'. $response->choices[0]->message->content];
        $messagesMetaToKeep[] = [ 'tokens'=> $response->usage->completionTokens + 3];
        $this->messages = $messagesToKeep;
        $this->messagesMeta = $messagesMetaToKeep;
    }

    /**
     * Stores the messages and their associated metadata in cache memory.
     *
     * If the 'enableMemory' option is not set to 'disable', this function 
     * serializes and caches both the messages and their metadata.
     * The cached items will expire based on the 'memoryPeriod' option.
     *
     * @access private
     * @return void
     */
    private function saveMessagesToMemory(){
        if (!$this->__memoryInit()){
            return false;
        }
        $seconds = $this->options['memoryPeriod'] * 60;
        Cache::put($this->memoryKey, $this->messages, $seconds);
        Cache::put($this->memoryMetakey, $this->messagesMeta, $seconds);
    }

    /**
     * Clears messages and metadata from cache memory.
     *
     * Clears the messages and their associated metadata in cache memory.
     *
     * @access private
     * @return void
     */
    private function clearMemory()
    {
        $this->messages = [];
        $this->memoryMetakey = [];
        if (!$this->__memoryInit()){
            return false;
        }
        Cache::forget($this->memoryKey);
        Cache::forget($this->memoryMetakey);
    }

    private function __memoryInit(){
        // protected $memoryKey;
        // protected $memoryMetakey;
        if ($this->options['enableMemory'] == 'disable') {return false;}
        if($this->memoryKey) { return true; }
        $sessionMD5 = md5($this->options['session']);
        $this->memoryKey = 'memory:' . $sessionMD5;
        $this->memoryMetakey = 'memoryMeta:' . $sessionMD5;
        return true;
    }
    /**
     * Retrieves messages and their associated metadata from cache memory.
     *
     * If the 'enableMemory' option is not set to 'disable', this function 
     * fetches the messages and their metadata from the cache.
     * After retrieval, it refreshes the expiration time of the cached items 
     * based on the 'memoryPeriod' option.
     * Finally, it updates the class properties with the fetched values.
     *
     * @access private
     * @return void
     */
    private function getMessagesFromMemory(){
        $this->messages = [];
        $this->memoryMetakey = [];
        if ($this->__memoryInit()){
            $seconds = $this->options['memoryPeriod'] * 60;
            
            $memoryKey_value = Cache::get($this->memoryKey);
            $memoryMetakey_value = Cache::get($this->memoryMetakey);

            Cache::put($this->memoryKey, $memoryKey_value, $seconds);
            Cache::put($this->memoryMetakey, $memoryMetakey_value, $seconds);

            $this->messages = $memoryKey_value;
            $this->memoryMetakey = $memoryMetakey_value;
        }
    }
}