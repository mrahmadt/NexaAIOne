<?php

namespace App\Splitters;

trait HasSplitTextWithRegex
{
    private function splitTextWithRegex($text, $separator, $keepSeparator)
    {
        $splits = [];
    
        if ($separator) {
            if ($keepSeparator) {
                // The parentheses in the pattern keep the delimiters in the result.
                $_splits = preg_split("/($separator)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE);
                for ($i = 1; $i < count($_splits); $i += 2) {
                    $splits[] = $_splits[$i] . ($_splits[$i + 1] ?? '');
                }
                if (count($_splits) % 2 === 0) {
                    $splits[] = end($_splits);
                }
                array_unshift($splits, $_splits[0]);
            } else {
                $splits = preg_split("/$separator/", $text);
            }
        } else {
            $splits = str_split($text);
        }
    
        return array_filter($splits, function($value) {
            return $value !== '';
        });
    }
    
}