<?php
// Need Summary to be part of LLM
// What if the messages are large?
// FIX code based on new options
namespace App\Features\LLMs\OpenAI;

use Illuminate\Support\Facades\Cache;
use App\Models\Memory;

trait HasMemory
{
    /**
     * A string that represents the cache key for the conversation history.
     *
     * @var string
     */
    protected $memoryKey;

    /**
     * A string that represents the cache key for the conversation metadata.
     *
     * @var string
     */
    protected $memoryMetakey;

    /**
     * A string that represents hash of the session.
     *
     * @var string
     */
    protected $sessionHash;

    /**
     * An array that defines the options for conversation tracking.
     *
     * @var array
     */
    protected static $memoryOptions = [
        'enableMemory' => [
            "name" => "enableMemory",
            "type" => "select",
            "required" => false,
            "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations. (Short memory will be saved in Memory, Long memory will be saved in Database)",
            "default" => 'disabled',
            "isApiOption" => false,
            "options"=>[
                'disabled' => 'Disabled',
                'shortMemory' => 'Short Memory',
                'longMemory' => 'Long Memory',
            ],
            "_group" => 'Memory',
        ],
        'memoryOptimization' => [
            "name" => "memoryOptimization",
            "type" => "select",
            "required" => false,
            "desc" => "Do you want to enable conversation tracking? Turning this on will retain a record of past conversations. (Embeddings always saved in Database)",
            "default" => 'disabled',
            "isApiOption" => false,
            "options"=>[
                'noOptimization' => 'No memory optimization',
                'truncate' => 'Truncate',
                'summarization' => 'Summarization',
                // 'embeddings' => 'Embeddings',
            ],
            "_group" => 'Memory',
        ],

        'memoryPeriod' => [
            "name" => "memoryPeriod",
            "type" => "number",
            "required" => false,
            "desc" => "How long, in minutes, should the conversation be retained in memory? If no new messages are received within this duration, the conversation history will be cleared (ignored for Long Memory)",
            "default" => 60,
            "isApiOption" => false,
            "_group" => 'Memory',
        ],
        'memoryMaxTokenPercentage' => [
            "name" => "memoryMaxTokenPercentage",
            "type" => "number",
            "required" => false,
            "desc" => "Defines the threshold, as a percentage of the LLM Model's max tokens, at which memory optimization will be triggered. When memory token usage reaches this specified percentage, optimization measures specified in the memoryOptimization variable will be enacted.",
            "default" => 50,
            "isApiOption" => false,
            "_group" => 'Memory',
        ],
        'clearMemory' => [
            "name" => "clearMemory",
            "type" => "boolean",
            "required" => false,
            "desc" => "Clear memory for this session.",
            "isApiOption" => true,
            "default" => 0,
            "_group" => 'Memory',
        ],
        'session' => [
            "name" => "session",
            "type" => "string",
            "required" => false,
            "desc" => "Unique session id for this conversation.",
            "default" => "global",
            "isApiOption" => true,
            "_group" => 'General',
        ],
    ];


