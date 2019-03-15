<?php

class LOutput {
    
    private static function newline() {
        echo LStringUtils::getNewlineString();
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
    static function message($message,bool $new_line_after=true) {
        echo $message;
        if ($new_line_after) {
            self::newline();
        }
    }
        
    /**
     * Same behaviour as error_message, hides output only in production, but still saves error on logs.
     * 
     * @param \Exception $ex
     */
    static function exception(\Exception $ex,bool $print_stack_trace = true) {
        echo LStringUtils::getExceptionMessage($ex, $print_stack_trace);
    }

}
