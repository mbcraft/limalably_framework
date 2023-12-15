<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

if (!class_exists('DIOException')) {
    class DIOException extends \Exception
    {
        function  __construct($message, $code=null, $previous=null) {
            parent::__construct($message);
        }
    }
}

?>