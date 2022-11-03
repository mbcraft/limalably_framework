<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTestRunner {
    
    static $test_classes = [];
    
    static function clear() {
        self::$test_classes = [];
    }
    
    static function collect($root_dir,$folder) {
        
        $elements = scandir($root_dir.$folder); 
        
        foreach ($elements as $elem) {
            if ($elem!='.' && $elem!='..')
            {    
                $full_path = $root_dir.$folder.$elem;
                
                if (is_file($full_path) && strpos($elem,'TestLib.class.php')===(strlen($elem)-strlen('TestLib.class.php'))) {
                    require_once ($full_path);
                }

                if (is_file($full_path) && strpos($elem,'Test.class.php')===(strlen($elem)-strlen('Test.class.php'))) {
                    self::$test_classes[] = $root_dir.$folder.$elem;
                }
                if (is_dir($full_path.'/')) {
                    self::collect($root_dir,substr($full_path, strlen($root_dir)).'/');
                }
            }
        }
    }
    
    static function run() {
        LExecutionMode::setUnitTesting();

        foreach (self::$test_classes as $test_class) {
            //echo "Test class  : ".$test_class."\n";
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
        //uso LResult ...
        //$NL = $_SERVER['ENVIRONMENT'] == 'script' ? "\n" : "<br>";
        LResult::message('');
        LResult::message('Unit test summary : ',false);
        LResult::message(LTestCase::getTestCaseCount().' TEST CASES, '.LTestCase::getTestErrorsCount().' ERRORS, '.LTestCase::getTestMethodsCount().' METHODS, '.LTestCase::getAssertionsCount().' ASSERTIONS, '.LTestCase::getFailuresCount().' FAILURES.');
        LResult::message('');
        
        $failures_and_exceptions = LTestCase::getCollectedFailuresAndExceptions();
        foreach ($failures_and_exceptions as $ex) {
            if ($ex instanceof LUnitTestException) {
                LResult::message("Unit Test Exception : ", false);
                LResult::exception($ex, true);
            }
            elseif ($ex instanceof LTestFailure) {
                LResult::message("Failure : ", false);
                $ex->printFailure();
            } else {
                LResult::message("Exception : ",false);
                LResult::exception($ex, true);
            }
            
            LResult::message('');
            
        }
        
    }
    
    
}