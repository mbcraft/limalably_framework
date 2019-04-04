<?php

class LResultLogger implements LILogger {
    
    private $initialized = false;
    
    public function close() {
        //nothing to do ...
    }

    public function debug($message) {
        LResult::framework_debug("LOG Debug : ".$message);
    }

    public function error($message,$code = '') {
        LResult::message("LOG Error : ".$message);
    }

    public function exception(\Exception $ex) {
        LResult::message("LOG Exception : ". LStringUtils::getExceptionMessage($ex));
    }

    public function fatal($message) {
        LResult::message("LOG Fatal : ".$message);
    }

    public function info($message) {
        LResult::debug("LOG Info : ".$message);
    }
    
    public function isInitialized() {
        return $this->initialized;
    }

    public function init() {
        LResult::message("Initializing output logger ...");
        $this->initialized = true;
    }

    public function warning($message) {
        LResult::message("LOG Warning : ".$message);
    }

}
