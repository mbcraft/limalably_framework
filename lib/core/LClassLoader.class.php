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
    
    const PATTERN_FIND_NAMESPACES = "/namespace[ ]+(?<namespace>[a-zA-Z_0-9\\\\]+)[;{ ]+/";
    const PATTERN_FIND_CLASSES = "/class[ ]+(?<class>[a-zA-Z_0-9]+)[{ ]+/";
    const PATTERN_FIND_TRAITS = "/trait[ ]+(?<trait>[a-zA-Z_0-9]+)[{ ]+/";
    const PATTERN_FIND_INTERFACES = "/interface[ ]+(?<interface>[a-zA-Z_0-9]+)[{ ]+/";
    
    private static $class_map = [];
    
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
    
    public static function saveClassMapToFile($filename) {
        $prefix = "<php \n return ";
        $suffix = ";";
        $content = $prefix.var_export(self::$class_map, true).$suffix;
        
        file_put_contents($filename, $content);
    }
    
    public static function loadClassMapFromFile($filename) {
        self::$class_map = include($filename);
    }
    
    public static function parseFolders(array $folder_list) {
        foreach ($folder_list as $folder) {
            self::recursiveParseFolder($_SERVER['PROJECT_DIR'].$folder);
        }
    }
    
    private static function isValidCodeFile($full_filename) {
        return is_file($full_filename) && LStringUtils::endsWith($full_filename, LConfig::mustGet('/defaults/classloader/code_file_ends_with'));
    }
    
    private static function recursiveParseFolder($full_folder_path) {
        $dir_elements = scandir($full_folder_path);
        
        foreach ($dir_elements as $element) {
            if (is_file($full_folder_path.$element))
        }
    }
    
}