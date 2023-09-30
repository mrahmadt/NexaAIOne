<?php
namespace App\Loaders;

use Illuminate\Support\Facades\Http;

class Generic {
    protected $options = [];
    
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function execute($source){
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            return $this->getUrl($source);
        } elseif (file_exists($source)) {
            return $this->getFile($source);
        }
            return false;
    }

    protected function getUrl($source){
        try{
            $headers = [];
            if(isset($this->options['headers'])) $headers = array_merge($headers, $this->options['headers']);
            $response = Http::withHeaders($headers)->get($source);
            // Throw an exception if a client or server error occurred...
            $response->throw();
            $content = $response->body();
            $content = $this->processFile($content, $source);
            return [ 'content' => $content ] ;
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    protected function getFile($source){
        try{
            $content = file_get_contents($source);
            $content = $this->processFile($content, $source);
            return [ 'content' => $content ] ;
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    protected function processFile($content, $source) {
        // Check the file extension
        $fileExtension = pathinfo($source, PATHINFO_EXTENSION);
        if ($fileExtension === 'csv') {
            return $this->PhpSpreadsheet($source);
        }elseif ($fileExtension === 'pdf') {
            return $this->pdf2Text($source);    
        } else {
            // Check MIME type for text files
            $finfo = new \finfo(FILEINFO_MIME);
            $mimeType = $finfo->buffer($content);
            if (strpos($mimeType, 'application/pdf') === 0) {
                return $this->pdf2Text($source);
            }elseif (strpos($mimeType, 'text') === 0) {
                return $content;
            } else {
                return $this->PhpSpreadsheet($source);
            }
        }

        return $content;
    }
    
    protected function pdf2Text($source) {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($source);
        return $pdf->getText();
    }
    protected function PhpSpreadsheet($source) {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($source);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($source);
        $sheetCount = $spreadsheet->getSheetCount();
        $content = null;
        for($i = 0; $i< $sheetCount; $i++){
            $dataArray = $spreadsheet->getSheet($i)->toArray(
            NULL,        // Value that should be returned for empty cells
            TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
            FALSE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
            FALSE,         // Should the array be indexed by cell row and cell column
            FALSE         // Return values for rows/columns even if they are defined as hidden.
            );
            foreach ($dataArray as $innerArray) {
                $line = implode(' ', $innerArray);
                if($line) $content .= $line . PHP_EOL;
            }
        }
        return $content;
    }

    protected function isTextContent($content) {
        $finfo = new \finfo(FILEINFO_MIME);
        $mimeType = $finfo->buffer($content);
        // Check if MIME type begins with 'text'
        return strpos($mimeType, 'text') === 0;
    }
}