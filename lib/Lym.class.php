<?php

class Lym {
    
    private static $boot_called = false;
    
    private static function isBootCalled() {
        return self::$boot_called;
    }
    
    private static function setBootAsCalled() {
        self::$boot_called = true;
    }

    private static function detectAndSaveEnvironment() {
        $_SERVER['ENVIRONMENT'] = 'script';
        if (isset($_SERVER['SERVER_NAME'])) { //is a virtual host?
            $_SERVER['ENVIRONMENT'] = 'web';
        }
        
        LConfig::saveServerVar('ENVIRONMENT');
        LOutput::framework_debug("Environment detected : " . $_SERVER['ENVIRONMENT']);
    }
    
    private static function detectAndSaveHostnameAndRawRoute() {
        // hostname to detect

        $hostname = 'localhost'; //default set as localhost
        $hostname_found = false;
        
        if (isset($_SERVER['SERVER_NAME'])) { //is a virtual host?
            $hostname = $_SERVER['SERVER_NAME'];
            $hostname_found = true;
            $_SERVER['RAW_ROUTE'] = $_REQUEST['routemap'];
        }

        if (!$hostname_found && isset($_SERVER['SESSION_MANAGER'])) { //is a console window inside a window manager?
            $parts = explode(':', $_SERVER['SESSION_MANAGER']);
            $part0 = $parts[0];
            $sub_parts = explode('/', $part0);
            if (isset($sub_parts[1])) {
                $hostname = $sub_parts[1];
                $hostname_found = true;
            }
            //calcolo del valore di RAW_ROUTE prendendo l'argomento della linea di comando se il nome host non è ancora stato impostato
            if (isset($_SERVER['argv'][1])) {
                $_SERVER['RAW_ROUTE'] = $_SERVER['argv'][1];
            } else {
                LOutput::error_message("Route not found in command-line execution.");
                exit(1);
            }
        }

        if (!$hostname_found) {
            //calcolo del valore di RAW_ROUTE prendendo l'argomento della linea di comando se il nome host non è ancora stato impostato
            if (isset($_SERVER['argv'][1])) {
                $_SERVER['RAW_ROUTE'] = $_SERVER['argv'][1];
            } else {
                LOutput::error_message("Route not found in command-line execution.");
                exit(1);
            }
        }
        


        $_SERVER['HOSTNAME'] = $hostname;
        LConfig::saveServerVar('HOSTNAME');
        LOutput::framework_debug("Hostname detected : " . $_SERVER['HOSTNAME']);

        // hostname set
        LConfig::saveServerVar('RAW_ROUTE');
        LOutput::framework_debug("Raw route detected : " . $_SERVER['RAW_ROUTE']);
    }
    
    private static function initRoute() {
        
        $folder_route = LConfig::get('folder_route','index.html');

        $route = $_SERVER['RAW_ROUTE'];

        if ($route == null) {
            $route = '';
        }
        if ($route[strlen($route) - 1] == '/') {
            $route .= $folder_route;
        }

        $_SERVER['ROUTE'] = $route;
        LConfig::saveServerVar('ROUTE');
        // route set
        LOutput::framework_debug("Route detected : " . $_SERVER['ROUTE']);
    }

    public static function framework_boot() {
        if (self::isBootCalled()) throw new \Exception("Boot function already called.");
        self::setBootAsCalled();
        
        LLog::init();
        
        ob_start();
        
        self::detectAndSaveEnvironment();
        
        LConfig::saveServerVar('FRAMEWORK_DIR');
        LOutput::framework_debug("Loading framework from : " . $_SERVER['FRAMEWORK_DIR']); 
        
        self::detectAndSaveHostnameAndRawRoute();
        LOutput::framework_debug("Execution mode : ".LExecutionMode::get());
        
        LConfig::init();
        
        self::initRoute();
        
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
        
        self::detectAndSaveEnvironment();
        
        LConfig::saveServerVar('PROJECT_DIR');
        LOutput::framework_debug("Project dir detected : " . $_SERVER['PROJECT_DIR']);
        LConfig::saveServerVar('FRAMEWORK_DIR');
        LOutput::framework_debug("Loading framework from : " . $_SERVER['FRAMEWORK_DIR']);  

        self::detectAndSaveHostnameAndRawRoute();
        LOutput::framework_debug("Execution mode : ".LExecutionMode::get());

        LConfig::init();
        
        self::initRoute();
        
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

        LLog::debug("A debug message ...");
        LLog::info("An info message ...");
        LLog::warning("A warning message ...");
        LLog::error("An error message ,.,");
        LLog::fatal("A fatal message ...");
        //more to come ...
    }
    
    private static function finish() {
        ob_end_flush();
        
        LDbConnectionManager::dispose();
        
        LLog::close();
    }

}
