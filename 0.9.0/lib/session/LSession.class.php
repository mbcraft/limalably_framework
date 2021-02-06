<?php

class LSession {
    
    use LStaticTreeMapRead;
    
    protected static $tree_map = null;
    
    protected static $my_view = null;
    
    private static function setupIfNeeded() {
        if (self::$tree_map == null) {
            self::$tree_map = new LTreeMap($_SESSION);
        
            self::$my_view = new LTreeMapView('/',self::$tree_map);
        }
    } 
    
}