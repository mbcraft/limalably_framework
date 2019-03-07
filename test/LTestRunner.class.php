<?php

class LTestRunner {
    
    static $test_classes = [];
    
    static function clear() {
        self::$test_classes = [];
    }
    
    static function collect($folder) {
        
        $elements = scandir($_SERVER['PROJECT_DIR'].$folder); 
        
        foreach ($elements as $elem) {
            if ($elem!='.' && $elem!='..')
            {    
                $full_path = $_SERVER['PROJECT_DIR'].$folder.$elem;
                
                if (is_file($full_path) && strpos($elem,'Test.class.php')===(strlen($elem)-strlen('Test.class.php'))) {
                    self::$test_classes[] = $folder.$elem;
                }
                if (is_dir($full_path.'/')) {
                    self::collect(substr($full_path, strlen($_SERVER['PROJECT_DIR'])).'/');
                }
            }
        }
    }
    
    static function run() {
        foreach (self::$test_classes as $test_class) {
            echo "Test class  : ".$test_class."\n";
            require_once($test_class);
            $path_parts = explode('/',$test_class);
            $filename = array_pop($path_parts);
            $filename_parts = explode('.',$filename);
            $class_name = array_shift($filename_parts);
            //run unit tests
            $class_name::run();
            
        }
    }
    
    static function printSummary() {
        $NL = $_SERVER['ENVIRONMENT'] == 'script' ? "\n" : "<br>";
    }
    
    
}