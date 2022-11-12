<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

error_reporting(E_ALL);

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

function ensure_all_strings_or_null($code_place_description,$var_list) {
    if (!is_array($var_list)) throw new \Exception("Invalid array of elements for ensure_all_strings_or_null function");
    foreach ($var_list as $var) {
        if (!is_string($var) && !is_null($var)) throw new \Exception("Invalid string or null found in ".$code_place_description.".");
    }
}

function ensure_string_not_null($code_place_description,$st) {
    if ($st==null) throw new \Exception("String is null in ".$code_place_description." : found ".$st);
    if (!is_string($st)) throw new \Exception("Variable is not a string in ".$code_place_description." : found ".$st);
}

function ensure_all_strings($code_place_description,$var_list) {
    if (!is_array($var_list)) throw new \Exception("Invalid array of elements in ensure_all_strings function");
    foreach ($var_list as $var) {
        if (!is_string($var)) throw new \Exception("Invalid string found in ".$code_place_description.". ".$var." found.");
    }
}

function ensure_all_numbers_or_strings_or_null($code_place_description,$var_list) {
    if (!is_array($var_list)) throw new \Exception("Invalid array of elements in ensure_all_numbers_or_strings_or_null function");
    foreach ($var_list as $var) {
        if (!is_string($var) && !is_numeric($var) && !is_null($var))
            throw new \Exception("Some variable is not a simple numeric type or string or null : ".get_class($var)." was found in ".$code_place_description.".");
    }
}


function ensure_all_numbers_or_strings($code_place_description,$var_list) {
    if (!is_array($var_list)) throw new \Exception("Invalid array of elements in ensure_all_numbers_or_strings function");
    foreach ($var_list as $var) {
        if (!is_string($var) && !is_numeric($var))
            throw new \Exception("Some variable is not a simple numeric type or string. ".($var ? get_class($var) : "null" )." was found in ".$code_place_description.".");
    }
}

function ensure_all_numbers($code_place_description,$var_list) {
    if (!is_array($var_list)) throw new \Exception("Invalid array of elements in ensure_all_numbers_or_strings function");
    foreach ($var_list as $var) {
        if (!is_numeric($var))
            throw new \Exception("Some variable is not a simple numeric type . ".get_class($var)." was found in ".$code_place_description.".");
    }
}

function ensure_instance_of($code_place_description,$var,$class_name_list) {

    if (!is_array($class_name_list)) throw new \Exception("Invalid array of class names for ensure_instance_of function");

    foreach ($class_name_list as $clazz) {
        if ($var instanceof $clazz) return;
    }    

    throw new \Exception("Variable is not an instance of the listed classes : ".implode(',',$class_name_list)." in ".$code_place_description." - found object of class :".get_class($var));
}

function ensure_all_instances_of($code_place_description,$var_list,$class_list) {
    if (!is_array($var_list)) throw new \Exception("Invalid array of elements for ensure_all_instances_of function");
    if (!is_array($class_list)) throw new \Exception("Invalid array of class names for ensure_all_instances_of function");
    foreach ($var_list as $var) {

        ensure_instance_of($code_place_description,$var,$class_list);

    }

}

function array_value_exists($needle,$haystack) {
    foreach ($haystack as $k => $v) {
        if ($needle===$v) return true;
    }
    return false;
}

class LClassLoader {
      
    //patterns
    const PATTERN_FIND_NAMESPACES = "/\n(\s)*namespace(\s)+(?<namespace>[a-zA-Z_0-9\\\\]+)(\s)*[;{ \n]+/i";
    const PATTERN_FIND_CLASSES = "/\n(\s)*(abstract)?(final)?(\s)*class(\s)+(?<class>[a-zA-Z_0-9]+)(\s)*[{ \n]+/i";
    const PATTERN_FIND_TRAITS = "/\n(\s)*trait(\s)+(?<trait>[a-zA-Z_0-9]+)(\s)*[{ \n]+/i";
    const PATTERN_FIND_INTERFACES = "/\n(\s)*interface(\s)+(?<interface>[a-zA-Z_0-9]+)(\s)*[{ \n]+/i";
    
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
    
    private static $init_called = false;

    public static function initCalled() {
        return self::$init_called;
    }

    public static function init() {
        self::$init_called = true;
            
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
        if (!isset($_SERVER['ROUTE']) || !isset($_SERVER['RAW_ROUTE'])) return true;
        
        $is_skip_cache_route = in_array($_SERVER['ROUTE'],LConfigReader::simple('/classloader/skip_cache_route_list')); //ok cerca nei valori
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
        return is_readable($full_filename) && LStringUtils::endsWith($full_filename, LConfigReader::simple('/classloader/code_file_ends_with'));
    }
    
    private static function findPatternInCode($pattern,$code) {
        $matches = [];
        preg_match_all($pattern,$code,$matches,PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        return $matches;
    }
    
    private static function recursiveParseFolder($full_folder_path) {

        if (!is_dir($full_folder_path)) return;

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