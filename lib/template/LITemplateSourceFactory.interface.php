<?php


interface LITemplateSourceFactory {
    
    function isTemplateSource(string $string_source);
    
    function isInitialized();
    
    function init(string $root_path);
    
    function initWithDefaults();
    
    function createFileTemplateSource(string $relative_folder_path,string $relative_cache_path);
    
    function createStringArrayTemplateSource(array $data_map,string $relative_cache_path);
    
    function createTemplateFromString(string $template_source);
    
}