<?php

interface LILogWriter {
    
    const LEVEL_DEBUG = 1;
    const LEVEL_INFO = 2;
    const LEVEL_WARNING = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_FATAL = 5;
    
    const LOG_MODES_ARRAY = [self::MODE_NORMAL,self::MODE_RESET,self::MODE_ROLLING];
    
    const MODE_NORMAL = 'normal';
    const MODE_RESET = 'reset';
    const MODE_ROLLING = 'rolling';
    
    function init();
    
    function write($message, $level);
    
    function close();
    
}