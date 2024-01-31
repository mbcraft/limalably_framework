<?php


if (!class_exists('LDIOException')) {
    class LDIOException extends \Exception
    {
        function  __construct($message, $code=null, $previous=null) {
            parent::__construct($message);
        }
    }
}