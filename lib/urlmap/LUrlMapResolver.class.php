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
    
    
    
    function __construct($root_folder,$static_folder='urlmap/public/static/',$hash_db_folder='urlmap/public/hash_db/',$private_folder='urlmap/private/') {
        $this->root_folder = $root_folder;
        $this->static_folder = $static_folder;
        $this->hash_db_folder = $hash_db_folder;
        $this->private_folder = $private_folder;
        
        if (!is_dir($this->root_folder)) throw new \Exception("La cartella root per la risoluzione degli urlmap non esiste : ".$root_folder);
        if (!is_dir($this->root_folder.$this->static_folder)) throw new \Exception("La cartella per gli urlmap pubblici statici non esiste : ".$static_folder);
        if (!is_dir($this->root_folder.$this->hash_db_folder)) throw new \Exception("La cartella per gli urlmap pubblici hash non esiste : ".$hash_db_folder);
        if (!is_dir($this->root_folder.$this->private_folder)) throw new \Exception("La cartella per gli urlmap privati non esiste : ".$private_folder);
        
        $this->folder_route = LConfigReader::simple('/urlmap/folder_route');
        $this->inherited_route = LConfigReader::simple('/urlmap/inherited_route');
        $this->truncate_route = LConfigReader::simple('/urlmap/truncate_route');
        
        $this->ignore_missing_extends = LConfigReader::simple('/urlmap/ignore_missing_extends');
        $this->ignore_missing_imports = LConfigReader::simple('/urlmap/ignore_missing_imports');

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
    private function isPrivateRoute($route) {
        $path = $this->root_folder.$this->private_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::framework_debug('Cerco private route : '.$route);
        return is_readable($path);
    }
    
    private function getHashUrlMapAsArray($route) {
        $path = $this->root_folder.$this->hash_db_folder.sha1($route).self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        return $this->readUrlMapAsArray($path);
    }
    
    /**
     * Ritorna true se la route parametro è inserita nell'hash_db delle route.
     * 
     * @param string $route La route
     * @return boolean Se la route è valida per l'hash db.
     */
    private function isHashRoute($route) {
        $path = $this->root_folder.$this->hash_db_folder.sha1($route).self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::framework_debug('Cerco hash route : '.$route);
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
      
    private function getPublicUrlMapAsArray($route) {
        $path = $this->root_folder.$this->static_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::framework_debug("Ritorno l'urlmap pubblica alla route ".$route);
        return $this->readUrlMapAsArray($path);
    }
    
    /**
     * Ritorna un valore booleano in base al tipo di route rilevata.
     * 
     * @param string $route La route
     * @return boolean True se è una route pubblica, false altrimenti
     */
    private function isPublicRoute($route) {
        $path = $this->root_folder.$this->static_folder.$route.self::URLMAP_EXTENSION;
        $path = str_replace('//', '/', $path);
        LResult::framework_debug('Cerco static route : '.$route);
        return is_readable($path);
    }
    
    private function normalizeUrlMapWithIncludes($array_map) {
        $url_map_calculator = new LUrlMapCalculator();
        if (isset($array_map['extends'])) {
            $route_list = $array_map['extends'];
            if (!is_array($route_list)) $route_list = array($route_list);
            foreach ($route_list as $route) {
                $map = $this->internalResolveUrlMap($route,self::FLAGS_SEARCH_PRIVATE);
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
                $map = $this->internalResolveUrlMap($route,self::FLAGS_SEARCH_PRIVATE);
                if ($map) {
                    $url_map_calculator->addUrlMapData($map);
                } else {
                    if (!$this->ignore_missing_imports) throw new \Exception("Route not found in imports : ".$route);
                }
            }
            
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
            LResult::framework_debug("Risolvo la route pubblica : ".$route);
            if ($this->isPublicRoute($route)) {
                
                $array_map = $this->getPublicUrlMapAsArray($route);
                if ($this->isUrlMapWithIncludes($array_map)) {
                    $array_map = $this->normalizeUrlMapWithIncludes($array_map);
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
                    $array_map = $this->normalizeUrlMapWithIncludes($array_map);
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
            $array_map = $this->normalizeUrlMapWithIncludes($array_map);
        }
        return $array_map;
    }
    
    public function getParentRoute($route) {
        if (!$this->inherited_route) return null;
        
        if (LStringUtils::endsWith($route, $this->inherited_route)) $route = substr($route,0,-strlen($this->inherited_route));
        if ($route == "") return null;
        if (dirname($route)=='.' || dirname($route)=='/') return $this->inherited_route;
        else return dirname($route).'/'.$this->inherited_route;
    }
    
    public function getNextSearchedRoute($route) {
        if (!$this->truncate_route) return null;
        
        if (LStringUtils::endsWith($route, $this->truncate_route)) $route = substr($route,0,-strlen($this->truncate_route));
        if ($route == "") return null;
        if (dirname($route)=='.' || dirname($route)=='/') return $this->truncate_route;
        else return dirname($route).'/'.$this->truncate_route;
    }
    
    public function resolveUrlMap(string $route, int $search_flags = self::FLAGS_SEARCH_ALL) {
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
        
        if (in_array($route, $this->urlmap_references)) return [];
        else $this->urlmap_references[] = $route;
        
        if (LStringUtils::startsWith($route, '/')) $route = substr ($route, 1);
        
        if (($search_flags & self::FLAGS_SEARCH_PUBLIC) == self::FLAGS_SEARCH_PUBLIC) {
            LResult::framework_debug("Cerco la route in static e hash : ".$route);
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
        }
        if (($search_flags & self::FLAGS_SEARCH_PRIVATE) == self::FLAGS_SEARCH_PRIVATE) {
            LResult::framework_debug("Cerco la route in private : ".$route);
            if ($this->isPrivateRoute($route)) {
                return $this->resolvePrivateUrlMap($route);
            }
        }
        return null;
    }
}
