<?php

trait LStaticHashMapBase {
    
    protected static $hash_map = null;
    
    private static function setupIfNeeded() {
        if (self::$hash_map == null) self::$hash_map = new LHashMap();
    }    
}
