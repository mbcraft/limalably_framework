<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LIOException extends \Exception
{
    function  __construct($message, $code=null, $previous=null) {
        parent::__construct($message);
    }
}