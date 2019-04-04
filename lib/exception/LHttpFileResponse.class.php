<?php

class LHttpFileResponse extends LHttpResponse {
    
    private $file_full_path;
    private $file_name;
    private $inline;
    private $mime_type;
    
    function __construct (string $file_full_path,string $file_name,bool $inline,string $mime_type = null) {
        
        if (!file_exists($file_full_path)) throw new \Exception("Unable to find file for download at path : ".$file_full_path);
        
        $this->file_full_path = $file_full_path;
        $this->file_name = $file_name;
        $this->inline = $inline;
        if (!$mime_type) {
            $this->mime_type = mime_content_type ($this->file_full_path);
        } else {
            $this->mime_type = $mime_type;
        }
        parent::__construct();
    }
    
    function execute() {
        
        header('Content-Description: File Transfer');
        header('Content-Type: '.$this->mime_type);
        
        $content_disposition = $this->inline ? 'inline' : 'attachment';
        
        header('Content-Disposition: '.$content_disposition.'; filename="'.$this->file_name.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($this->file_full_path));
        header('Connection: close');
        flush(); // Flush system output buffer
        readfile($this->file_full_path);
        exit;
    }
    
    
}
