<?php
namespace App\Splitters;

use Yethee\Tiktoken\EncoderProvider;

class TokenTextSplitter extends TextSplitter
{
    use HasSplitTextWithRegex;

    private $tokenizer;

    public function __construct(array $options = []) {
        $defaults = [
            'encoding_name' => 'p50k_base',
            'model_name' => null,
        ];
        $options = array_merge($defaults, $options);
        parent::__construct($options);

        $provider = new EncoderProvider();
        if ($options['model_name'] !== null) {
            $this->tokenizer = $provider->getForModel($options['model_name']);
        } else {
            $this->tokenizer = $provider->get($options['encoding_name']);
        }
    }

    private function splitTextOnTokens(string $text): array {
        $splits = [];
        $inputIds = $this->tokenizer->encode($text);
        $startIdx = 0;
        $curIdx = min($startIdx + $this->chunk_size, count($inputIds));
        $chunkIds = array_slice($inputIds, $startIdx, $curIdx);

        while ($startIdx < count($inputIds)) {
            $splits[] = $this->tokenizer->decode($chunkIds);
            $startIdx += $this->chunk_size - $this->chunk_overlap;
            $curIdx = min($startIdx + $this->chunk_size, count($inputIds));
            $chunkIds = array_slice($inputIds, $startIdx, $curIdx);
        }
        return $splits;
    }

    public function splitText($text) {
        if ($this->options['clean_text']) {
            $text = $this->cleanText($text);
        }
        if ($this->options['optimize_text']) {
            $text = $this->optimizeText($text);
        }
        return ['content'=>$this->splitTextOnTokens($text), 'extraMetadata' => $this->extraMetadata];
    }
}