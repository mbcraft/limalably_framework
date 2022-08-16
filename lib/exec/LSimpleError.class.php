<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LSimpleError {
    
    const SIMPLE_ERROR_CODE = -1;
    
    private $message;
    
    function __construct($message,$code = self::SIMPLE_ERROR_CODE) {
        $this->message = $message;
        if ($code>=0) throw new \Exception("It is not possibile to use simple errors with a positive code number, use negative code numbers.");
        $this->code = $code;
    }
    
    public function getMessage() {
        return $this->message;
    }
    
    public function getCode() {
        return $this->code;
    }
        
}
