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
            $this->output("Exception during setUp : " . self::exceptionDumpMessage($ex));
        }
        try {
            $this->{$method_name}();
        } catch (\Exception $ex) {
            self::$total_failures++;
            if (!($ex instanceof LTestException)) {
                self::output(self::exceptionDumpMessage($ex));
            }
        }
        try {
            $this->setUp();
        } catch (\Exception $ex) {
            self::$total_test_errors++;
            $this->output("Exception during tearDown : " . self::exceptionDumpMessage($ex));
        }
    }

    public function run() {
        self::$total_test_cases++;
        $clazz_name = get_class();
        $all_methods = get_class_methods($clazz_name);
        foreach ($all_methods as $test_method) {
            if (strpos($test_method, 'test') === 0) {
                $this->callTestMethod($test_method);
            }
        }
    }

}
