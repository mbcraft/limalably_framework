<?php

class LResult {
    
    private static $has_error = false;
    
    private static function newline() {
        echo LStringUtils::getNewlineString();
    }

    /**
     * Writes a message, only if mode is at least framework development.
     * Never redirects to logs.
     * 
     * @param string $message
     */
    public static function trace(string $message) {
        if (LConfigReader::executionMode('/misc/trace_enabled')) {
            echo $message;
            self::newline();
        }
    }

    /**
     * Writes a message, only if mode is at least development. (framework development is ok).
     * Never redirects to log.
     * 
     * @param string $message
     */
    public static function debug(string $message) {
        if (LConfigReader::executionMode('/misc/debug_enabled')) {
            echo $message;
            self::newline();
        }
    }
    
    /**
     * Output is always shown. Appends 'newline' based on environment. Never redirects to log.
     * Usually appends newline if not specified with the second parameter as false.
     * 
     * @param string $message The message to be printed.
     * @param bool $new_line_after true if newline must be appended, false otherwise. Defaults to true,
     */
    public static function message(string $message,bool $new_line_after=true) {
        echo $message;
        if ($new_line_after) {
            self::newline();
        }
    }
       
    /**
     * Same behaviour as exception, hides output only in production, but still saves error on logs.
     * Appends 'newline' based on environment.
     * 
     * @param string $message
     */
    public static function error_message(string $message) {
        self::$has_error = true;
        
        if (LExecutionMode::displayErrors()) {
            echo $message;
            self::newline();
        } 
        if (LExecutionMode::logErrors())
        {
            LLog::error($message);
        }
    }
    
    /**
     * Same behaviour as error_message, hides output only in production, but still saves error on logs.
     * 
     * @param \Exception $ex The exception to print.
     */
    public static function exception(\Exception $ex,bool $print_stack_trace = true) {
        self::$has_error = true;
        
        if (LExecutionMode::displayErrors()) {
            $use_newline = $_SERVER['ENVIRONMENT'] == 'script';
            echo LStringUtils::getExceptionMessage($ex, $print_stack_trace, $use_newline);
        } 
        if (LExecutionMode::logErrors())
        {
            LLog::exception($ex);
        }
    }
    
    public static function hasError() {
        return self::$has_error;
    }

}
