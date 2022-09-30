<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LSession {
    
    use LStaticTreeMapRead;
    use LStaticTreeMapWrite;

    protected static $tree_map = null;
    
    protected static $my_view = null;
    
    private static function setupIfNeeded() {
        if (self::$tree_map == null) {
            self::$tree_map = new LTreeMap($_SESSION);
        
            self::$my_view = new LTreeMapView('/',self::$tree_map);
        }
    } 
    
}
