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
    
    const PATTERN_FIND_NAMESPACES = "/namespace[ ]+(?<namespace>[a-zA-Z_0-9\\\\]+)[;{ ]+/i";
    const PATTERN_FIND_CLASSES = "/class[ ]+(?<class>[a-zA-Z_0-9]+)[{ ]+/i";
    const PATTERN_FIND_TRAITS = "/trait[ ]+(?<trait>[a-zA-Z_0-9]+)[{ ]+/i";
    const PATTERN_FIND_INTERFACES = "/interface[ ]+(?<interface>[a-zA-Z_0-9]+)[{ ]+/i";
    
    private static $class_map = [];
    
    public static function autoload($clazz) {
        if (LExecutionMode::isTesting() || LExecutionMode::isProduction()) {
            if (isset(self::$class_map[$clazz]))
            {
                if (!self::hasCachedClassContent(self::$class_map[$clazz]))
                {
                    $original_path = self::$class_map[$clazz];
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
        return $class_content;
    }
    
    public static function emptyCache() {
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
            self::emptyCache();
            self::parseFoldersFromConfig();
        }
        
        self::registerAutoloader();
    }
    
    public static function registerAutoloader() {
        spl_autoload_register('LClassLoader::autoload',true);
        
        self::attachComposerInFramework();
        
        self::attachComposerInProject();
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
    
    private static function hasClassMapCache() {
        return is_readable($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path'));
    }
    
    private static function hasCachedClassContent($original_path) {
        $path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder_path').sha1($original_path).'.php';
        return is_readable($path);
    }
    
    private static function canSaveClassMapToCache() {
        return is_dir(dirname($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path')));
    }
    
    private static function canSaveMangledClassesToCache() {
        return is_dir($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder_path'));
    }
    
    private static function deleteClassMapCache() {
        $cache_filename = LConfigReader::simple('/classloader/map_cache_path');
        if (file_exists($_SERVER['PROJECT_DIR'].$cache_filename)) @unlink($_SERVER['PROJECT_DIR'].$cache_filename);
    }
    
    private static function deleteClassContentCache() {
        $cache_dir = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder_path');
        $elements = scandir($cache_dir);
        foreach ($elements as $el) {
            if ($el!='.' && $el!='..') { @unlink($cache_dir.$el); }
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
        $content_dir = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder_path');
        if (!self::canSaveMangledClassesToCache()) {
            mkdir(dirname($content_dir),0777,true);
            chmod(dirname($content_dir),0777);
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
        
        $path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder_path').sha1($original_path).'.php';
        file_put_contents($path, $content);
    }
    
    private static function loadClassMapFromCache() {
        self::$class_map = include($_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/map_cache_file_path'));
    }
    
    private static function requireCachedClassContent($original_path) {
        $path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/class_cache_folder_path').sha1($original_path).'.php';
        
        require_once($path);
    }
    
    public static function parseFoldersFromConfig() {
        self::parseFrameworkFolders(LConfigReader::simple('/classloader/framework_folder_list'));
        self::parseProjectFolders(LConfigReader::simple('/classloader/project_folder_list'));
    }
    
    public static function parseFrameworkFolders(array $folder_list) {
        foreach ($folder_list as $folder) {
            self::recursiveParseFolder($_SERVER['FRAMEWORK_DIR'].$folder);
        }
    }
    
    public static function parseProjectFolders(array $folder_list) {
        foreach ($folder_list as $folder) {
            self::recursiveParseFolder($_SERVER['PROJECT_DIR'].$folder);
        }
    }
    
    private static function isValidCodeFile($full_filename) {
        return is_file($full_filename) && LStringUtils::endsWith($full_filename, LConfig::mustGet('/defaults/classloader/code_file_ends_with'));
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
    
    static function dump() {
        var_dump(self::$class_map);
    }
    
}