<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LUrlMapBuilder {
    
    private $my_map;
    
    public function __construct() {
        $this->my_map = new LTreeMap();
    }
        
    function setRealUrl($url) {
        $this->my_map->set("/real_url",$url);
    }
    
    function setExtends($extends) {
        $this->my_map->set('/extends',$extends);
    }
    
    function writeToFile($path) {
        $content = LJsonUtils::encodeData('urlmap', $path, $this->my_map->getRoot());
        
        file_put_contents($path, $content);
        
        chmod($path,0777);
    }
    
}