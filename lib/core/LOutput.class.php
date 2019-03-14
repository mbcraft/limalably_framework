<?php

class LOutput {

    private static function newline() {
        if ($_SERVER['ENVIRONMENT'] == 'script')
            echo "\n";
        else
            echo '<br>';
    }

    static function framework_debug($message) {
        if (LExecutionMode::isFrameworkDevelopment()) {
            echo $message;
            self::newline();
        }
    }

    static function debug($message) {
        if (LExecutionMode::isDevelopment()) {
            echo $message;
            self::newline();
        }
    }

    /**
     * Same behaviour as exception, hides output only in production, but still saves error on logs.
     * Appends 'newline' based on environment.
     * 
     * @param type $message
     */
    static function error_message($message) {
        echo $message;
        self::newline();
    }
    
    /**
     * Output is always shown. Appends 'newline' based on environment.
     * 
     * @param type $message
     */
    static function message($message) {
        echo $message;
        self::newline();
    }
    
    /**
     * Output is always shown. Does not append newline.
     * 
     * @param type $text
     */
    static function raw_output($text) {
        echo $text;
    }
    
    /**
     * Same behaviour as error_message, hides output only in production, but still saves error on logs.
     * 
     * @param \Exception $ex
     */
    static function exception(\Exception $ex,bool $print_stack_trace = true) {
        echo 'Exception : '.$ex->getMessage()."\n";
        echo 'File : '.$ex->getFile().' Line : '.$ex->getLine()."\n";
        if ($print_stack_trace) {
            echo 'Stack Trace : '.$ex->getTraceAsString();
            if ($ex->getPrevious()) self::exception ($ex->getPrevious ());
        }
    }

}
