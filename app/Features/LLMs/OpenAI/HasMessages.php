<?php

namespace App\Features\LLMs\OpenAI;

use Yethee\Tiktoken\EncoderProvider;


trait HasMessages
{

    /**
     * The name of the encoding to use for tokenizing text.
     *
     * @var string
     */
    protected static $encoding_name = 'cl100k_base';

    /**
     * The options for the messages in the conversation.
     *
     * @var array
     */
    protected static $messagesOptions = [
        'systemMessage' => [
            "name" => "systemMessage",
            "type" => "text",
            "required" => false,
            "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
            'default' => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
            "isApiOption" => false,
            "_group" => 'Messages',
        ],
        'updateSystemMessage' => [
            "name" => "updateSystemMessage",
            "type" => "text",
            "required" => false,
            "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
            "isApiOption" => false,
            "default" => null,
            "_group" => 'Messages',
        ],
        'userMessage' => [
            "name" => "userMessage",
            "type" => "string",
            "required" => true,
            "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
            "isApiOption" => true,
            "default" => null,
            "_group" => 'Messages',
        ],
        'messages' => [
            "name" => "messages",
            "type" => "json",
            "required" => false,
            "desc" => "A list of messages comprising the conversation so far. check https://platform.openai.com/docs/api-reference/chat/create#messages (Memory will be disabled when using this option)",
            "isApiOption" => false,
            "default" => false,
            "_group" => 'Messages',
        ],
    ];

    /**
     * The messages in the conversation.
     *
     * @var array
     */
    protected $messages;

    /**
     * Metadata about the messages in the conversation.
     *
     * @var array
     */
    protected $messagesMeta;

    /**
     * Get the messages in the conversation.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Save the messages to memory.
     *
     * @return void
     */
    private function saveMessages()
    {
        //get orginalContent back
        foreach ($this->messagesMeta as $key => $messagesMeta) {
            if(isset($messagesMeta['orginalContent'])){
                $this->messages[$key]['content'] = $messagesMeta['orginalContent'];
                $this->messagesMeta[$key]['tokens'] = $this->messagesMeta[$key]['orginalToken'];
                unset($this->messagesMeta[$key]['orginalContent']);
                unset($this->messagesMeta[$key]['orginalToken']);
            }
        }
        return $this->saveMessagesToMemory();
    }

    /**
     * Send messages to the OpenAI language model and add the responses to the conversation.
     *
     * @param array $messages
     * @param array $ChatCompletionOptions
     * @param bool $addToMessages
     * @return mixed
     */
    private function sendMessages($messages = [], $ChatCompletionOptions = [], $addToMessages = true){
        $ChatCompletionOptions = $ChatCompletionOptions ?  $ChatCompletionOptions : $this->ChatCompletionOptions;
        $messages = $messages ?  $messages : $this->messages;
        $this->debug('sendMessages()', ['$ChatCompletionOptions'=>$ChatCompletionOptions,'$messages'=>$messages]);
        $response = $this->sendMessageToLLM(
            $messages,
            $ChatCompletionOptions,
        );
        if($addToMessages){
            if(count($response->choices)>1){
                foreach ($response->choices as $result) {
                    $message = [ 
                        'role'=> $result->message->role,
                        'content'=> $result->message->content, 
                    ];
                    $this->addMessage($message);
                }
            }else{
                $message = [ 
                    'role'=> $response->choices[0]->message->role,
                    'content'=> $response->choices[0]->message->content, 
                ];
                $this->addMessage($message, $response->usage->completionTokens);
            }
        }
        
        return $response;
    }

    /**
     * Prepare the messages for sending to the OpenAI language model.
     *
     * @return bool
    */
    private function prepareMessages(){
        if($this->options['messages']) {
            $this->messages = json_decode($this->options['messages'], true);
            $this->options['userMessage'] = false;
            return true;
        }else{
            $this->getMessagesFromMemory();
        }

        if($this->messages){
            if($this->options['updateSystemMessage']){
                $this->options['updateSystemMessage'] = $this->promptTemplate($this->options['updateSystemMessage']);
                $this->addMessage(['role' => 'system', 'content' => $this->options['updateSystemMessage']]);
            }

        }else{
            if($this->options['systemMessage']){
                $this->options['systemMessage'] = $this->promptTemplate($this->options['systemMessage']);
                $this->addMessage(['role' => 'system', 'content' => $this->options['systemMessage']]);
            }
        }

        //orginalContent
        $content = isset($this->options['userMessage']) ? $this->options['userMessage'] : "Say Hello";
        $myMessage = [
            'role' => 'user',
        ];

        if($this->ApiModel->collection_id) {
            // need embedding_id from collection
            // need to make sure collection is cached
            // need to make sure collection has embedder_id or use 1
            // Embedding user message in the prompt
            // Lookup DB for chunks
            // What if we got chunks with large tokens?
            // what if we don't have chunks?
            // Add chunks with user question? 
            // What if we have chunks but no answer from openAI
            $myMessage['orginalContent'] = $content;
        }else{
            $myMessage['content'] = $content;
        }
        

        $this->addMessage($myMessage);

        $this->optimizeMemory();
        return true;
    }

    protected function promptTemplate($message){
        foreach ($this->options as $key => $value) {
            $message = str_replace('{{'.$key.'}}', $value, $message);
        }
        $message = str_replace(
            ['[current_date]', '[current_time]', '[current_datetime]'], 
            [
                date('j/M/Y'),  // Current date in format like 1/Sept/2022
                date('g:ia'),  // Current time in format like 10:26pm
                date('j/M/Y g:ia')  // Current datetime in format like 1/Sept/2022 10:26pm
            ], 
            $message
        );
        // Remove any remaining {{variable}} that were not replaced
        $message = preg_replace('/\{\{.*?\}\}/', '', $message);
        return $message;
    }
    /**
     * Count the number of tokens in a given string.
     *
     * @param string $content
     * @return int
     */
    public function countTokens($content)
    {
        $provider = new EncoderProvider();
        $provider->setVocabCache(storage_path('encoders'));
        $encoder = $provider->getForModel($this->options['model']);
        $tokens = $encoder->encode($content);
        return count($tokens);
    }

    /**
     * Add a message to the conversation.
     *
     * @param array $message
     * @param bool $tokens
     * @return void
     */
    private function addMessage($message, $tokens = false)
    {
        if ($this->options['enableMemory'] != 'disabled' && $this->options['memoryOptimization'] != 'noOptimization'){
            if(!$tokens){
                $tokens = $this->countTokens($message['content']);
            }
            $messagesMeta = [
                'tokens' => $tokens,
            ];
            if(isset($message['orginalContent'])){
                $messagesMeta['orginalContent'] = $message['orginalContent'];
                $messagesMeta['orginalToken'] = $this->countTokens($message['orginalContent']);
                unset($message['orginalContent']);
            }
            $this->messagesMeta[] = $messagesMeta;
            $this->messages[] = $message;
            if(!isset($this->messagesMeta['all']['totalTokens'])) $this->messagesMeta['all']['totalTokens'] = 0;
            $this->messagesMeta['all']['totalTokens']+= $tokens;
        }else{
            if(isset($message['orginalContent'])){ unset($message['orginalContent']); }
            $this->messages[] = $message;
            $this->messagesMeta[] = [];
        }
        $this->debug('addMessage()', ['message' => $message, 'tokens' => $tokens]);
    }
}