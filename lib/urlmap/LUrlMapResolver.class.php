<?php

class LUrlMapResolver {
    
    static $my_preferred_format = null;    
    
    static function isProcExec($link) {
        return strpos($link,'#')===false;
    }
    
    static function isBlogicExec($link) {
        return strpos($link,'#')!==false;
    }
    
    static function isValidProcUrlMap($route) {
        $proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $path = $_SERVER['PROJECT_DIR'].$proc_folder.$route.$proc_extension;
        return is_readable($path);
    }
    
    static function includeProcUrlMapFile($route) {
        $proc_folder = LConfigReader::simple('/classloader/proc_folder');
        $proc_extension = LConfigReader::simple('/classloader/proc_extension');
        $path = $_SERVER['PROJECT_DIR'].$proc_folder.$route.$proc_extension;
        include $path;
    }
    
    static function createProcUrlMap($route,$format) {
        $urlmap_builder = new LUrlMapBuilder();
        $urlmap_builder->setFormat($format);
        $urlmap_builder->setExecDo($route);
        return $urlmap_builder->getUrlMapData();
    }
    
    static function isPrivateRoute($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/private/'.$route.'.json';
        return is_readable($path);
    }
    
    static function isHashRoute($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/hash_db/'.sha1($route).'.json';
        return is_readable($path);
    }
    
    static function isUrlMapLink($hash_map) {
        if ($hash_map->is_set('urlmap_link')) return true;
        else return false;
    }
    
    static function getValidUrlMapRouteLink($hash_map) {
        if (!self::isUrlMapLink($hash_map)) throw new \Exception("Parameter is not a valid urlmap link!");
        $link = $hash_map->mustGet('/urlmap_link');
        if (self::isPrivateRoute($link) && self::isPublicRoute($link)) throw new \Exception("The linked urlmap is both private and public!");
        return $link;
    }
    
    static function isPublicRoute($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/static/'.$route.'.json';
        return is_readable($path);
    }
    
    static function setPreferredFormat($preferred_format) {
        self::$my_preferred_format = $preferred_format;
    }
    
    static function resolveUrlMap($route) {
        if ($_SERVER['ENVIRONMENT']=='script' && LConfigReader::simple('/urlmap/shortcut_proc')) {
            if (self::isProcUrlMap($route)) {
                
            }
        }
    }
}
