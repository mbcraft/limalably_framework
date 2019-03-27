<?php

class LOutputLogger implements LILogger {
    
    private $initialized = false;
    
    public function close() {
        //nothing to do ...
    }

    public function debug($message) {
        LOutput::framework_debug("LOG Debug : ".$message);
    }

    public function error($message) {
        LOutput::message("LOG Error : ".$message);
    }

    public function exception(\Exception $ex) {
        LOutput::message("LOG Exception : ". LStringUtils::getExceptionMessage($ex));
    }

    public function fatal($message) {
        LOutput::message("LOG Fatal : ".$message);
    }

    public function info($message) {
        LOutput::debug("LOG Info : ".$message);
    }
    
    public function isInitialized() {
        return $this->initialized;
    }

    public function init() {
        LOutput::message("Initializing output logger ...");
        $this->initialized = true;
    }

    public function warning($message) {
        LOutput::message("LOG Warning : ".$message);
    }

}
