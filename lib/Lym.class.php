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
        if (self::isBootCalled())
            throw new \Exception("Boot function already called.");
        self::setBootAsCalled();

        LLog::init();

        try {
            self::framework_start();
        } catch (\Exception $ex) {
            LResult::exception($ex);
        }
        self::finish();
    }

    public static function project_boot() {
        if (self::isBootCalled())
            throw new \Exception("Boot function already called.");
        self::setBootAsCalled();

        LLog::init();

        $executor = new LProjectCommandExecutor();
        $executor->tryExecuteCommand();
        if (!$executor->hasExecutedCommand()) {

            try {
                self::project_start();
            } catch (\Exception $ex) {
                LResult::exception($ex);
            }
        }
        self::finish();
    }

    private static function framework_start() {

        $executor = new LFrameworkCommandExecutor();
        $executor->tryExecuteCommand();
    }

    private static function project_start() {
       
        $request_handler_class_name = LConfigReader::executionMode('/request/route_handler_class');
        //more to come ...
        $request_handler = new $request_handler_class_name();
        
        $request_handler->tryExecuteCommand();

    }

    public static function finish() {

        LDbConnectionManager::dispose();

        LLog::close();

        exit();
    }

}
