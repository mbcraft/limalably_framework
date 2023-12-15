<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LHost
{
    static function current()
    {
        return isset($_SERVER["HTTP_HOST"]) ? $_SERVER['HTTP_HOST'] : false;
    }

    static function current_no_www()
    {
        $current_host = self::current();
        if (strpos($current_host,"www.")===0)
            return substr($current_host,4);
        else
            return $current_host;
    }
    
    static function isRemote()
    {
        return !LHost::isLocal();
    }
    
    static function isLocal()
    {
        $current_host = LHost::current();
        if (!$current_host) return true;
        if (strstr($current_host,".")===false)
            return true;
        else 
            return false;
    }
}