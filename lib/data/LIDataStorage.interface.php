<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LIDataStorage {
    
    function isInitialized();
    
    function init(string $root_path);
    
    function initWithDefaults();
    
    function loadArray(string $path);
    
    function load(string $path);
    
    function isValidFilename($filename);
    
    function isSaved(string $path);
    
    function save(string $path,array $data);
    
    function delete(string $path);
    
}
