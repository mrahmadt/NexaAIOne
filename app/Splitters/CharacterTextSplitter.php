<?php

namespace App\Splitters;

class CharacterTextSplitter extends TextSplitter
{
    use HasSplitTextWithRegex;
    private $separator;
    private $is_separator_regex;

    public function __construct($options = [])
    {
        $defaults = [
            'separator' => "\n\n",
            'is_separator_regex' => false
        ];

        $options = array_merge($defaults, $options);

        parent::__construct($options);
        $this->separator = str_replace(['\n', '\t', '\r'], ["\n", "\t", "\r"], $options['separator']);
        $this->is_separator_regex = $options['is_separator_regex'];
    }

    public function splitText($text)
    {
        $separator = $this->is_separator_regex ? $this->separator : preg_quote($this->separator, '/');
        $splits = $this->splitTextWithRegex($text, $separator, $this->keep_separator);
        $separator = $this->keep_separator ? "" : $this->separator;
        return ['content'=>$this->mergeSplits($splits, $separator), 'extraMetadata' => $this->extraMetadata];
    }

}