<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*
Le classi che finiscono con TestLib.class.php sono considerate classi di libreria e vengono caricate per poter eseguire i test .
Le classi dei test devono finire con Test.class.php .

I percorsi che cominciano con underscore (_) vengono saltati. 

*/

class LTestRunner {
    
    static $test_classes = [];
    
    static function clear() {
        self::$test_classes = [];
    }
    
    static function collect($root_dir,$path) {
        
        if (LStringUtils::startsWith($path,'_')) {
            
            echo "Skipping folder '".$path."' as it starts with '_' ...\n";

            return;
        }

        if (LStringUtils::endsWith($path,'/')) {

            $elements = scandir($root_dir.$path); 
            
            foreach ($elements as $elem) {
                if ($elem!='.' && $elem!='..')
                {    
                    $full_path = $root_dir.$path.$elem;
                    
                    if (is_file($full_path) && strpos($elem,'TestLib.class.php')===(strlen($elem)-strlen('TestLib.class.php'))) {
                        require_once ($full_path);
                    }

                    if (is_file($full_path) && strpos($elem,'Test.class.php')===(strlen($elem)-strlen('Test.class.php'))) {
                        self::$test_classes[] = $full_path;
                    }
                    if (is_dir($full_path.'/')) {
                        self::collect($root_dir,substr($full_path, strlen($root_dir)).'/');
                    }
                }
            }

        }

        if (LStringUtils::endsWith($path,'.php')) {

            $full_path = $root_dir.$path;

            if (is_file($full_path) && strpos($full_path,'Test.class.php')===(strlen($full_path)-strlen('Test.class.php'))) {
                self::$test_classes[] = $full_path;
            }
        }
    }
    
    static function run() {
        LExecutionMode::setUnitTesting();

        foreach (self::$test_classes as $test_class) {
            
            require_once($test_class);
            $path_parts = explode('/',$test_class);
            $filename = array_pop($path_parts);
            $filename_parts = explode('.',$filename);
            $class_name = array_shift($filename_parts);
            //run unit tests
            $class_name::run();
            
        }
        self::printSummary();
    }
    
    static function printSummary() {
        
        LResult::messagenl('');
        LResult::message('Unit test summary : ');
        LResult::messagenl(LTestCase::getTestCaseCount().' TEST CASES, '.LTestCase::getTestErrorsCount().' ERRORS, '.LTestCase::getTestMethodsCount().' METHODS, '.LTestCase::getAssertionsCount().' ASSERTIONS, '.LTestCase::getFailuresCount().' FAILURES.');
        LResult::messagenl('');
        
        $failures_and_exceptions = LTestCase::getCollectedFailuresAndExceptions();
        foreach ($failures_and_exceptions as $ex) {
            if ($ex instanceof LUnitTestException) {
                LResult::message("Unit Test Exception : ");
                LResult::exception($ex, true);
            }
            elseif ($ex instanceof LTestFailure) {
                LResult::message("Failure : ");
                $ex->printFailure();
            } else {
                LResult::message("Exception : ");
                LResult::exception($ex, true);
            }
            
            LResult::message('');
            
        }
        
    }
    
    
}