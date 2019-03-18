<?php

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
                
                if (is_file($full_path) && strpos($elem,'Test.class.php')===(strlen($elem)-strlen('Test.class.php'))) {
                    self::$test_classes[] = $folder.$elem;
                }
                if (is_dir($full_path.'/')) {
                    self::collect($root_dir,substr($full_path, strlen($root_dir)).'/');
                }
            }
        }
    }
    
    static function run() {
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
        //uso LOutput ...
        //$NL = $_SERVER['ENVIRONMENT'] == 'script' ? "\n" : "<br>";
        LOutput::message('');
        LOutput::message('Unit test summary : ',false);
        LOutput::message(LTestCase::getTestCaseCount().' TEST CASES, '.LTestCase::getTestErrorsCount().' ERRORS, '.LTestCase::getTestMethodsCount().' METHODS, '.LTestCase::getAssertionsCount().' ASSERTIONS, '.LTestCase::getFailuresCount().' FAILURES.');
        LOutput::message('');
        
        $failures_and_exceptions = LTestCase::getCollectedFailuresAndExceptions();
        foreach ($failures_and_exceptions as $ex) {
            if ($ex instanceof LUnitTestException) {
                LOutput::message("Unit Test Exception : ", false);
                LOutput::exception($ex, true);
            }
            elseif ($ex instanceof LTestFailure) {
                LOutput::message("Failure : ", false);
                $ex->printFailure();
            } else {
                LOutput::message("Exception : ",false);
                LOutput::exception($ex, true);
            }
            
            LOutput::message('');
            
        }
        
    }
    
    
}