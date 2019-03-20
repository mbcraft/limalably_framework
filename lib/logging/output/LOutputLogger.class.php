<?php

class LOutputLogger implements LILogger {
    
    public function close() {
        LOutput::message("Closing output logger ...");
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

    public function init() {
        LOutput::message("Initializing output logger ...");
    }

    public function warning($message) {
        LOutput::message("LOG Warning : ".$message);
    }

}
