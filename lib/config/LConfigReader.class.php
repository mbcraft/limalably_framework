<?php

/**
 * Questa classe è adibita alle letture delle configurazioni in modo che l'utente possa sovrascrivere i valori utilizzando le convenzioni stabilite.
 */
class LConfigReader {
    
    public static function simple($path,$default_value) {
        return LConfig::get($path,
                LConfig::get('/defaults/'.$path,$default_value));
    }
    
    public static function mustSimple($path) {
        return LConfig::get($path,
                LConfig::mustGet('/defaults/'.$path));
    }
    
    public static function executionMode($path,$default_value) {
        $exec_mode = LExecutionMode::get();
        return LConfig::get($path,
                LConfig::get('/execution_mode/'.$exec_mode.'/'.$path,
                 LConfig::get('/defaults/execution_mode/'.$exec_mode.'/'.$path,
                  LConfig::get('/defaults/'.$path,$default_value))));
    }
    
    public static function mustExecutionMode($path) {
        $exec_mode = LExecutionMode::get();
        return LConfig::get($path,
                LConfig::get('/execution_mode/'.$exec_mode.'/'.$path,
                 LConfig::get('/defaults/execution_mode/'.$exec_mode.'/'.$path,
                  LConfig::mustGet('/defaults/'.$path))));
    }
    
    public static function executionModeWithType($type,$config_path) {
        $exec_mode = LExecutionMode::get();
        $path_no_type = str_replace('%type%','',$config_path);
        $path_type = str_replace('%type%',$type,$config_path);
        
        return LConfig::get($path_no_type,
                LConfig::get('/execution_mode/'.$exec_mode.'/'.$path_no_type,
                 LConfig::get('/defaults/execution_mode/'.$exec_mode.'/'.$path_no_type, 
                  LConfig::get('/defaults/'.$path_type,null))));
    }
    
}
