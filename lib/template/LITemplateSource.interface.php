<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LITemplateSource {
    
    function hasRootFolder();

    function getRootFolder();

    function searchTemplate($path);
    
    function getTemplate($path);
    
}
