<?php

class LConfig {
    
    private static $data = [];
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
            LOutput::framework_debug('Server var '.$var_name.' persisted into configuration ...');
        }
    }
        
    public static function init() {
        
        if (self::$data !== null) {
            LOutput::framework_debug('Error : LConfig::init called more than one time ...');
            return;
        }
        
        // loading config ...

        $path_parts[] = 'config';
        $path_parts[] = 'hostnames';
        $path_parts[] = $_SERVER['HOSTNAME'];

        $config_dir_path = $_SERVER['PROJECT_DIR'].implode('/', $path_parts) . '/';

        if (!is_dir($config_dir_path)) {
            LOutput::output("Config dir not found : " . $config_dir_path);
            exit(1);
        } else {
            // config dir found
            LOutput::framework_debug("Config dir found : ".$config_dir_path);    
        }

        if (is_file($config_dir_path . 'config.php')) {
            self::$php_config_found = true;
            require_once($config_dir_path . 'config.php');
        } else {
            $php_config = [];
        }

        if (is_file($config_dir_path . 'config.json')) {
            self::$json_config_found = true;
            try {
                $json_content = json_decode(file_get_contents($config_dir_path . 'config.json'), true);
                if (empty($json_content))
                    throw new \Exception("Empty config found or error in json decoding ...");
            } catch (\Exception $ex) {
                echo "Errore nella lettura del file di configurazione " . $config_dir_path . "config.json ...\n";
                echo $ex->getMessage();
                exit(1);
            }

            $all_config = array_replace_recursive($json_content,$php_config);
            
            self::$data = array_replace_recursive($all_config,self::$data);
            
            // config loaded
            $message = "Config loaded : ";
            if (self::phpConfigFound())
                $message .= '/config/hostnames/' . $hostname . '/config.php';
            if ($php_config_found && $json_config_found)
                $message .= ' + ';
            if (self::jsonConfigFound())
                $message .= '/config/hostnames/' . $hostname . '/config.json';
            Loutput::framework_debug($message);
        } 
    }
    
}
