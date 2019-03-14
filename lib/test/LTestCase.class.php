<?php

class LTestCase extends LAssert {

    private static $total_test_cases = 0;
    private static $total_test_methods = 0;
    private static $total_test_errors = 0;
    private static $total_failures = 0;

    public static function getTestCaseCount() {
        return self::$total_test_cases;
    }

    public static function getTestMethodsCount() {
        return self::$total_test_methods;
    }

    public static function getTestErrorsCount() {
        return self::$total_test_errors;
    }

    public static function getFailuresCount() {
        return self::$total_failures;
    }

    public function setUp() {
        //empty
    }

    public function tearDown() {
        //empty
    }

    private function callTestMethod($method_name) {
        self::$total_test_methods++;
        try {
            $this->setUp();
        } catch (\Exception $ex) {
            self::$total_test_errors++;
            LOutput::error_message("Exception during setUp in test class ".static::class);
        }
        try {
            //echo "Eseguo metodo ".$method_name."\n";
            $this->{$method_name}();
        } catch (\Exception $ex) {
            self::$total_failures++;
            if (!($ex instanceof LTestException)) {
                LOutput::exception($ex,false);
            }
        }
        try {
            $this->tearDown();
        } catch (\Exception $ex) {
            self::$total_test_errors++;
            LOutput::error_message("Exception during tearDown in test class ".static::class);
        }
    }

    public static function run() {
        //echo "Running test ...\n";
        self::$total_test_cases++;
        $clazz_name = static::class;
        $all_methods = get_class_methods($clazz_name);
        
        foreach ($all_methods as $test_method) {
            if (strpos($test_method, 'test') === 0) {
                $class_instance = new $clazz_name();
                $class_instance->callTestMethod($test_method);
            }
        }
    }

}
