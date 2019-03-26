<?php

class LUrlMapResolver {
    
    
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
        include $path;
    }
    
    /**
     * Crea una url map
     * 
     * @param type $route
     * @param type $format
     * @return type
     */
    static function createUrlMap($route,$format,$save_file_path=null) {
        $urlmap_builder = new LUrlMapBuilder();
        $urlmap_builder->setFormat($format);
        if ($save_file_path) {
            $urlmap_builder->setSaveFile($save_file_path);
        }
        $urlmap_builder->setExecDo($route);
        return $urlmap_builder->getUrlMapData();
    }
    
    
    static function getPrivateUrlMap($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/private/'.$route.'.json';
        $path = str_replace('//', '/', $path);
        return self::readUrlMap($path);
    }
    /**
     * Controlla se una route è valida come url map privata.
     * 
     * @param string $route
     * @return boolean true se la route è valida e punta a una url map privata, false altrimenti.
     */
    static function isPrivateRoute($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/private/'.$route.'.json';
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    static function getHashUrlMap($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/hash_db/'.sha1($route).'.json';
        return self::readUrlMap($path);
    }
    
    /**
     * Ritorna true se la route parametro è inserita nell'hash_db delle route.
     * 
     * @param string $route La route
     * @return boolean Se la route è valida per l'hash db.
     */
    static function isHashRoute($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/hash_db/'.sha1($route).'.json';
        return is_readable($path);
    }
    
    /**
     * Legge una urlmap al percorso specificato e ritorna una hashmap con i dati dell'urlmap all'interno.
     * 
     * @param string $path Il percorso all'urlmap
     * @return \LHashMap La hashmap risultante
     * @throws \Exception Se ci sono degli errori in fase di decodifica
     */
    static function readUrlMap($path) {
        if (!is_readable($path)) throw new \Exception("UrlMap at path ".$path." is not readable."); 
        $result_array = json_decode(file_get_contents($path),true);
        $last_error = json_last_error();
        if ($last_error == JSON_ERROR_NONE) {
            $hashmap = new LHashMap($result_array);
            return $hashmap;
        }
        switch ($last_error) {
            case JSON_ERROR_DEPTH : throw new \Exception("Error decoding urlmap at path : ".$path.". Max depth reached.");
            case JSON_ERROR_STATE_MISMATCH : throw new \Exception("Error decoding urlmap at path : ".$path.". Invalid or malformed JSON.");
            case JSON_ERROR_CTRL_CHAR : throw new \Exception("Error decoding urlmap at path : ".$path.". Control character error.");
            case JSON_ERROR_SYNTAX : throw new \Exception("Error decoding urlmap at path : ".$path.". Syntax error.");
            case JSON_ERROR_UTF8 : throw new \Exception("Error decoding urlmap at path : ".$path.". UTF-8 encoding error.");
            case JSON_ERROR_RECURSION : throw new \Exception("Error decoding urlmap at path : ".$path.". Error in recursive references.");
            case JSON_ERROR_INF_OR_NAN : throw new \Exception("Error decoding urlmap at path : ".$path.". INF or NaN values found.");
            case JSON_ERROR_UNSUPPORTED_TYPE : throw new \Exception("Error decoding urlmap at path : ".$path.". A value of type that cannot be encoded is found.");
            case JSON_ERROR_INVALID_PROPERTY_NAME : throw new \Exception("Error decoding urlmap at path : ".$path.". Invalid property name.");
            case JSON_ERROR_UTF16 : throw new \Exception("Error decoding urlmap at path : ".$path.". UTF-16 encoding error.");
            default : throw new \Exception("Error decoding urlmap at path : ".$path.".");
        }
        
    }
    /**
     * Ritorna un valore booleano in base alla presenza di link a route nella urlmap
     * @param type $hash_map
     * @return boolean true se la url map contiene un link, false altrimenti
     */
    static function isUrlMapLink($hash_map) {
        if ($hash_map->is_set('urlmap_link')) return true;
        else return false;
    }
    
    /**
     * Ritorna il valore del link contenuto nell'url map.
     * 
     * @param type $hash_map La mappa hash con i dati
     * @return string La route da usare
     * @throws \Exception Se il parametro non è una mappa hash con un valido link a urlmap
     */
    public static function getValidUrlMapRouteLink($hash_map) {
        if (!self::isUrlMapLink($hash_map)) throw new \Exception("Parameter is not a valid urlmap link!");
        $link = $hash_map->mustGet('/urlmap_link');
        if (self::isPrivateRoute($link) && self::isPublicRoute($link)) throw new \Exception("The linked urlmap is both private and public!");
        return $link;
    }
    
    public static function getPublicUrlMap($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/static/'.$route.'.json';
        $path = str_replace('//', '/', $path);
        return self::readUrlMap($path);
    }
    
    /**
     * Ritorna un valore booleano in base al tipo di route rilevata.
     * 
     * @param string $route La route
     * @return boolean True se è una route pubblica, false altrimenti
     */
    public static function isPublicRoute($route) {
        $path = $_SERVER['PROJECT_DIR'].'urlmap/public/static/'.$route.'.json';
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    static function normalizeUrlMapWithLink($hashmap) {
        $route_link = self::getValidUrlMapRouteLink($hashmap);
        $hashmap->remove('/urlmap_link');
        $linked_url_map = self::simpleResolveUrlMap($route_link);
        $linked_url_map_array = $linked_url_map->getRoot();
        $base_url_map_array = $hashmap->getRoot();
        $url_map_calculator = new LUrlMapCalculator();
        $url_map_calculator->shiftUrlMapData($base_url_map_array);
        $url_map_calculator->shiftUrlMapData($linked_url_map_array);
        return $url_map_calculator->calculate();
    }
     
    static function isShortcutToProc($route) {
        if ($_SERVER['ENVIRONMENT']=='script' && LConfigReader::simple('/urlmap/shortcut_proc')) {
            if (self::isProcUrlMap($route)) {
                if (self::isPublicRoute($route) || self::isHashRoute($route) || self::isPrivateRoute($route))
                    throw new \Exception("Error : route is a proc shortcut but also something else.");
                return true;
            }
        }
        return false;
    }
    
    static function resolveProcShortcut($route) {
        //se sono in uno script e la route punta a una proc e la config è ok allora prendo quella
        if ($_SERVER['ENVIRONMENT']=='script' && LConfigReader::simple('/urlmap/shortcut_proc')) {
            if (self::isProcUrlMap($route)) {
                if (self::isPublicRoute($route) || self::isHashRoute($route) || self::isPrivateRoute($route))
                    throw new \Exception("Error : route is a proc but also something else.");
                $builder = new LUrlMapBuilder();
                $builder->setFormat('output');
                $builder->setExecDo($route);
                return $builder->getUrlMapData();
            }
        }
        throw new \Exception("Route does not resolve to a proc shortcut.");
    }
    
    private static function resolvePublicUrlMap($route) {
        $calculator = new LUrlMapCalculator();
        do {
            if (self::isPublicRoute($route)) {
                $hashmap = self::getPublicUrlMap($route);
                if (self::isUrlMapLink($hashmap)) {
                    $hashmap = self::normalizeUrlMapWithLink($hashmap);
                }
                $calculator->shiftUrlMapData($hashmap->getRoot());
            }
            $route = self::getParentRoute($route);
        } while ($route != null);
        return $calculator->calculate();
    }
    
    private static function resolvePrivateUrlMap($route) {
        $calculator = new LUrlMapCalculator();
        do {
            if (self::isPrivateRoute($route)) {
                $hashmap = self::getPrivateUrlMap($route);
                if (self::isUrlMapLink($hashmap)) {
                    $hashmap = self::normalizeUrlMapWithLink($hashmap);
                }
                $calculator->shiftUrlMapData($hashmap->getRoot());
            }
            $route = self::getParentRoute($route);
        } while ($route != null);
        return $calculator->calculate();
    }
    
    private static function resolveHashUrlMap($route) {
        $hashmap = self::getHashUrlMap($route);
        if (self::isUrlMapLink($hashmap)) {
            $hashmap = self::normalizeUrlMapWithLink($hashmap);
        }
        return $hashmap;
    }
    
    public static function getParentRoute($route) {
        if (LStringUtils::endsWith($route, '_default')) $route = substr($route,0,-8);
        if ($route == "") return null;
        if (dirname($route)=='.' || dirname($route)=='/') return '_default';
        else return dirname($route).'/_default';
    }
    
    private static function simpleResolveUrlMap($route) {
        if (LStringUtils::startsWith($route, '/')) $route = substr ($route, 1);
        
        $route_check_order = LConfigReader::executionMode('/urlmap/search_order');
        $route_checks = explode(',',$route_check_order);
        foreach ($route_checks as $route_check) {
            switch ($route_check) {
                case 'static' : {
                    if (self::isPublicRoute($route)) {
                        if (self::isPrivateRoute($route)) throw new \Exception("Route ".$route." is both private and public");
                        return self::resolvePublicUrlMap($route);
                    }
                    break;
                }
                case 'hash_db' : {
                    if (self::isHashRoute($route)) {
                        if (self::isPrivateRoute($route)) throw new \Exception("Route ".$route." is both private and hash");
                        return self::resolveHashUrlMap($route);
                    }
                    break;
                }
            }

        }
        if (self::isPrivateRoute($route)) {
            return self::resolvePrivateUrlMap($route);
        }
        return false;
    }
}
