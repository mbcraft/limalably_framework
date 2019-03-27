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
        
        LLog::initWithConfig();
        
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
        
            LLog::initWithConfig();

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
       
        $obj =  new Prova\Qualcosa();
        echo "funzione prova : ".$obj->prova()."\n";
        
        $env = new Twig\Loader\FilesystemLoader(['template/'],$_SERVER['PROJECT_DIR']);
        
        //more to come ...
    }
    
    private static function finish() {
        ob_end_flush();
        
        LDbConnectionManager::dispose();
        
        LLog::close();
    }

}