    private function optimizeMemory()
    {
        if (!$this->__memoryInit() || $this->options['memoryOptimization'] == 'noOptimization') {
            return false;
        }
        // Calculate % of model Max Tokens
        // TODO: do we need to reduce memory to be % below memoryMaxTokenPercentage? so we don't have to optimize memory after every message?

        $debugData = [];
        $debugData['optimizeMemory'] = true;

        $memoryMaxTokens = ($this->options['memoryMaxTokenPercentage']/100) * $this->options['_model_maxTokens'];
        
        $debugData['$memoryMaxTokens'] = $memoryMaxTokens;
        $debugData['$messagesMeta'] = $this->messagesMeta;
        $debugData['$messages'] = $this->messages;

        if ($this->messagesMeta['all']['totalTokens'] >= $memoryMaxTokens) {
            $lastMessage = array_pop($this->messages);
            $lastMessageMeta = array_pop($this->messagesMeta);
            if ($this->options['memoryOptimization'] == 'truncate') {
                $this->optimizeMemoryTruncate($memoryMaxTokens);
            } elseif ($this->options['memoryOptimization'] == 'summarization') {
                $this->optimizeMemorySummary($memoryMaxTokens);
            } elseif ($this->options['memoryOptimization'] == 'embeddings') {
                $this->optimizeMemoryEmbeddings($memoryMaxTokens);
            }
            $this->messages[] = $lastMessage;
            $this->messagesMeta[] = $lastMessageMeta;
            $this->messagesMeta['all']['totalTokens'] = 0;
            foreach($this->messagesMeta as $meta) {
                if(isset($meta['tokens'])) {
                    $this->messagesMeta['all']['totalTokens'] += $meta['tokens'];
                }
            }
            $debugData['new $messagesMeta'] = $this->messagesMeta;
            $debugData['new $messages'] = $this->messages;
        } else {
            $debugData['optimizeMemory'] = false;
        }
        $this->debug('optimizeMemory()', $debugData);
    }

    private function optimizeMemoryEmbeddings($memoryMaxTokens){
        $this->extraResponses['memoryOptimization'] = 'Embeddings';

    }

    private function optimizeMemoryTruncate($memoryMaxTokens)
    {
        $tokensCounted = 0;
        $messagesToKeep = [];
        $messagesMetaToKeep = [];
        $this->extraResponses['memoryOptimization'] = 'Truncate';

        $keepMessageInMemory = function ($index) use (&$tokensCounted, &$messagesToKeep, &$messagesMetaToKeep) {
            $messagesToKeep[] = $this->messages[$index];
            $messagesMetaToKeep[] = $this->messagesMeta[$index];
            $tokensCounted += $this->messagesMeta[$index]['tokens'];
        };

        foreach (array_reverse($this->messages, true) as $index => $message) {
            if ($message['role'] === 'system') {
                $keepMessageInMemory($index);
            }elseif (($tokensCounted + $this->messagesMeta[$index]['tokens']) < $memoryMaxTokens) {
                $keepMessageInMemory($index);
            }
        }

        $this->messages = array_reverse($messagesToKeep);
        $this->messagesMeta = array_reverse($messagesMetaToKeep);
        
    }

    private function optimizeMemorySummary($memoryMaxTokens)
    {
        $this->extraResponses['memoryOptimization'] = 'Summarization';
        $prompt_system = [ 'role'=> 'system', 'content'=> config('openai.memory_summarization_prompt') ];
        $totalWords = ($memoryMaxTokens / 4) . ' words';
        $totalTokens = $memoryMaxTokens . ' tokens';
        $prompt_system['content'] = str_replace(['{{totalTokens}}','{{totalWords}}'], [$totalTokens,$totalWords], $prompt_system['content']);
        $conversationToSummary = null;

        $tokensCounted = 0;
        $messagesToKeep = [];
        $messagesMetaToKeep = [];

        foreach($this->messages as $index => $message) {
            if($message['role'] == 'user') {
                $conversationToSummary .= 'User:' . $message['content'] . "\n";
            } elseif($message['role'] == 'assistant') {
                $conversationToSummary .= 'AI:' . $message['content'] . "\n";
            } elseif($message['role'] == 'system') {
                $messagesToKeep[] = $message;
                $messagesMetaToKeep[] = $this->messagesMeta[$index];
                $tokensCounted += $this->messagesMeta[$index]['tokens'];
            }
        }
        $prompt_user = [ 'role'=> 'user', 'content'=> $conversationToSummary ];
        $ChatCompletionOptions = [];
        $ChatCompletionOptions['model'] = config('openai.memory_summarization_model') ?? $ChatCompletionOptions['model'];
        $response = $this->sendMessageToLLM([$prompt_system, $prompt_user], $ChatCompletionOptions, true);

        $messagesToKeep[] = ['role'=>'system', 'content'=> 'Previous context:'. $response->choices[0]->message->content];
        $messagesMetaToKeep[] = [ 'tokens'=> $response->usage->completionTokens + 3];
        $this->messages = $messagesToKeep;
        $this->messagesMeta = $messagesMetaToKeep;
    }

