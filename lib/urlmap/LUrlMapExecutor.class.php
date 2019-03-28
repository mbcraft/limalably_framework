<?php

class LUrlMapExecutor {
    
    private $my_url_map = null;
    
    function __construct($url_map) {
        if (!$url_map instanceof LTreeMap) throw new \Exception("Url map is not valid");
        $this->my_url_map = $url_map;
    }
    
    function execute($input) {
        //import parametri
        //input parameters check
        //session parameters check
        //output composition
        //exec before do after
        //template rendering
    }
    /**
     * 
     * @param type $exec
     * @return type
     */
    private static function isProcExec($exec) {
        return !self::isClassMethodExec($exec);
    }
    
    /**
     * 
     * @param type $exec
     * @return type
     */
    private static function isClassMethodExec($exec) {
        return strpos($exec,'#')!==false || strpos($exec,'::')!==false;
    }
    
    /**
     * Ritorna un valore booleano che indica se la route è valida come shortcut per un file di proc.
     * 
     * @param string $route La route
     * @return boolean true se lo shortcut alla proc è valido, false altrimenti
     */
    private static function isValidProcFileRoute($route) {
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
    private static function executeProcFile($route) {
        $proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $path = $_SERVER['PROJECT_DIR'].$proc_folder.$route.$proc_extension;
        $path = str_replace('//', '/', $path);
        include $path;
    }
    
    private static function executeBlogic($route) {
        
    }
}
