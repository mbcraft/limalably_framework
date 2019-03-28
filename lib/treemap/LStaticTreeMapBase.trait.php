<?php

trait LStaticTreeMapBase {
    
    protected static $tree_map = null;
    
    private static function setupIfNeeded() {
        if (self::$tree_map == null) self::$tree_map = new LTreeMap();
    }    
}
