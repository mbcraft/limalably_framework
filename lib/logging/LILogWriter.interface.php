<?php

interface LILogWriter {
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_FATAL = 5;
        
    function init();
    
    function write($message, $level);
    
    function close();
    
}