    /**
     * Stores the messages and their associated metadata in cache memory.
     *
     * If the 'enableMemory' option is not set to 'disabled', this function
     * serializes and caches both the messages and their metadata.
     * The cached items will expire based on the 'memoryPeriod' option and short memory.
     *
     * @access private
     * @return void
     */
    private function saveMessagesToMemory()
    {
        if (!$this->__memoryInit()) {
            return false;
        }
        if($this->options['enableMemory'] == 'longMemory'){
            Memory::updateOrCreate(
                ['api_id'=>$this->api_id, 'sessionHash'=>$this->sessionHash],
                ['messages' => $this->messages, 'messagesMeta' => $this->messagesMeta]
            );
        }else{
            $seconds = $this->options['memoryPeriod'] * 60;
            Cache::put($this->memoryKey, $this->messages, $seconds);
            Cache::put($this->memoryMetakey, $this->messagesMeta, $seconds);
        }
        $this->debug('saveMessagesToMemory()', ['messages' => $this->messages, 'messagesMeta' => $this->messagesMeta]);
        return true;
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
        if (!$this->__memoryInit()) {
            return false;
        }
        if($this->options['enableMemory'] == 'longMemory'){
            Memory::where(['api_id'=>$this->api_id, 'sessionHash'=>$this->sessionHash])->delete();
        }else{
            Cache::forget($this->memoryKey);
            Cache::forget($this->memoryMetakey);
        }
        $this->debug('clearMemory()', true);
        return true;
    }

    /**
     * Initializes the conversation history cache.
     *
     * @param array $options
     * @return void
     */
    private function __memoryInit()
    {
        if ($this->options['enableMemory'] == 'disabled') {
            return false;
        }
        if($this->memoryKey) {
            return true;
        }
        $this->sessionHash = md5($this->options['session']);
        $this->memoryKey = 'memory:'. $this->api_id. ':'. $this->sessionHash;
        $this->memoryMetakey = 'memoryMeta:' . $this->api_id. ':'. $this->sessionHash;

        $this->debug('__memoryInit()', ['memoryKey' => $this->memoryKey, 'memoryMetakey' => $this->memoryMetakey]);
        return true;
    }
    /**
     * Retrieves messages and their associated metadata from cache memory.
     *
     * If the 'enableMemory' option is not set to 'disabled', this function
     * fetches the messages and their metadata from the cache.
     * After retrieval, it refreshes the expiration time of the cached items
     * based on the 'memoryPeriod' option if short memory.
     * Finally, it updates the class properties with the fetched values.
     *
     * @access private
     * @return void
     */
    private function getMessagesFromMemory()
    {
        $this->messages = [];
        $this->messagesMeta = [];
        $memoryKey_value = [];
        $memoryMetakey_value = [];
        if (!$this->__memoryInit()) { return false; }

        if($this->options['enableMemory'] == 'longMemory'){
            $memory = Memory::where(['api_id'=>$this->api_id, 'sessionHash'=>$this->sessionHash])->first();
            if($memory){
                $memoryKey_value = $memory->messages;
                $memoryMetakey_value = $memory->messagesMeta;
            }
        }else{
            $seconds = $this->options['memoryPeriod'] * 60;
            $memoryKey_value = Cache::get($this->memoryKey);
            $memoryMetakey_value = Cache::get($this->memoryMetakey);
            Cache::put($this->memoryKey, $memoryKey_value, $seconds);
            Cache::put($this->memoryMetakey, $memoryMetakey_value, $seconds);
        }
        if($memoryKey_value) $this->messages = $memoryKey_value;
        if($memoryMetakey_value) $this->messagesMeta = $memoryMetakey_value;
        $this->debug('getMessagesFromMemory()', ['messages' => $this->messages, 'messagesMeta' => $this->messagesMeta]);
    }
}
