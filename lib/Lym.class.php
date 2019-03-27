<?php

class Lym {
    
    private static $boot_called = false;
    
    private static function isBootCalled() {
        return self::$boot_called;
    }
    
    private static function setBootAsCalled() {
        self::$boot_called = true;
    }

    public static function framework_boot() {
        if (self::isBootCalled()) throw new \Exception("Boot function already called.");
        self::setBootAsCalled();
        
        LLog::init();
        
        ob_start();
                
        try {
            self::framework_start();
        } catch (\Exception $ex) {
            LOutput::exception($ex);
        }
        self::finish();
    }

    public static function project_boot() {
        if (self::isBootCalled()) throw new \Exception("Boot function already called.");
        self::setBootAsCalled();
        
        LLog::init();
        
        ob_start();
        
        $executor = new LProjectCommandExecutor();
        $executor->tryExecuteCommand();
        if (!$executor->hasExecutedCommand()) {
        
            try {
                self::project_start();
            } catch (\Exception $ex) {
                LOutput::exception($ex);
            }
        }
        self::finish();
    }
        
    private static function framework_start() {
        
        $executor = new LFrameworkCommandExecutor();
        $executor->tryExecuteCommand();
        
    }

    private static function project_start() {        
        
        $urlmap_resolver = new LUrlMapResolver();
        $urlmap = $urlmap_resolver->resolveUrlMap($_SERVER['ROUTE']);
        
        var_dump($urlmap);
        //more to come ...
    }
    
    private static function finish() {
        ob_end_flush();
        
        LDbConnectionManager::dispose();
        
        LLog::close();
    }

}
