<?php

class LTwigTemplateSourceFactory implements LITemplateSourceFactory {
    
    public function createFileTemplateSource(string $root_path,string $cache_path) {
        return new LTwigFileTemplateSource($root_path, $cache_path);
    }

    public function createStringArrayTemplateSource(array $data_map,string $cache_path) {
        return new LTwigStringArrayTemplateSource($data_map,$cache_path);
    }

}
