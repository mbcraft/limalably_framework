<?php

/**
 * Alias of require_once but with project path as base folder
 * 
 * @param type $filename_relative_path
 */
function project_require_once($filename_relative_path) {
    require_once($_SERVER['PROJECT_DIR'].$filename_relative_path);
}

/**
 * Alias of require but with project path as base folder
 * 
 * @param type $filename_relative_path
 */
function project_require($filename_relative_path) {
    require($_SERVER['PROJECT_DIR'].$filename_relative_path);
}

class LClassLoader {
      
    //patterns
    const PATTERN_FIND_NAMESPACES = "/\nnamespace[ ]+(?<namespace>[a-zA-Z_0-9\\\\]+)[;{ ]+/i";
    const PATTERN_FIND_CLASSES = "/\n(abstract )?(final )?class[ ]+(?<class>[a-zA-Z_0-9]+)[{ ]+/i";
    const PATTERN_FIND_TRAITS = "/\ntrait[ ]+(?<trait>[a-zA-Z_0-9]+)[{ ]+/i";
    const PATTERN_FIND_INTERFACES = "/\ninterface[ ]+(?<interface>[a-zA-Z_0-9]+)[{ ]+/i";
    
    private static $class_map = [];
    
    public static function autoload($clazz) {
        $is_skip_cache_route = self::isSkipCacheRoute();
        
        if (!$is_skip_cache_route && (LExecutionMode::isTesting() || LExecutionMode::isProduction())) {
            if (isset(self::$class_map[$clazz]))
            {
                $original_path = self::$class_map[$clazz];
                if (!self::hasCachedClassContent(self::$class_map[$clazz]))
                {
                    $original_content = file_get_contents($original_path);
                    $mangled_content = self::prepareClassContent($original_content);
                    self::saveMangledClassToCache($original_path, $mangled_content);
                }
                self::requireCachedClassContent($original_path);
            }
        } else {
            if (isset(self::$class_map[$clazz])) require_once(self::$class_map[$clazz]);
        }
    }
    
    private static function prepareClassContent($class_content) {
        $mangled_call_list = LConfigReader::simple('/classloader/cache_commented_call_list');
        foreach ($mangled_call_list as $call_text) {
            $class_content = str_replace($call_text, '//'.$call_text, $class_content);
        }
        return $class_content;
    }
    
    private static function emptyCache() {
        self::deleteClassMapCache();
        self::deleteClassContentCache();
    }
    
    public static function init() {
            
        if (LExecutionMode::isTesting() || LExecutionMode::isProduction()) {
            if (self::hasClassMapCache()) {
                self::loadClassMapFromCache();
            } else {
                self::parseFoldersFromConfig();
                self::saveClassMapToCache();
            }
        } else {
            if (isset($_SERVER['PROJECT_DIR'])) {
                self::emptyCache();
            }
            self::parseFoldersFromConfig();
        }
        
        self::registerAutoloader();
    }
    
    private static function registerAutoloader() {
        spl_autoload_register('LClassLoader::autoload',true);
        
        self::attachComposerInFramework();
        if (isset($_SERVER['PROJECT_DIR'])) {
            self::attachComposerInProject();
        }
    }
    
    private static function attachComposerInFramework() {
        if (is_file($_SERVER['FRAMEWORK_DIR'].'vendor/autoload.php')) {
            require_once($_SERVER['FRAMEWORK_DIR'].'vendor/autoload.php');
        }
    }
    
    private static function attachComposerInProject() {
        if (is_file($_SERVER['PROJECT_DIR'].'vendor/autoload.php')) {
            require_once($_SERVER['PROJECT_DIR'].'vendor/autoload.php');
        }
    }
    
    private static function isSkipCacheRoute() {
        $is_skip_cache_route = in_array($_SERVER['ROUTE'],LConfigReader::simple('/classloader/skip_cache_route_list'));
        $skip_cache_query_parameter = LConfigReader::simple('/classloader/skip_cache_query_parameter');
        if ($skip_cache_query_parameter) {
            $is_skip_cache_route |= strpos($_SERVER['RAW_ROUTE'],$skip_cache_query_parameter)!==false;
        }
        return $is_skip_cache_route;
    }
    
