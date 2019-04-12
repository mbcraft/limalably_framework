<?php

class LUrlMapResolver {
    
    const URLMAP_EXTENSION = ".json";
    
    const FLAGS_SEARCH_PUBLIC = 1;
    const FLAGS_SEARCH_PRIVATE = 2;
    const FLAGS_SEARCH_ALL = 3;
    
    private $urlmap_references;
    private $original_route;
    
    private $root_folder;
    
    private $static_folder;
    private $hash_db_folder;
    private $private_folder;
    
    private $folder_route;
    private $inherited_route;
    private $truncate_route;
    
    private $ignore_missing_extends;
    private $ignore_missing_imports;
    
    const VARIABLE_PREFIX = '{';
    const VARIABLE_SUFFIX = '}';
        
    function isInitialized() {
        return $this->root_folder != null;
    }
    
    private function finalizeInit() {
        if (!is_dir($this->root_folder)) throw new \Exception("La cartella root per la risoluzione degli urlmap non esiste : ".$this->root_folder);
        if (!is_dir($this->root_folder.$this->static_folder)) throw new \Exception("La cartella per gli urlmap pubblici statici non esiste : ".$this->static_folder);
        if (!is_dir($this->root_folder.$this->hash_db_folder)) throw new \Exception("La cartella per gli urlmap pubblici hash non esiste : ".$this->hash_db_folder);
        if (!is_dir($this->root_folder.$this->private_folder)) throw new \Exception("La cartella per gli urlmap privati non esiste : ".$this->private_folder);
        
        $this->folder_route = LConfigReader::simple('/urlmap/special_folder_route');
        $this->inherited_route = LConfigReader::simple('/urlmap/special_inherited_route');
        $this->truncate_route = LConfigReader::simple('/urlmap/special_truncate_route');
        
        $this->ignore_missing_extends = LConfigReader::simple('/urlmap/ignore_missing_extends');
        $this->ignore_missing_imports = LConfigReader::simple('/urlmap/ignore_missing_imports');
    }
    
    function init(string $root_folder,string $static_folder,string $hash_db_folder,string $private_folder) {
        
        $this->root_folder = $root_folder;
        $this->static_folder = $static_folder;
        $this->hash_db_folder = $hash_db_folder;
        $this->private_folder = $private_folder;
        
        $this->finalizeInit();
    }
    
