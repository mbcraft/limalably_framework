<?php

interface LIDataStorage {
    
    function isInitialized();
    
    function init(string $root_path);
    
    function initWithDefaults();
    
    function load(string $path);
    
    function is_saved(string $path);
    
    function save(string $path,array $data);
    
    function delete(string $path);
    
}
