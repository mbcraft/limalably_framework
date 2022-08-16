<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/**
 * Questa classe è adibita alle letture delle configurazioni in modo che l'utente possa sovrascrivere i valori utilizzando le convenzioni stabilite.
 */
class LConfigReader {
    
    const NO_DEFAULT_VALUE = -16;
    
    public static function simple($config_path,$default_value = self::NO_DEFAULT_VALUE) {
        if (LConfig::is_set($config_path)) {
            return LConfig::mustGetOriginal($config_path);
        }
        if (LConfig::is_set('/defaults/'.$config_path)) {
            return LConfig::mustGetOriginal('/defaults/'.$config_path);
        }
        
        if ($default_value != self::NO_DEFAULT_VALUE) return $default_value;
        
        throw new \Exception("Value not found in config : ".$config_path);
    }
    
    public static function executionMode($config_path,$default_value = self::NO_DEFAULT_VALUE) {
        $exec_mode = LExecutionMode::get();
        
        if (LConfig::is_set($config_path)) {
            return LConfig::mustGetOriginal($config_path);
        }
        
        if (LConfig::is_set('/execution_mode/'.$exec_mode.'/'.$config_path)) {
            return LConfig::mustGetOriginal('/execution_mode/'.$exec_mode.'/'.$config_path);
        }
        
        if (LConfig::is_set('/defaults/execution_mode/'.$exec_mode.'/'.$config_path)) {
            return LConfig::mustGetOriginal('/defaults/execution_mode/'.$exec_mode.'/'.$config_path);
        }
        
        if (LConfig::is_set('/defaults/'.$config_path)) {
            return LConfig::mustGetOriginal('/defaults/'.$config_path);
        }
        
        if ($default_value != self::NO_DEFAULT_VALUE) return $default_value;
        
        throw new \Exception("Value not found in config : ".$config_path);
    }
    
    public static function executionModeWithType($type,$config_path,$default_value = self::NO_DEFAULT_VALUE) {
        $exec_mode = LExecutionMode::get();
        $path_no_type = str_replace('%type%','',$config_path);
        $path_type = str_replace('%type%',$type,$config_path);
        
        if (LConfig::is_set($path_no_type)) {
            return LConfig::mustGetOriginal($path_no_type);
        }
        if (LConfig::is_set('/execution_mode/'.$exec_mode.'/'.$path_no_type)) {
            return LConfig::mustGetOriginal('/execution_mode/'.$exec_mode.'/'.$path_no_type);
        }
        if (LConfig::is_set('/defaults/execution_mode/'.$exec_mode.'/'.$path_no_type)) {
            return LConfig::mustGetOriginal('/defaults/execution_mode/'.$exec_mode.'/'.$path_no_type);
        }
        if (LConfig::is_set('/defaults/'.$path_type)) {
            return LConfig::mustGetOriginal('/defaults/'.$path_type);
        }
        
        if ($default_value != self::NO_DEFAULT_VALUE) return $default_value;
        
        throw new \Exception("Value not found in config : ".$config_path);
    }
    
}
