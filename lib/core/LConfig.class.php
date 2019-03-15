<?php

class LConfig {

    use LStaticHashMapBase;
    use LStaticReadHashMap;

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
        if (!isset(self::$data[$var_name])) {
            self::$data[$var_name] = $_SERVER[$var_name];
            LOutput::framework_debug('Server var ' . $var_name . ' persisted into configuration ...');
        }
    }

    public static function init() {

        if (self::$init_called) {
            throw new \Exception('Error : LConfig::init() called more than one time ...');
        }
        self::$init_called = true;

        // loading internal config ...



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
            LOutput::error_message("Internal config not found : " . $internal_config_file_path);
            exit(1);
        }

        try {
            $internal_json_config = json_decode(file_get_contents($internal_config_file_path), true);
            if (empty($internal_json_config))
                throw new \Exception("Empty internal config found or error in json decoding ...");
        } catch (\Exception $ex) {
            LOutput::error_message("Errore nella lettura del file di configurazione interna " . $internal_config_file_path . " ...");
            Loutput::exception($ex);
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
                LOutput::error_message("Config dir not found : " . $config_dir_path);
                exit(1);
            } else {
                // config dir found
                LOutput::framework_debug("Config dir found : " . $config_dir_path);
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
                    LOutput::error_message("Errore nella lettura del file di configurazione " . $config_dir_path . "config.json ...");
                    Loutput::exception($ex);
                    exit(1);
                }

            }
            
            $all_config = array_replace_recursive($internal_json_config,$php_config);

            $all_config = array_replace_recursive($all_config, $json_config);

            self::$data = array_replace_recursive($all_config, self::$data);

        } else {
            self::$data = array_replace_recursive($internal_json_config,self::$data);
        }
        
        // config loaded
        $message = "Config loaded ...";
        if (self::phpConfigFound())
            $message .= '/config/hostnames/' . $_SERVER['HOSTNAME'] . '/config.php';
        if (self::phpConfigFound() && self::jsonConfigFound())
            $message .= ' + ';
        if (self::jsonConfigFound())
            $message .= '/config/hostnames/' . $_SERVER['HOSTNAME'] . '/config.json';
        Loutput::framework_debug($message);
    }
    
    
    /*
    //to be removed
    static function dump() {
        var_dump(self::$data);
    } 
    */

}
