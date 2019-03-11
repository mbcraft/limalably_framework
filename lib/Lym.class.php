<?php

class Lym {

    private static function detectAndSaveHostnameEnvironmentAndRawRoute() {
        // hostname to detect

        $hostname = 'localhost'; //default set as localhost
        $hostname_found = false;
        $_SERVER['ENVIRONMENT'] = 'script';

        if (isset($_SERVER['SERVER_NAME'])) { //is a virtual host?
            $hostname = $_SERVER['SERVER_NAME'];
            $hostname_found = true;
            $_SERVER['RAW_ROUTE'] = $_REQUEST['routemap'];
            $_SERVER['ENVIRONMENT'] = 'web';
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
                echo "Route not found in command-line execution.\n";
                exit(1);
            }
        }

        if (!$hostname_found) {
            //calcolo del valore di RAW_ROUTE prendendo l'argomento della linea di comando se il nome host non è ancora stato impostato
            if (isset($_SERVER['argv'][1])) {
                $_SERVER['RAW_ROUTE'] = $_SERVER['argv'][1];
            } else {
                echo "Route not found in command-line execution.\n";
                exit(1);
            }
        }
        
        LConfig::saveServerVar('ENVIRONMENT');
        LOutput::framework_debug("Environment detected : " . $_SERVER['ENVIRONMENT']);

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


    public static function boot() {
        
        ob_start();

        LOutput::framework_debug("Execution mode : framework_debug");
        LConfig::saveServerVar('FRAMEWORK_DIR');
        LOutput::framework_debug("Loading framework from : " . $_SERVER['FRAMEWORK_DIR']);  
        LConfig::saveServerVar('PROJECT_DIR');
        LOutput::framework_debug("Project dir detected : " . $_SERVER['PROJECT_DIR']);
        self::detectAndSaveHostnameEnvironmentAndRawRoute();

        LConfig::init();

        self::initRoute();
        
        self::start();
    }

    private static function start() {
        $route = $_SERVER['ROUTE'];
        if ($route == 'internal/set_execution_mode') {
            
        }
        if ($route == 'internal/run_framework_tests') {
            LTestRunner::clear();
            LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], 'tests/');
            LTestRunner::run();
            exit(0);
        }
        if ($route == 'internal/run_tests') {
            LTestRunner::clear();
            LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests/');
            LTestRunner::run();
            exit(0);
        }
        if ($route == 'internal/run_tests_fast') {
            LTestRunner::clear();
            LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests_fast/');
            LTestRunner::run();
            exit(0);
        }
    }

}
