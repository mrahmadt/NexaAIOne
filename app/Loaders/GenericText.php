<?php

use Illuminate\Support\Facades\Http;

class GenericText {
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
            if (!$this->isTextContent($content)) {
                return false;
            }
            return [ 'content' => $content ] ;
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    protected function getFile($source){
        try{
            $content = file_get_contents($source);
            if (!$this->isTextContent($content)) {
                return false;
            }
            return [ 'content' => $content ] ;
        } catch (\Exception $e) {
            return false;
        }
        return false;
    }

    protected function isTextContent($content) {
        $finfo = new finfo(FILEINFO_MIME);
        $mimeType = $finfo->buffer($content);
    
        // Check if MIME type begins with 'text'
        return strpos($mimeType, 'text') === 0;
    }
}