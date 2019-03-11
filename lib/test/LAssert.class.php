<?php

class LTestException extends Exception {
    
}

class LAssert {
    
    private static $total_assertions = 0;
    
    
    public static function getAssertionsCount() {
        return self::$total_assertions;
    }
    
    protected static function output($message) {
        echo "\n".$message."\n";
    }
    
    protected static function exceptionDumpMessage(\Exception $ex) {
        return "File : ".$ex->getFile()."- Code : ".$ex->getCode()." - Line : ".$ex->getLine()." - Message :".$ex->getMessage()." \n Stack Trace : ".$ex->getTraceAsString();
    }
    
    private static function success() {
        self::$total_assertions++;
        LAssert::output(".");
    }
    
    private static function failure($message) {
        self::$total_assertions++;
        LAssert::output($message);
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