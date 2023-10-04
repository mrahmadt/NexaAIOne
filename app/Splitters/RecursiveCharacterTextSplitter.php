<?php
namespace App\Splitters;

class RecursiveCharacterTextSplitter extends TextSplitter
{
    use HasSplitTextWithRegex;
    private $separators;
    private $is_separator_regex;

    public function __construct($options = [])
    {
        $defaults = [
            'separators' => ["\n\n", "\n", " ", ""],
            'is_separator_regex' => false
        ];
        $options = array_merge($defaults, $options);
        parent::__construct($options);
        $this->separators = array_map(function($separator) {
            return str_replace(['\n', '\t', '\r'], ["\n", "\t", "\r"], $separator);
        }, $options['separators']);

        $this->is_separator_regex = $options['is_separator_regex'];
    }

    public function splitText($text){
        if ($this->options['clean_text']) {
            $text = $this->cleanText($text);
        }
        $content = $this->splitTextWithSeparator($text, $this->separators);
        if ($this->options['optimize_text']) {
            $content = $this->optimizeText($content);
        }
        return ['content'=>$content, 'extraMetadata' => $this->extraMetadata];
    }

    public function splitTextWithSeparator($text, $separators)
    {
        $finalChunks = [];
        $separator = end($separators);
        $newSeparators = [];

        foreach ($separators as $index => $_separator) {
            $escapedSeparator = $this->is_separator_regex ? $_separator : preg_quote($_separator, '/');
            if ($_separator === "") {
                $separator = $_separator;
                break;
            }
            if (preg_match("/$escapedSeparator/", $text)) {
                $separator = $_separator;
                $newSeparators = array_slice($separators, $index + 1);
                break;
            }
        }

        $splits = $this->splitTextWithRegex($text, $separator, $this->keep_separator);
        $goodSplits = [];
        $_separator = $this->keep_separator ? "" : $separator;
        foreach ($splits as $s) {
            if (mb_strlen($s) < $this->chunk_size) {
                $goodSplits[] = $s;
            } else {
                if (!empty($goodSplits)) {
                    $mergedText = $this->mergeSplits($goodSplits, $_separator);
                    $finalChunks = array_merge($finalChunks, $mergedText);
                    $goodSplits = [];
                }
                if (empty($newSeparators)) {
                    $finalChunks[] = $s;
                } else {
                    $otherInfo = $this->splitTextWithSeparator($s, $newSeparators);
                    $finalChunks = array_merge($finalChunks, $otherInfo);
                }
            }
        }
        if (!empty($goodSplits)) {
            $mergedText = $this->mergeSplits($goodSplits, $_separator);
            $finalChunks = array_merge($finalChunks, $mergedText);
        }
        return $finalChunks;
    }

}