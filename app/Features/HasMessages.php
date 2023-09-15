<?php

namespace App\Features;
use Yethee\Tiktoken\EncoderProvider;


trait HasMessages
{
    use HasOptions;

    protected static $encoding_name = 'cl100k_base'; // Encodings specify how text is converted into tokens https://github.com/openai/openai-cookbook/blob/main/examples/How_to_count_tokens_with_tiktoken.ipynb
    protected static $messagesOptions = [
        'systemMessage' => [
            "name" => "systemMessage",
            "type" => "text",
            "required" => false,
            "desc" => "Provides high-level directives or context for the entire conversation. Typically used at the beginning of the chat. If you wish to update or add more context during a conversation, use the 'updateSystemMessage' parameter.",
            'default' => "You are a support agent. Provide brief and accurate answers. Elaborate only when prompted. If uncertain, admit you don't know. Shift between Friendly, Youthful, Funny, and Concise styles as needed.",
            "isApiOption" => false,
        ],
        'updateSystemMessage' => [
            "name" => "updateSystemMessage",
            "type" => "text",
            "required" => false,
            "desc" => "Allows you to send additional system-level instructions during a conversation, helping to refine or redirect the model's behavior as the conversation progresses.",
            "isApiOption" => false,
            "default" => null,
        ],
        'userMessage' => [
            "name" => "userMessage",
            "type" => "string",
            "required" => true,
            "desc" => "Instruct, question, or guide the model, eliciting specific responses based on the input provided.",
            "isApiOption" => true,
            "default" => null,
        ],
        'messages' => [
            "name" => "messages",
            "type" => "json",
            "required" => false,
            "desc" => "A list of messages comprising the conversation so far. check https://platform.openai.com/docs/api-reference/chat/create#messages",
            "isApiOption" => false,
            "default" => null,
        ],
    ];

    //$this->openAIclient

    protected $messages;
    protected $messagesMeta;

    private function sendMessages($messages = [], $ChatCompletionOptions = [], $addToMessages = true){
        $response = $this->sendMessageToLLM(
            $messages || $this->messages,
            $ChatCompletionOptions || $this->ChatCompletionOptions,
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

    private function prepareMessages(){
        if($this->options['messages']) {
            $this->messages = $this->options['messages'];
            $this->options['userMessage'] = false;
            return true;
        }

        $this->getMessagesFromMemory();

        if($this->messages){
            if($this->options['updateSystemMessage']){
                $this->addMessage(['role' => 'system', 'content' => $this->options['updateSystemMessage']]);
            }

        }else{
            foreach ($this->options as $key => $value) {
                $this->options['systemMessage'] = str_replace('{{'.$key.'}}', $value, $this->options['systemMessage']);
            }
            if($this->options['systemMessage']){
                $this->addMessage(['role' => 'system', 'content' => $this->options['systemMessage']]);
            }
        }
        $this->addMessage(['role' => 'user', 'content' => $this->options['userMessage'] || "Say Hello"]);
        return true;
    }


    private function addMessage($message, $tokens = false){
        if ($this->options['enableMemory'] != 'disable' && $this->options['enableMemory'] != 'noOptimization'){
            if(!$tokens){
                $provider = new EncoderProvider();
                $encoder = $provider->get(self::$encoding_name);
                $tokens = $encoder->encode($message['content']);
                $tokens = count($tokens);
            }
            $this->messagesMeta[]['tokens'] = $tokens;
            $this->messages[] = $message;
            if(!isset($this->messagesMeta['all']['totalTokens'])) $this->messagesMeta['all']['totalTokens'] = 0;
            $this->messagesMeta['all']['totalTokens']+= $tokens;
        }else{
            $this->messages[] = $message;
            $this->messagesMeta[] = [];
        }

    }
}