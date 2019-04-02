<?php

class LConfig {

    use LStaticTreeMapBase;
    use LStaticTreeMapRead;

    private static $init_called = false;
    private static $php_config_found = false;
    private static $json_config_found = false;

    public static function phpConfigFound() {
        return self::$php_config_found;
    }

    public static function jsonConfigFound() {
        return self::$json_config_found;
    }

    public static function saveServerVar($var_name) {
        self::setupIfNeeded();

        if (!self::is_set($var_name)) {
            self::$tree_map->set($var_name, $_SERVER[$var_name]);
            LResult::framework_debug('Server var ' . $var_name . ' persisted into configuration ...');
        }
    }

    private static function detectAndSaveDirs() {
        if (isset($_SERVER['PROJECT_DIR'])) {
            LConfig::saveServerVar('PROJECT_DIR');
            LResult::framework_debug("Project dir detected : " . $_SERVER['PROJECT_DIR']);
        }
        LConfig::saveServerVar('FRAMEWORK_DIR');
        LResult::framework_debug("Loading framework from : " . $_SERVER['FRAMEWORK_DIR']);
    }

    private static function detectAndSaveEnvironment() {
        $_SERVER['ENVIRONMENT'] = 'script';
        if (isset($_SERVER['SERVER_NAME'])) { //is a virtual host?
            $_SERVER['ENVIRONMENT'] = 'web';
        }

        LConfig::saveServerVar('ENVIRONMENT');
        LResult::framework_debug("Environment detected : " . $_SERVER['ENVIRONMENT']);
    }

    private static function detectAndSaveHostnameRawRouteAndParameters() {
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

                $parameters = $_SERVER['argv'];
                array_shift($parameters);
                array_shift($parameters);

                $_SERVER['PARAMETERS'] = $parameters;

                LConfig::saveServerVar('PARAMETERS');
            } else {
                LResult::error_message("Route not found in command-line execution.");
                exit(1);
            }
        }

        if (!$hostname_found) {
            //calcolo del valore di RAW_ROUTE prendendo l'argomento della linea di comando se il nome host non è ancora stato impostato
            if (isset($_SERVER['argv'][1])) {
                $_SERVER['RAW_ROUTE'] = $_SERVER['argv'][1];

                $parameters = $_SERVER['argv'];
                array_shift($parameters);
                array_shift($parameters);

                $_SERVER['PARAMETERS'] = $parameters;

                LConfig::saveServerVar('PARAMETERS');
            } else {
                LResult::error_message("Route not found in command-line execution.");
                exit(1);
            }
        }



        $_SERVER['HOSTNAME'] = $hostname;
        LConfig::saveServerVar('HOSTNAME');
        LResult::framework_debug("Hostname detected : " . $_SERVER['HOSTNAME']);

        // hostname set
        LConfig::saveServerVar('RAW_ROUTE');
        LResult::framework_debug("Raw route detected : " . $_SERVER['RAW_ROUTE']);
    }

    private static function initRoute() {

        $folder_route = LConfig::get('folder_route', 'index.html');

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
        LResult::framework_debug("Route detected : " . $_SERVER['ROUTE']);
    }

    private static function initFromConfigFiles() {
        if (isset($_SERVER['PROJECT_DIR'])) {
            $path_parts = [];
            $path_parts[] = 'config';
            $path_parts[] = 'internal';
            $path_parts[] = 'framework.json';
            $internal_config_file_path = $_SERVER['PROJECT_DIR'] . implode('/', $path_parts);
        } else {
            $path_parts = [];
            $path_parts[] = 'project_image';
            $path_parts[] = 'config';
            $path_parts[] = 'internal';
            $path_parts[] = 'framework.json';
            $internal_config_file_path = $_SERVER['FRAMEWORK_DIR'] . implode('/', $path_parts);
        }
        if (!is_file($internal_config_file_path)) {
            LResult::error_message("Internal config not found : " . $internal_config_file_path);
            exit(1);
        }

        try {
            $internal_json_config = json_decode(file_get_contents($internal_config_file_path), true);
            if (empty($internal_json_config))
                throw new \Exception("Empty internal config found or error in json decoding ...");
        } catch (\Exception $ex) {
            LResult::error_message("Errore nella lettura del file di configurazione interna " . $internal_config_file_path . " ...");
            LResult::exception($ex);
            exit(1);
        }

        // loading config ...

        if (isset($_SERVER['PROJECT_DIR'])) {

            $path_parts = [];
            $path_parts[] = 'config';
            $path_parts[] = 'hostnames';
            $path_parts[] = $_SERVER['HOSTNAME'];

            $config_dir_path = $_SERVER['PROJECT_DIR'] . implode('/', $path_parts) . '/';

            if (!is_dir($config_dir_path)) {
                LResult::error_message("Config dir not found : " . $config_dir_path);
                exit(1);
            } else {
                // config dir found
                LResult::framework_debug("Config dir found : " . $config_dir_path);
            }

            if (is_file($config_dir_path . 'config.php')) {
                self::$php_config_found = true;
                $php_config = include_once($config_dir_path . 'config.php');
            } else {
                $php_config = [];
            }

            if (is_file($config_dir_path . 'config.json')) {
                self::$json_config_found = true;
                try {
                    $json_config = json_decode(file_get_contents($config_dir_path . 'config.json'), true);
                    if (empty($json_config))
                        throw new \Exception("Empty config found or error in json decoding ...");
                } catch (\Exception $ex) {
                    LResult::error_message("Errore nella lettura del file di configurazione " . $config_dir_path . "config.json ...");
                    LResult::exception($ex);
                    exit(1);
                }
            }

            $all_config = array_replace_recursive($internal_json_config, $php_config);

            $all_config = array_replace_recursive($all_config, $json_config);

            $final_data = array_replace_recursive($all_config, self::get('/'));
        } else {
            $final_data = array_replace_recursive($internal_json_config, self::get('/'));
        }

        self::$tree_map->setRoot($final_data);

        // config loaded
        $message = "Config loaded ...";
        if (self::phpConfigFound())
            $message .= '/config/hostnames/' . $_SERVER['HOSTNAME'] . '/config.php';
        if (self::phpConfigFound() && self::jsonConfigFound())
            $message .= ' + ';
        if (self::jsonConfigFound())
            $message .= '/config/hostnames/' . $_SERVER['HOSTNAME'] . '/config.json';
        LResult::framework_debug($message);
    }

    public static function init() {

        if (self::$init_called) {
            throw new \Exception('Error : LConfig::init() called more than one time ...');
        }
        self::$init_called = true;

        // setting other variables
        self::detectAndSaveEnvironment();

        self::detectAndSaveDirs();

        self::detectAndSaveHostnameRawRouteAndParameters();
        
        self::initRoute();
        
        LResult::framework_debug("Execution mode : " . LExecutionMode::get());

        // loading internal config ...

        self::initFromConfigFiles();

    }

}
