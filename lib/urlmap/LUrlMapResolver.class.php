<?php

class LUrlMapResolver {
    private $urlmap_references;
    
    private $root_folder;
    
    private $static_folder;
    private $hash_db_folder;
    private $private_folder;
    
    function __construct($root_folder,$static_folder='urlmap/public/static/',$hash_db_folder='urlmap/public/hash_db/',$private_folder='urlmap/private/') {
        $this->root_folder = $root_folder;
        $this->static_folder = $static_folder;
        $this->hash_db_folder = $hash_db_folder;
        $this->private_folder = $private_folder;
        
        if (!is_dir($this->root_folder)) throw new \Exception("La cartella root per la risoluzione degli urlmap non esiste : ".$root_folder);
        if (!is_dir($this->root_folder.$this->static_folder)) throw new \Exception("La cartella per gli urlmap pubblici statici non esiste : ".$static_folder);
        if (!is_dir($this->root_folder.$this->hash_db_folder)) throw new \Exception("La cartella per gli urlmap pubblici hash non esiste : ".$hash_db_folder);
        if (!is_dir($this->root_folder.$this->private_folder)) throw new \Exception("La cartella per gli urlmap privati non esiste : ".$private_folder);
        
    }
    
    private function getPrivateUrlMap($route) {
        $path = $this->root_folder.$this->private_folder.$route.'.json';
        $path = str_replace('//', '/', $path);
        return $this->readUrlMap($path);
    }
    /**
     * Controlla se una route è valida come url map privata.
     * 
     * @param string $route
     * @return boolean true se la route è valida e punta a una url map privata, false altrimenti.
     */
    private function isPrivateRoute($route) {
        $path = $this->root_folder.$this->private_folder.$route.'.json';
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    private function getHashUrlMap($route) {
        $path = $this->root_folder.$this->hash_db_folder.sha1($route).'.json';
        $path = str_replace('//', '/', $path);
        return $this->readUrlMap($path);
    }
    
    /**
     * Ritorna true se la route parametro è inserita nell'hash_db delle route.
     * 
     * @param string $route La route
     * @return boolean Se la route è valida per l'hash db.
     */
    private function isHashRoute($route) {
        $path = $this->root_folder.$this->hash_db_folder.sha1($route).'.json';
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    /**
     * Legge una urlmap al percorso specificato e ritorna una hashmap con i dati dell'urlmap all'interno.
     * 
     * @param string $path Il percorso all'urlmap
     * @return \LTreeMap La hashmap risultante
     * @throws \Exception Se ci sono degli errori in fase di decodifica
     */
    public function readUrlMap($path) {
        if (!is_readable($path)) throw new \Exception("UrlMap at path ".$path." is not readable."); 
        $result_array = json_decode(file_get_contents($path),true);
        $last_error = json_last_error();
        if ($last_error == JSON_ERROR_NONE) {
            $treemap = new LTreeMap($result_array);
            return $treemap;
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
     * @param type $tree_map
     * @return boolean true se la url map contiene un link, false altrimenti
     */
    private function isUrlMapWithIncludes($tree_map) {
        if ($tree_map->is_set('/extends') || $tree_map->is_set('/imports')) return true;
        else return false;
    }
    
    /**
     * Ritorna il valore del link contenuto nell'url map.
     * 
     * @param type $tree_map La mappa hash con i dati
     * @return string La route da usare
     * @throws \Exception Se il parametro non è una mappa hash con un valido link a urlmap
     */
    private function verifyValidRoute($route) {
        if ($this->isPrivateRoute($route) && ($this->isPublicRoute($route) || $this->isHashRoute($route))) throw new \Exception("The linked urlmap is both private and static or hash_db!");
    }
    
    private function getPublicUrlMap($route) {
        $path = $this->root_folder.$this->static_folder.$route.'.json';
        $path = str_replace('//', '/', $path);
        LOutput::framework_debug("Ritorno l'urlmap pubblica alla route ".$route);
        return $this->readUrlMap($path);
    }
    
    /**
     * Ritorna un valore booleano in base al tipo di route rilevata.
     * 
     * @param string $route La route
     * @return boolean True se è una route pubblica, false altrimenti
     */
    private function isPublicRoute($route) {
        $path = $this->root_folder.$this->static_folder.$route.'.json';
        $path = str_replace('//', '/', $path);
        return is_readable($path);
    }
    
    private function normalizeUrlMapWithIncludes($treemap) {
        $url_map_calculator = new LUrlMapCalculator();
        if ($treemap->is_set('/extends')) {
            $route_list = $treemap->getArray('/extends',[]);
            foreach ($route_list as $route) {
                $map = $this->internalResolveUrlMap($route);
                $url_map_calculator->addUrlMapData($map->getRoot());
            }
            $treemap->remove('/extends');
        }
        $url_map_calculator->addUrlMapData($treemap->getRoot());
        if ($treemap->is_set('/imports')) {
            $route_list = $treemap->getArray('/imports',[]);
            foreach ($route_list as $route) {
                $map = $this->internalResolveUrlMap($route);
                $url_map_calculator->addUrlMapData($map->getRoot());
            }
            $treemap->remove('/imports');
        }
        
        return $url_map_calculator->calculate();
    }
     
    public function isShortcutToProc($route) {
        if ($_SERVER['ENVIRONMENT']=='script' && LConfigReader::simple('/urlmap/shortcut_proc')) {
            if ($this->isProcUrlMap($route)) {
                if ($this->isPublicRoute($route) || $this->isHashRoute($route) || $this->isPrivateRoute($route))
                    throw new \Exception("Error : route is a proc shortcut but also something else.");
                return true;
            }
        }
        return false;
    }
    
    public function resolveProcShortcut($route) {
        //se sono in uno script e la route punta a una proc e la config è ok allora prendo quella
        if ($_SERVER['ENVIRONMENT']=='script' && LConfigReader::simple('/urlmap/shortcut_proc')) {
            if ($this->isProcUrlMap($route)) {
                if ($this->isPublicRoute($route) || $this->isHashRoute($route) || $this->isPrivateRoute($route))
                    throw new \Exception("Error : route is a proc but also something else.");
                $builder = new LUrlMapBuilder();
                $builder->setFormat('output');
                $builder->setExecDo($route);
                return $builder->getUrlMapData();
            }
        }
        throw new \Exception("Route does not resolve to a proc shortcut.");
    }
    
    private function resolvePublicUrlMap($route) {
        $calculator = new LUrlMapCalculator();
        do {
            LOutput::framework_debug("Risolvo la route pubblica : ".$route);
            if ($this->isPublicRoute($route)) {
                
                $treemap = $this->getPublicUrlMap($route);
                if ($this->isUrlMapWithIncludes($treemap)) {
                    $treemap = $this->normalizeUrlMapWithIncludes($treemap);
                }
                $calculator->unshiftUrlMapData($treemap->getRoot());
            }
            $route = $this->getParentRoute($route);
        } while ($route != null);
        return $calculator->calculate();
    }
    
    private function resolvePrivateUrlMap($route) {
        $calculator = new LUrlMapCalculator();
        do {
            if ($this->isPrivateRoute($route)) {
                $treemap = $this->getPrivateUrlMap($route);
                if ($this->isUrlMapWithIncludes($treemap)) {
                    $treemap = $this->normalizeUrlMapWithIncludes($treemap);
                }
                $calculator->unshiftUrlMapData($treemap->getRoot());
            }
            $route = $this->getParentRoute($route);
        } while ($route != null);
        return $calculator->calculate();
    }
    
    private function resolveHashUrlMap($route) {
        $treemap = $this->getHashUrlMap($route);
        if ($this->isUrlMapWithIncludes($treemap)) {
            $treemap = $this->normalizeUrlMapWithIncludes($treemap);
        }
        return $treemap;
    }
    
    public function getParentRoute($route) {
        if (LStringUtils::endsWith($route, '_default')) $route = substr($route,0,-8);
        if ($route == "") return null;
        if (dirname($route)=='.' || dirname($route)=='/') return '_default';
        else return dirname($route).'/_default';
    }
    
    public function resolveUrlMap($route) {
        $this->urlmap_references = [];
        return $this->internalResolveUrlMap($route);
    }
    
    private function internalResolveUrlMap($route) {
        if (in_array($route, $this->urlmap_references)) return new LTreeMap();
        else $this->urlmap_references[] = $route;
        
        if (LStringUtils::startsWith($route, '/')) $route = substr ($route, 1);
        
        $route_check_order = LConfigReader::executionMode('/urlmap/search_order');
        $route_checks = explode(',',$route_check_order);
        foreach ($route_checks as $route_check) {
            switch ($route_check) {
                case 'static' : {
                    if ($this->isPublicRoute($route)) {
                        if ($this->isPrivateRoute($route)) throw new \Exception("Route ".$route." is both private and public");
                        return $this->resolvePublicUrlMap($route);
                    }
                    break;
                }
                case 'hash_db' : {
                    if ($this->isHashRoute($route)) {
                        if ($this->isPrivateRoute($route)) throw new \Exception("Route ".$route." is both private and hash");
                        return $this->resolveHashUrlMap($route);
                    }
                    break;
                }
            }

        }
        if ($this->isPrivateRoute($route)) {
            return $this->resolvePrivateUrlMap($route);
        }
        return null;
    }
}
