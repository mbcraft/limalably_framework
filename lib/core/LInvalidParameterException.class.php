<?php


class LInvalidParameterException extends \Exception
{
    function __construct($message,$code = null,$previous = null)
    {
        parent::__construct($message);
    }
}

