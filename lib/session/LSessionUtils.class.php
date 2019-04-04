<?php

class LSessionUtils {
    
    private static $session_tree = null;
    
    static function currentSessionTree() {
        return self::$session_tree;
    }
    
    static function create() {
        
        $a = session_id();
        if(empty($a)) 
        {
            session_start(['session.use_strict_mode' => true]);
            session_regenerate_id(true);
        }
        
        self::$session_tree = new LTreeMap($_SESSION);
        
        return self::currentSessionTree();
    }
    
    static function destroy() {
        session_destroy();
        self::$session_tree = null;
    }
    
    static function finishedWrite() {
        session_write_close();
    }
    
}
