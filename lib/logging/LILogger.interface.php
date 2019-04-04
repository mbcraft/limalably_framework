<?php

interface LILogger {
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_FATAL = 5;
    
    function isInitialized();
    
    function init();
    
    function debug($message);
    
    function info($message);
    
    function warning($message);
    
    function error($message,$code = '');
    
    function exception(\Exception $ex);
    
    function fatal($message);
    
    function close();
}