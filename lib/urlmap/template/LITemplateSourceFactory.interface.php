<?php


interface LITemplateSourceFactory {
    
    function createFileTemplateSource(string $root_path,$cache_path);
    
    function createStringArrayTemplateSource(array $data_map,$cache_path);
    
}