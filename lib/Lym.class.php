<?php

class Lym {
    
    private static $boot_calles = false;

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
        if (self::$boot_calles) throw new \Exception("Framework boot function already called.");
        self::$boot_calles = true;
        
        ob_start();
        
        self::detectAndSaveEnvironment();
        
        LConfig::saveServerVar('FRAMEWORK_DIR');
        LOutput::framework_debug("Loading framework from : " . $_SERVER['FRAMEWORK_DIR']); 
        
        self::detectAndSaveHostnameAndRawRoute();
        LOutput::framework_debug("Execution mode : ".LExecutionMode::get());
        
        LConfig::init();
        
        self::initRoute();
        
        self::framework_start();
    }

    public static function project_boot() {
        if (self::$boot_calles) throw new \Exception("Framework boot function already called.");
        self::$boot_calles = true;
        
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
        
        self::project_start();
    }
    
    private static function handleSetExecutionMode() {
        if (!isset($_SERVER['argv'][2])) {
            LOutput::error_message("Mode name not set. Choose between 'maintenance','framework_debug','debug' or 'production'.");
            exit(1);
        }
        $mode_name = $_SERVER['argv'][2];
        try {
            LExecutionMode::setByName($mode_name);
            LOutput::message("Execution mode set to '".$mode_name."' successfully.");
            exit(0);
        } catch (\Exception $ex) {
            LOutput::exception($ex);
            exit(1);
        }
        
    }
    
    private static function handleGetExecutionMode() {
        LOutput::message("Execution mode is now '".LExecutionMode::get()."'.");
        exit(0);
    }
    
    private static function handleRunFrameworkTests() {
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], 'tests/');
        LTestRunner::run();
        exit(0);
    }
    
    private static function handleRunTests() {
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests/');
        LTestRunner::run();
        exit(0);
    }
    
    private static function handleRunTestsFast() {
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests_fast/');
        LTestRunner::run();
        exit(0);
    }
    
    private static function handleInternalFrameworkProcedures() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/run_framework_tests' : self::handleRunFrameworkTests();
        }

    }
    
    private static function handleInternalProjectProcedures() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/set_execution_mode' : self::handleSetExecutionMode();
            case 'internal/get_execution_mode' : self::handleGetExecutionMode();
            case 'internal/run_tests' : self::handleRunTests();
            case 'internal/run_tests_fast' : self::handleRunTestsFast();
        }

    }
    
    private static function framework_start() {
        self::handleInternalFrameworkProcedures();
    }

    private static function project_start() {
        self::handleInternalProjectProcedures(); //maybe exit if one is found
        
        //more to come ...
    }

}
