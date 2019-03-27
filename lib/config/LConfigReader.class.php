<?php

/**
 * Questa classe è adibita alle letture delle configurazioni in modo che l'utente possa sovrascrivere i valori utilizzando le convenzioni stabilite.
 */
class LConfigReader {
    
    public static function simple($config_path) {
        if (LConfig::is_set($config_path)) {
            return LConfig::mustGet($config_path);
        }
        if (LConfig::is_set('/defaults/'.$config_path)) {
            return LConfig::mustGet('/defaults/'.$config_path);
        }
        
        throw new \Exception("Value not found in config : ".$config_path);
    }
    
    public static function executionMode($config_path) {
        $exec_mode = LExecutionMode::get();
        
        if (LConfig::is_set($config_path)) {
            return LConfig::mustGet($config_path);
        }
        
        if (LConfig::is_set('/execution_mode/'.$exec_mode.'/'.$config_path)) {
            return LConfig::mustGet('/execution_mode/'.$exec_mode.'/'.$config_path);
        }
        
        if (LConfig::is_set('/defaults/execution_mode/'.$exec_mode.'/'.$config_path)) {
            return LConfig::mustGet('/defaults/execution_mode/'.$exec_mode.'/'.$config_path);
        }
        
        if (LConfig::is_set('/defaults/'.$config_path)) {
            return LConfig::mustGet('/defaults/'.$config_path);
        }
        
        throw new \Exception("Value not found in config : ".$config_path);
    }
    
    public static function executionModeWithType($type,$config_path) {
        $exec_mode = LExecutionMode::get();
        $path_no_type = str_replace('%type%','',$config_path);
        $path_type = str_replace('%type%',$type,$config_path);
        
        if (LConfig::is_set($path_no_type)) {
            return LConfig::mustGet($path_no_type);
        }
        if (LConfig::is_set('/execution_mode/'.$exec_mode.'/'.$path_no_type)) {
            return LConfig::mustGet('/execution_mode/'.$exec_mode.'/'.$path_no_type);
        }
        if (LConfig::is_set('/defaults/execution_mode/'.$exec_mode.'/'.$path_no_type)) {
            return LConfig::mustGet('/defaults/execution_mode/'.$exec_mode.'/'.$path_no_type);
        }
        if (LConfig::is_set('/defaults/'.$path_type)) {
            return LConfig::mustGet('/defaults/'.$path_type);
        }
        
        throw new \Exception("Value not found in config : ".$config_path);
    }
    
}
