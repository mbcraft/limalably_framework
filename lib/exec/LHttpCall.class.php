<?php

class LHttpCall {
    
    static function raw_get($url,$params = array()) {
        if (!empty($params)) {
            $url .= '?';
            $first_param = true;
            foreach ($params as $key => $value) {
                if (!$first_param) $url.= '&';
                $url.= urlencode($key);
                $url.= '=';
                $url.= urlencode($value);
                $first_param = false;
            }
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($ch);
    }
    
    static function raw_post($url,$params = array()) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($ch);
    }
    
    static function get($url,$params = array()) {
        $result = self::raw_get($url, $params);
        
        return LJsonUtils::parseContent('response', $url, $result);
    }
    
    static function post($url,$params = array()) {
        $result = self::raw_post($url, $params);
        
        return LJsonUtils::parseContent('response', $url, $result);
    }
    
    
}
