<?php

/**
 * Interface to be implemented for validators
 */
interface LIValidatorDriver {
    
    /**
     * Validate a value, returns an array of error messages or an empty array if all is ok.
     * 
     * @param type $value
     */
    function validate($name,$value,$rules);
    
    
}
