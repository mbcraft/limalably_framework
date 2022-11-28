<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LTestFailure extends \Exception {
    public function printFailure() {
        $stack_trace = $this->getTrace();
        
        LResult::messagenl($this->getMessage());
        
        $line = $stack_trace[1]['line'];
        $file = $stack_trace[1]['file'];
        $function = $stack_trace[2]['function'];
        
        LResult::messagenl('File : '.$file);
        LResult::messagenl("Function : ".$function." - Line : ".$line);
        
    }
}

class LAssert {
    
    private static $total_assertions = 0;
    
    
    public static function getAssertionsCount() {
        return self::$total_assertions;
    }
        
    private static function success() {
        self::$total_assertions++;
        LResult::message(".");
    }
    
    private static function failure($message) {
        self::$total_assertions++;
        
        throw new LTestFailure($message);
    }

    private function getValueAsString($value) {
        if ($value===true) return "true";
        if ($value===false) return "false";
        if (is_string($value)) return $value;
        if (is_numeric($value)) return $value;
        if (is_array($value)) return var_export($value,true);
        if (get_class($value)!=null) {
            $class = get_class($value);
            return "Object of class ".$class;
        }
    }
    
    public function assertFalse($value,$message) {
        if ($value==true) {
            self::failure($message." - Value:'".$this->getValueAsString($value)."'");
        } else {
            self::success();
        }
    }
    
    public function assertTrue($value,$message) {
        if ($value==false) {
            self::failure($message." - Value:'".$this->getValueAsString($value)."'");
        } else {
            self::success();
        }
    }

    protected $expectedException = null;

    public function expectException(string $class_name) {
        $this->expectedException = $class_name;
    }
    
    public function assertNotSame($actual,$expected,$message) {
        if ($actual===$expected) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Expected:'".$this->getValueAsString($expected)."'");
        } else {
            self::success();
        }
    }
    
    
    public function assertSame($actual,$expected,$message) {
        if ($actual!==$expected) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Expected:'".$this->getValueAsString($expected)."'");
        } else {
            self::success();
        }
    }
    
    public function assertLessThanOrEqual($actual,$max,$message) {
        if ($actual>$max) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Max:'".$this->getValueAsString($max)."'");
        } else {
            self::success();
        }
    }
    
    
    public function assertLessThan($actual,$max,$message) {
        if ($actual>=$max) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Max:'".$this->getValueAsString($max)."'");
        } else {
            self::success();
        }
    }
    
    public function assertGreaterThanOrEqual($actual,$min,$message) {
        if ($actual<$min) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Min:'".$this->getValueAsString($min)."'");
        } else {
            self::success();
        }
    }
    
    
    public function assertGreaterThan($actual,$min,$message) {
        if ($actual<=$min) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Min:'".$this->getValueAsSting($min)."'");
        } else {
            self::success();
        }
    }
    
    public function assertNotEqual($actual,$expected,$message) {
        if ($actual==$expected) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Expected:'".$this->getValueAsString($expected)."'");
        } else
        {
            self::success();
        }
    }
    
    public function assertEqual($actual,$expected,$message) {
        if ($actual!=$expected) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."' - Expected:'".$this->getValueAsString($expected)."'");
        } else
        {
            self::success();
        }
    }
    
    public function assertNull($actual,$message) {
        if ($actual!=null) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."'");
        } else
        {
            self::success();
        }
    }
    
    public function assertNotNull($actual,$message) {
        if ($actual==null) {
            self::failure($message." - Actual:'".$this->getValueAsString($actual)."'");
        } else
        {
            self::success();
        }
    }
    
    public function fail($message) {
        self::failure($message);
    }
    
}