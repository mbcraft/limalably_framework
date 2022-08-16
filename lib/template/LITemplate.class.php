<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LITemplate {
    
    function render(array $params);  
    
    function getImplementationObject();
    
}
