<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LHttp
{
    public static function get_to_file($url,$target)
    {
        if (!($target instanceof LFile))
            $target_file = new LFile($target);
        else
            $target_file = $target;
        
        if (!$target_file->exists()) $target_file->touch();
        
        $ch = curl_init($url);
        
        $fw = $target_file->openWriter();
        $handle = $fw->getHandler();
        curl_setopt($ch,CURLOPT_FILE,$handle);
        curl_exec($ch);
        curl_close($ch);
    }
    
    public static function get($url)
    {
        $ch = curl_init($url);
        
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        return $response;
    }

    public static function post_to_file($url,$params,$target) {
        $ch = curl_init($url);
        
        curl_setopt($ch,CURLOPT_POST,true);
        $post_fields = array();
        foreach ($params as $k => $v)
        {
            if ($v instanceof LFile) {
                $post_fields[$k] = curl_file_create($v->getFullPath());
                continue;
            }
            
            $post_fields[$k] = $v;
                
        }
        
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
                
        $fw = $target_file->openWriter();
        $handle = $fw->getHandler();
        curl_setopt($ch,CURLOPT_FILE,$handle);
        curl_exec($ch);
        curl_close($ch);
        
    }
    
    public static function post($url,$params)
    {
        $ch = curl_init($url);
        
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        $post_fields = array();
        foreach ($params as $k => $v)
        {
            if ($v instanceof LFile) {                
                $post_fields[$k] = curl_file_create($v->getFullPath());
                continue;
            }
            
            $post_fields[$k] = $v;
                
        }
        
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
        
        $response = curl_exec($ch);

        curl_close($ch);
        
        return $response;
    }
}

?>