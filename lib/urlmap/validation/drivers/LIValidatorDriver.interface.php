<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/**
 * Interface to be implemented for validators
 */
interface LIValidatorDriver {
    
    /**
     * Validate a value, returns an array of error messages or an empty array if all is ok.
     * 
     * @param type $value
     */
    function validate(string $name,$value,$rules,$input_map,$session_map);
    
    
}
