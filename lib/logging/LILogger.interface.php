<?php

interface LILogger {
    
    function init();
    
    function debug($message);
    
    function info($message);
    
    function warning($message);
    
    function error($message);
    
    function exception(\Exception $ex);
    
    function fatal($message);
    
    function close();
}