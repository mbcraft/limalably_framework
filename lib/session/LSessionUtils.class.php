<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LSessionUtils {
    
    private static $session_tree = null;
    
    public static function currentSessionTree() {
        return self::$session_tree;
    }
    
    public static function create() {
        if (self::$session_tree) throw new \Exception("Session Create was already called!");
        
        $a = session_id();

        $params = [];

        if(empty($a)) 
        {
            $session_params = LConfigReader::executionMode('/session');
            
            foreach ($session_params as $session_param => $value) {
                if ($value !== null) {
                    $params['session.'.$session_param] = $value;
                }
            }
                
            session_start($params);
            
        }
        else {
            session_regenerate_id(true);
            session_start();
        }
        

        self::$session_tree = new LTreeMap($_SESSION);
        
        return self::currentSessionTree();
    }
    
    public static function destroy() {
        session_destroy();
        self::$session_tree = null;
    }
    
    public static function finishedWrite() {
        session_write_close();
    }
    
}
