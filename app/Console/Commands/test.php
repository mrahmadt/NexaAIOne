<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Collection;
use App\Models\Embedder;
use OpenAI;

use Pgvector\Vector;
// use Pgvector\Laravel\Vector;

class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private function llm($messages, $temperature = null, $top_p = null)
    {
        $openaiApiKey = config('openai.api_key');

        $ChatCompletionOptions['model'] = 'gpt-3.5-turbo';
        if($temperature) $ChatCompletionOptions['temperature'] = 0;
        if($top_p) $ChatCompletionOptions['top_p'] = 0;
        $LLMclient = OpenAI::factory()->withApiKey($openaiApiKey)->make();
        $response = $LLMclient->chat()->create(
            array_merge(
                $ChatCompletionOptions,
                ['messages' => $messages ]
            )
        );
        return $response;
    }
    private function stream($messages, $temperature = null, $top_p = null)
    {
        $openaiApiKey = config('openai.api_key');
        $ChatCompletionOptions['model'] = 'gpt-3.5-turbo';
        if($temperature) $ChatCompletionOptions['temperature'] = 0;
        if($top_p) $ChatCompletionOptions['top_p'] = 0;
        $LLMclient = OpenAI::factory()->withApiKey($openaiApiKey)->make();
        $response = $LLMclient->chat()->createStreamed(
            array_merge(
                $ChatCompletionOptions,
                ['messages' => $messages ]
            )
        );
        return $response;
    }
    
    public function handle(){
        $messages = [
            ['role' => 'user', 'content' => 'Hello!'],
        ];
        $stream = $this->stream($messages);

        $message = [ 
            'role'=> 'assistant',
            'content'=> '', 
        ];

        foreach($stream as $response){
            if (connection_aborted()) {
                break;
            }
            if(isset($response->choices[0])){
                $message['content'] .= $response->choices[0]->delta->content;
                print_r(json_encode($response->choices[0]));
                print "\n";
            }
            // print_r($response);
            // print_r($response->choices[0]->toArray());
        }
foreach($stream as $response) {
    print "dasda";
}
        print_r($message);

        exit;
        $question = 'what are the working hours?';
        // $question = 'what are the business hours?'; // I don't know
        // $question = 'How far the moon from earth?';

        // $question = 'How many vacation days I can carry over?';
        //          +content: "According to the Leave Policy (HR P002) mentioned in the context, employees may accumulate up to a maximum of 25 days of leave. Any leave in excess of this limit at the end of the financial year will be forfeited, unless it is a result of the employer preventing the employee from taking leave and the leave has been applied for and refused in writing by the employer. (Reference: Leave Policy – HR P002, last saved on 22/7/2019, HRSimplified Online Customer Solutions staff)."

        $response = $this->process($question);
        dd($response);
    }
    public function process($question, $limit=5, $temperature = null, $top_p = null)
    {
            // need embedding_id from collection
            // need to make sure collection is cached
            // need to make sure collection has embedder_id or use 1
            // Embedding user message in the prompt
            // Lookup DB for chunks
            // What if we got chunks with large tokens?
            // what if we don't have chunks?
            // Add chunks with user question? 
            // What if we have chunks but no answer from openAI
            $collection_id = 1;

            $collection = Collection::where(['id'=>$collection_id])->first();
            $embedder = Embedder::where(['id'=> $collection->embedder_id])->first();
            $className = '\App\Embedders\\' . $embedder->className;
            $EmbedderClass = new $className($embedder->options);
            $embeds = $EmbedderClass->execute($question);
            if($embeds && isset($embeds->embeddings[0]->embedding)){
                // dd($embeds->usage->totalTokens);
                $embedding = $embeds->embeddings[0]->embedding;
                $content_tokens = $embeds->usage->totalTokens;
            }else{
                dd('erorr', $embeds);
            }

            $embeddingVector = new Vector($embedding);
            $documents = DB::table('documents')
            ->select('id', 'content', 'meta', 'content_tokens')
            ->whereRaw("collection_id=?", [$collection_id])
            ->orderByRaw("embeds <=> ?", [$embeddingVector])
            ->limit($limit)
            ->get();

            $total_tokens = 0;
            $context = null;
            foreach($documents as $document){
                $context .= "\n" . $document->content;
                $total_tokens += $document->content_tokens;
            }

            $messages = [
                [
                    'role' => 'system',
                    'content' => "You are an HR representative for a company that sells electronics online.",
                ],
                [
                    'role' => 'user',
                    'content' => "Answer the following Question based on the Context only. Only answer from the Context. When you want to refer to the context provided, call it 'HR Policy' not just 'context'. Try to provide a reference to the HR Policy number. If you don't know the answer, mention that you couldn't find the answer in the HR Policy.\nCONTEXT: " . $context . "\n\nQuestion:".$question,
                ]
            ];
            // dd($messages);
            $response = $this->llm($messages, $temperature, $top_p);
            // return ['total_tokens' => $total_tokens, 'response'=> $response, '$context'=>$context];
            return ['total_tokens' => $total_tokens, 'response'=> $response];
            
            /*

                Get the nearest neighbors to a vector
                SELECT * FROM items ORDER BY embedding <-> '[3,1,2]' LIMIT 5;

                Get the nearest neighbors to a row
                SELECT * FROM items WHERE id != 1 ORDER BY embedding <-> (SELECT embedding FROM items WHERE id = 1) LIMIT 5;

                Get rows within a certain distance
                SELECT * FROM items WHERE embedding <-> '[3,1,2]' < 5;
                Note: Combine with ORDER BY and LIMIT to use an index

                Get the distance
                SELECT embedding <-> '[3,1,2]' AS distance FROM items;

                For inner product, multiply by -1 (since <#> returns the negative inner product)
                SELECT (embedding <#> '[3,1,2]') * -1 AS inner_product FROM items;

                For cosine similarity, use 1 - cosine distance
                SELECT 1 - (embedding <=> '[3,1,2]') AS cosine_similarity FROM items;
            */


    }
}
