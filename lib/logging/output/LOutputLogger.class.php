<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LResultLogger implements LILogger {
    
    private $initialized = false;
    
    public function close() {
        //nothing to do ...
    }

    public function debug($message) {
        LResult::trace("LOG Debug : ".$message);
    }

    public function error($message,$code = '') {
        LResult::messagenl("LOG Error : ".$message);
    }

    public function exception(\Exception $ex) {
        LResult::messagenl("LOG Exception : ". LStringUtils::getExceptionMessage($ex));
    }

    public function fatal($message) {
        LResult::messagenl("LOG Fatal : ".$message);
    }

    public function info($message) {
        LResult::debug("LOG Info : ".$message);
    }
    
    public function isInitialized() {
        return $this->initialized;
    }

    public function init() {
        LResult::messagenl("Initializing output logger ...");
        $this->initialized = true;
    }

    public function warning($message) {
        LResult::messagenl("LOG Warning : ".$message);
    }

}
