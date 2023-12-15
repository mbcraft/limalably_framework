<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LCookie
{
    const DEFAULT_PATH = "/";

    public static function set($key,$value,$expire_days=30)
    {
        $expire_time = time()+60*60*24*$expire_days;
        setcookie($key, $value, $expire_time , self::DEFAULT_PATH);
    }

    public static function isSet($key) {
        return isset($_COOKIE[$key]);
    }
    
    public static function get($key)
    {
        if (!isset($_COOKIE[$key])) throw new \Exception("Can't get value of unset cookie with key : ".$key);
        return $_COOKIE[$key];
    }

    public static function delete($key)
    {
        self::set($key, false);
    }


    public static function getDumpAsHtmlComments()
    {
        $result = "";
        
        foreach (array_keys($_COOKIE) as $key)
        {
            $result .= "<!-- COOKIE : $key = ".$_COOKIE[$key]." -->";
        }
        
        return $result;
    }
}