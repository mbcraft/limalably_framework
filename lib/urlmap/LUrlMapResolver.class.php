<?php

class LUrlMapResolver {
    
    
    static function isProcUrlMap($route) {
        $begin_with = LConfig::mustGet('/defaults/classloader/proc_folder');
        return LStringUtils::startsWith($route, $begin_with);
    }
    
    static function getProcUrlMap($route,$format) {
        $urlmap_builder = new LUrlMapBuilder();
        $urlmap_builder->setFormat($format);
        $urlmap_builder->setExecDo($route);
        return $urlmap_builder->getUrlMapData();
    }
    
    static function isPrivateUrlMap($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/private/'.$route.'.json';
        return is_readable($path);
    }
    
    static function isUrlMapHash($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/hash_db/'.sha1($route).'.json';
        return is_readable($path);
    }
    
    static function isUrlMapLink($hash_map) {
        if ($hash_map->is_set('urlmap_link')) return true;
        else return false;
    }
    
    static function getValidUrlMapLink($hash_map) {
        if (!self::isUrlMapLink($hash_map)) throw new \Exception("Parameter is not a valid urlmap link!");
        $link = $hash_map->mustGet('/urlmap_link');
        if (self::isPrivateUrlMap($link) && self::isPublicUrlMap($link)) throw new \Exception("The linked urlmap is both private and public!");
        return $link;
    }
    
    static function isPublicUrlMap($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/static/'.$route.'.json';
        return is_readable($path);
    }
}