    function initWithDefaults() {
        
        $this->root_folder = $_SERVER['PROJECT_DIR'];
        $this->static_folder = LConfigReader::simple('/urlmap/static_routes_folder');
        $this->hash_db_folder = LConfigReader::simple('/urlmap/hash_db_routes_folder');
        $this->private_folder = LConfigReader::simple('/urlmap/private_routes_folder');
        
        $this->finalizeInit();
    }

    
    private function getPrivateUrlMapAsArray($route) {
        $path = $this->root_folder.$this->private_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        return $this->readUrlMapAsArray($path);
    }
    /**
     * Controlla se una route è valida come url map privata.
     * 
     * @param string $route
     * @return boolean true se la route è valida e punta a una url map privata, false altrimenti.
     */
    public function isPrivateRoute($route) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $path = $this->root_folder.$this->private_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::trace('Cerco private route : '.$route);
        return is_readable($path);
    }
    
    private function getHashUrlMapAsArray($route) {
        $path = $this->root_folder.$this->hash_db_folder.sha1($route).self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        return $this->readUrlMapAsArray($path);
    }
    
    public function getHashDbFilename($route) {
        return sha1($route).self::URLMAP_EXTENSION;
    }
    
    /**
     * Ritorna true se la route parametro è inserita nell'hash_db delle route.
     * 
     * @param string $route La route
     * @return boolean Se la route è valida per l'hash db.
     */
    public function isHashRoute($route) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $path = $this->root_folder.$this->hash_db_folder.$this->getHashDbFilename($route);
        $path = str_replace('//', '/', $path);
        LResult::trace('Cerco hash route : '.$route);
        return is_readable($path);
    }
        
    private function replaceUrlMapVariables($urlmap_content,$variables) {
        foreach ($variables as $k => $v) {
            $urlmap_content = str_replace(self::VARIABLE_PREFIX.$k.self::VARIABLE_SUFFIX, $v, $urlmap_content);
        }
        return $urlmap_content;
    }
    
    /**
     * Legge una urlmap al percorso specificato e ritorna una hashmap con i dati dell'urlmap all'interno.
     * 
     * @param string $path Il percorso all'urlmap
     * @return \LTreeMap La hashmap risultante
     * @throws \Exception Se ci sono degli errori in fase di decodifica
     */
    public function readUrlMapAsArray($path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (!is_readable($path)) throw new \Exception("UrlMap at path ".$path." is not readable.");
        $urlmap_content = file_get_contents($path);
        $current_map_array = LJsonUtils::parseContent("urlmap",$path,$urlmap_content);
        
        $capture_array = [];
        if (isset($current_map_array['capture'])) {
            $capture_pattern = $current_map_array['capture'];
            $route_capture = new LRouteCapture();
            $capture_array = $route_capture->captureParameters($capture_pattern, $this->original_route);
        }
        
        $all_replacement_params = array_replace(LEnvironmentUtils::getReplacementsArray(), $capture_array);
        $new_urlmap_content = $this->replaceUrlMapVariables($urlmap_content, $all_replacement_params);
        return LJsonUtils::parseContent("urlmap",$path,$new_urlmap_content);
    }
    /**
     * Ritorna un valore booleano in base alla presenza di link a route nella urlmap
     * @param type $tree_map
     * @return boolean true se la url map contiene un link, false altrimenti
     */
    private function isUrlMapWithIncludes($array_map) {
        if (isset($array_map['extends']) || isset($array_map['imports'])) return true;
        else return false;
    }
      
    private function getStaticUrlMapAsArray($route) {
        $path = $this->root_folder.$this->static_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::trace("Ritorno l'urlmap pubblica alla route ".$route);
        return $this->readUrlMapAsArray($path);
    }
    
    /**
     * Ritorna un valore booleano in base al tipo di route rilevata.
     * 
     * @param string $route La route
     * @return boolean True se è una route pubblica, false altrimenti
     */
    public function isStaticRoute($route) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $path = $this->root_folder.$this->static_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::trace('Cerco static route : '.$route);
        return is_readable($path);
    }
    
    private function normalizeUrlMapWithIncludes($array_map,$includes_search_flags) {
        $url_map_calculator = new LUrlMapCalculator();
        if (isset($array_map['extends'])) {
            $route_list = $array_map['extends'];
            if (!is_array($route_list)) $route_list = array($route_list);
            foreach ($route_list as $route) {
                $map = $this->internalResolveUrlMap($route,$includes_search_flags);
                if ($map) {
                    $url_map_calculator->addUrlMapData($map);
                } else {
                    if (!$this->ignore_missing_extends) throw new \Exception("Route not found in extends : ".$route);
                }
            }
            //unset($array_map['extends']);
        }
        $route_list = null;
        //rimuovo prima gli import per non causare strane ricorsioni
        if (isset($array_map['imports'])) {
            $route_list = $array_map['imports'];
            if (!is_array($route_list)) $route_list = array($route_list);
            //unset($array_map['imports']);
        }
        
        $url_map_calculator->addUrlMapData($array_map);
        
        if ($route_list) {
            foreach ($route_list as $route) {
                $map = $this->internalResolveUrlMap($route,$includes_search_flags);
                if ($map) {
                    $url_map_calculator->addUrlMapData($map);
                } else {
                    if (!$this->ignore_missing_imports) throw new \Exception("Route not found in imports : ".$route);
                }
            }
            
        }
        
        return $url_map_calculator->calculate();
    }
         
    private function resolveStaticUrlMap($route) {
        $calculator = new LUrlMapCalculator();
        do {
            LResult::trace("Risolvo la route pubblica : ".$route);
            if ($this->isStaticRoute($route)) {
                
                $array_map = $this->getStaticUrlMapAsArray($route);
                if ($this->isUrlMapWithIncludes($array_map)) {
                    $array_map = $this->normalizeUrlMapWithIncludes($array_map,self::FLAGS_SEARCH_PRIVATE);
                }
                $calculator->unshiftUrlMapData($array_map);
            }
            $route = $this->getParentRoute($route);
        } while ($route != null);
        return $calculator->calculate();
    }
    
    private function resolvePrivateUrlMap($route) {
        $calculator = new LUrlMapCalculator();
        do {
            if ($this->isPrivateRoute($route)) {
                $array_map = $this->getPrivateUrlMapAsArray($route);
                if ($this->isUrlMapWithIncludes($array_map)) {
                    $array_map = $this->normalizeUrlMapWithIncludes($array_map,self::FLAGS_SEARCH_PRIVATE);
                }
                $calculator->unshiftUrlMapData($array_map);
            }
            $route = $this->getParentRoute($route);
        } while ($route != null);
        return $calculator->calculate();
    }
    
    private function resolveHashUrlMap($route) {
        $array_map = $this->getHashUrlMapAsArray($route);
        if ($this->isUrlMapWithIncludes($array_map)) {
            $array_map = $this->normalizeUrlMapWithIncludes($array_map,self::FLAGS_SEARCH_ALL);
        }
        return $array_map;
    }
    
    public function getParentRoute($route) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (!$this->inherited_route) return null;
        
        if (LStringUtils::endsWith($route, $this->inherited_route)) $route = substr($route,0,-strlen($this->inherited_route));
        if ($route == "") return null;
        if (dirname($route)=='.' || dirname($route)=='/') return $this->inherited_route;
        else return dirname($route).'/'.$this->inherited_route;
    }
    
    public function getNextSearchedRoute($route) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (!$this->truncate_route) return null;
        
        if (LStringUtils::endsWith($route, $this->truncate_route)) $route = substr($route,0,-strlen($this->truncate_route));
        if ($route == "") return null;
        if (dirname($route)=='.' || dirname($route)=='/') return $this->truncate_route;
        else return dirname($route).'/'.$this->truncate_route;
    }
    
    public function resolveUrlMap(string $route, int $search_flags = self::FLAGS_SEARCH_ALL) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $this->original_route = $route;
        $this->urlmap_references = [];
        do {
            $array_map = $this->internalResolveUrlMap($route,$search_flags);
            if ($array_map) return new LTreeMap($array_map);
            $route = $this->getNextSearchedRoute($route);
        } while ($route!=null);
        
        return null;
    }
    
    private function internalResolveUrlMap(string $route,int $search_flags) {
        if (LStringUtils::endsWith($route, '/')) {
            if ($this->folder_route) {
                $route = $route.$this->folder_route;
            } else {
                return null;
            }
        }
        
        if (in_array($route, $this->urlmap_references)) return [];  //ok cerca nei valori
        else $this->urlmap_references[] = $route;
        
        if (LStringUtils::startsWith($route, '/')) $route = substr ($route, 1);
        
        if (($search_flags & self::FLAGS_SEARCH_PUBLIC) == self::FLAGS_SEARCH_PUBLIC) {
            LResult::trace("Cerco la route in static e hash : ".$route);
            $route_check_order = LConfigReader::executionMode('/urlmap/search_order');
            $route_checks = explode(',',$route_check_order);
            foreach ($route_checks as $route_check) {
                switch ($route_check) {
                    case 'static' : {
                        if ($this->isStaticRoute($route)) {
                            if ($this->isPrivateRoute($route)) throw new \Exception("Route ".$route." is both private and public");
                            return $this->resolveStaticUrlMap($route);
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
        }
        if (($search_flags & self::FLAGS_SEARCH_PRIVATE) == self::FLAGS_SEARCH_PRIVATE) {
            LResult::trace("Cerco la route in private : ".$route);
            if ($this->isPrivateRoute($route)) {
                return $this->resolvePrivateUrlMap($route);
            }
        }
        return null;
    }
}