    private static function hasClassMapCache() {
        return is_readable($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path'));
    }
    
    private static function hasCachedClassContent($original_path) {
        $path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder').sha1($original_path).'.php';
        return is_readable($path);
    }
    
    private static function canSaveClassMapToCache() {
        return is_dir(dirname($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path')));
    }
    
    private static function canSaveMangledClassesToCache() {
        return is_dir($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder'));
    }
    
    private static function deleteClassMapCache() {
        $cache_filename = LConfigReader::simple('/classloader/map_cache_file_path');
        if (file_exists($_SERVER['PROJECT_DIR'].$cache_filename)) @unlink($_SERVER['PROJECT_DIR'].$cache_filename);
    }
    
    private static function deleteClassContentCache() {
        $cache_dir = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder');
        if (is_dir($cache_dir)) {
            $elements = scandir($cache_dir);
            foreach ($elements as $el) {
                if ($el!='.' && $el!='..') { @unlink($cache_dir.$el); }
            }
        }
    }
    
    private static function createClassMapCacheDir() {
        $map_path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path');
        if (!self::canSaveClassMapToCache()) {
            mkdir(dirname($map_path),0777,true);
            chmod(dirname($map_path),0777);
        }
    }
    
    private static function createClassContentCacheDir() {
        $content_dir = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder');
        if (!self::canSaveMangledClassesToCache()) {
            mkdir($content_dir,0777,true);
            chmod($content_dir,0777);
        }
    }
    
    private static function saveClassMapToCache() {
        $map_path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path');
        self::createClassMapCacheDir();
        
        $prefix = "<?php \n return ";
        $suffix = ";";
        $content = $prefix.var_export(self::$class_map, true).$suffix;
        
        file_put_contents($map_path, $content);
    }
        
    private static function saveMangledClassToCache($original_path,$content) {
        self::createClassContentCacheDir();
        
        $path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder').sha1($original_path).'.php';
        file_put_contents($path, $content);
    }
    
    private static function loadClassMapFromCache() {
        self::$class_map = include($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path'));
    }
    
    private static function requireCachedClassContent($original_path) {
        $path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder').sha1($original_path).'.php';
        
        require_once($path);
    }
    
    private static function parseFoldersFromConfig() {
        self::parseFrameworkFolders(LConfigReader::simple('/classloader/framework_folder_list'));
        if (isset($_SERVER['PROJECT_DIR'])) {
            self::parseProjectFolders(LConfigReader::simple('/classloader/project_folder_list'));
        }
    }
    
    private static function parseFrameworkFolders(array $folder_list) {
        foreach ($folder_list as $folder) {
            self::recursiveParseFolder($_SERVER['FRAMEWORK_DIR'].$folder);
        }
    }
    
    private static function parseProjectFolders(array $folder_list) {
        foreach ($folder_list as $folder) {
            self::recursiveParseFolder($_SERVER['PROJECT_DIR'].$folder);
        }
    }
    
    private static function isValidCodeFile($full_filename) {
        return is_file($full_filename) && LStringUtils::endsWith($full_filename, LConfigReader::simple('/classloader/code_file_ends_with'));
    }
    
    private static function findPatternInCode($pattern,$code) {
        $matches = [];
        preg_match_all($pattern,$code,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        return $matches;
    }
    
    private static function recursiveParseFolder($full_folder_path) {
        $dir_elements = scandir($full_folder_path);
        
        foreach ($dir_elements as $element) {
            if ($element == '.' || $element == '..') continue;
            $full_path = $full_folder_path.$element;
            if (self::isValidCodeFile($full_path)) {
                $elements = [];
                $code = file_get_contents($full_path);
                $namespace_matches = self::findPatternInCode(self::PATTERN_FIND_NAMESPACES, $code);
                $class_matches = self::findPatternInCode(self::PATTERN_FIND_CLASSES, $code);
                $trait_matches = self::findPatternInCode(self::PATTERN_FIND_TRAITS, $code);
                $interface_matches = self::findPatternInCode(self::PATTERN_FIND_INTERFACES, $code);
                
                foreach ($namespace_matches as $match) {
                    $elements[$match['namespace'][1]] = array('namespace' => $match['namespace'][0]);
                }
                foreach ($class_matches as $match) {
                    $elements[$match['class'][1]] = array('element' => $match['class'][0]);
                }
                foreach ($trait_matches as $match) {
                    $elements[$match['trait'][1]] = array('element' => $match['trait'][0]);
                }
                foreach ($interface_matches as $match) {
                    $elements[$match['interface'][1]] = array('element' => $match['interface'][0]);
                }
                
                ksort($elements);
                $current_namespace = "";
                foreach ($elements as $k => $el) {
                    if (isset($el['namespace'])) $current_namespace = $el['namespace'].'\\';
                    else {
                        $element = $el['element'];
                        if (isset(self::$class_map[$current_namespace.$element])) {
                            throw new \Exception("Error : ".$current_namespace.$element." already defined in ".self::$class_map[$current_namespace.$element]." and found also in ".$full_path);
                        }
                        self::$class_map[$current_namespace.$element] = $full_path;
                    }
                }
                
            }
            if (is_dir($full_path.'/')) {
                self::recursiveParseFolder($full_path.'/');
            }
        }
    }
    
    public static function dump() {
        var_dump(self::$class_map);
    }
    
}