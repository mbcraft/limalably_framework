<?php

class LTestException extends Exception {
    
}

class LAssert {
    
    private static $total_assertions = 0;
    
    
    public static function getAssertionsCount() {
        return self::$total_assertions;
    }
        
    private static function success() {
        self::$total_assertions++;
        LOutput::message(".",false);
    }
    
    private static function failure($message) {
        self::$total_assertions++;
        LOutput::error_message($message);
        throw new LTestException();
    }
    
    public function assertFalse($value,$message) {
        if ($value==true) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    public function assertTrue($value,$message) {
        if ($value==false) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    public function assertNotSame($actual,$expected,$message) {
        if ($actual===$expected) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    
    public function assertSame($actual,$expected,$message) {
        if ($actual!==$expected) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    public function assertLessThanOrEqual($actual,$max,$message) {
        if ($actual>$max) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    
    public function assertLessThan($actual,$max,$message) {
        if ($actual>=$max) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    public function assertGreaterThanOrEqual($actual,$min,$message) {
        if ($actual<$min) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    
    public function assertGreaterThan($actual,$min,$message) {
        if ($actual<=$min) {
            self::failure($message);
        } else {
            self::success();
        }
    }
    
    public function assertNotEqual($actual,$expected,$message) {
        if ($actual==$expected) {
            self::failure($message);
        } else
        {
            self::success();
        }
    }
    
    public function assertEqual($actual,$expected,$message) {
        if ($actual!=$expected) {
            self::failure($message);
        } else
        {
            self::success();
        }
    }
    
}