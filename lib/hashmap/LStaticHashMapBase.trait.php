<?php

trait LStaticHashMapBase {
    
    protected static $data = [];
    
    public static function path_tokens($path)
    {
        $path_parts = explode("/",$path);
        
        $result = array();
        
        foreach ($path_parts as $p)
        {
            if ($p!==null && $p!=="")
                $result[] = $p;
        }
        return $result;
    }
    
    public static function all_but_last_path_tokens($path)
    {
        $path_tokens = self::path_tokens($path);
        return array_splice($path_tokens, 0, count($path_tokens)-1);
    }
    
    public static function last_path_token($path)
    {
        $path_tokens = self::path_tokens($path);
        return $path_tokens[count($path_tokens)-1];
    }
    
}
