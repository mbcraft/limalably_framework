<?php

class LUrlMapExecutor {
    /**
     * 
     * @param type $exec
     * @return type
     */
    static function isProcExec($exec) {
        return strpos($exec,'#')===false;
    }
    
    /**
     * 
     * @param type $exec
     * @return type
     */
    static function isBlogicExec($exec) {
        return strpos($exec,'#')!==false;
    }
    
    /**
     * Ritorna un valore booleano che indica se la route è valida come shortcut per un file di proc.
     * 
     * @param string $route La route
     * @return boolean true se lo shortcut alla proc è valido, false altrimenti
     */
    static function isValidProcFileRoute($route) {
        $proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $path = $_SERVER['PROJECT_DIR'].$proc_folder.$route.$proc_extension;
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    /**
     * Include il file di una proc
     * 
     * @param string $route La route al proc
     */
    static function includeProcFile($route) {
        $proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $path = $_SERVER['PROJECT_DIR'].$proc_folder.$route.$proc_extension;
        $path = str_replace('//', '/', $path);
        include $path;
    }
}
