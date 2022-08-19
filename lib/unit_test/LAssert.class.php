<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTestFailure extends \Exception {
    public function printFailure() {
        $stack_trace = $this->getTrace();
        
        LResult::message($this->getMessage());
        
        $line = $stack_trace[1]['line'];
        $file = $stack_trace[1]['file'];
        $function = $stack_trace[2]['function'];
        
        LResult::message('File : '.$file);
        LResult::message("Function : ".$function." - Line : ".$line);
        
    }
}

class LAssert {
    
    private static $total_assertions = 0;
    
    
    public static function getAssertionsCount() {
        return self::$total_assertions;
    }
        
    private static function success() {
        self::$total_assertions++;
        LResult::message(".",false);
    }
    
    private static function failure($message) {
        self::$total_assertions++;
        
        throw new LTestFailure($message);
    }
    
    public function assertFalse($value,$message) {
        if ($value==true) {
            self::failure($message." - Value:'".$value."'");
        } else {
            self::success();
        }
    }
    
    public function assertTrue($value,$message) {
        if ($value==false) {
            self::failure($message." - Value:'".$value."'");
        } else {
            self::success();
        }
    }
    
    public function assertNotSame($actual,$expected,$message) {
        if ($actual===$expected) {
            self::failure($message." - Actual:'".$actual."' - Expected:'".$expected."'");
        } else {
            self::success();
        }
    }
    
    
    public function assertSame($actual,$expected,$message) {
        if ($actual!==$expected) {
            self::failure($message." - Actual:'".$actual."' - Expected:'".$expected."'");
        } else {
            self::success();
        }
    }
    
    public function assertLessThanOrEqual($actual,$max,$message) {
        if ($actual>$max) {
            self::failure($message." - Actual:'".$actual."' - Max:'".$max."'");
        } else {
            self::success();
        }
    }
    
    
    public function assertLessThan($actual,$max,$message) {
        if ($actual>=$max) {
            self::failure($message." - Actual:'".$actual."' - Max:'".$max."'");
        } else {
            self::success();
        }
    }
    
    public function assertGreaterThanOrEqual($actual,$min,$message) {
        if ($actual<$min) {
            self::failure($message." - Actual:'".$actual."' - Min:'".$min."'");
        } else {
            self::success();
        }
    }
    
    
    public function assertGreaterThan($actual,$min,$message) {
        if ($actual<=$min) {
            self::failure($message." - Actual:'".$actual."' - Min:'".$min."'");
        } else {
            self::success();
        }
    }
    
    public function assertNotEqual($actual,$expected,$message) {
        if ($actual==$expected) {
            self::failure($message." - Actual:'".$actual."' - Expected:'".$expected."'");
        } else
        {
            self::success();
        }
    }
    
    public function assertEqual($actual,$expected,$message) {
        if ($actual!=$expected) {
            self::failure($message." - Actual:'".$actual."' - Expected:'".$expected."'");
        } else
        {
            self::success();
        }
    }
    
    public function assertNull($actual,$message) {
        if ($actual!=null) {
            self::failure($message." - Actual:'".$actual."'");
        } else
        {
            self::success();
        }
    }
    
    public function assertNotNull($actual,$message) {
        if ($actual==null) {
            self::failure($message." - Actual:'".$actual."'");
        } else
        {
            self::success();
        }
    }
    
    public function fail($message) {
        self::failure($message);
    }
    
}