<?php

require_once   "vendor/autoload.php";

require_once "app/Splitters/TextSplitter.php";
require_once "app/Splitters/HassplitTextWithRegex.php";
require_once "app/Splitters/CharacterTextSplitter.php";
require_once "app/Splitters/TokenTextSplitter.php";

use App\Splitters\TextSplitter;
use App\Splitters\HasSplitTextWithRegex;
use App\Splitters\CharacterTextSplitter;
use App\Splitters\TokenTextSplitter;

$text = file_get_contents('app/examples/state_of_the_union.txt');
// $myClass = new RecursiveCharacterTextSplitter(['chunk_size' => 100,'chunk_overlap' => 20]);
// $myClass = new CharacterTextSplitter(['separator' => "\n\n",'chunk_size' => 1000,'chunk_overlap' => 200]);
$myClass = new TokenTextSplitter(['chunk_size' => 100,'chunk_overlap' => 0]);

$chunks = $myClass->splitText($text);
print_r($chunks['content'][0]);
// print_r("\"" . $chunks[0] . "\"");
// print "\n---------------\n"; 
// print_r("\"" . $chunks[1] . "\"");
print "\n"; 