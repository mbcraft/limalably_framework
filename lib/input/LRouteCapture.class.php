<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LRouteCapture {
    
    const DOT_REPLACE = '\\.';
    const SLASH_REPLACE = '\\/';
    const STAR_REPLACE = '([a-zA-Z_0-9\\-\\+\\$!\\(\\)]+)';
    const LESS_THAN_REPLACE = '(?<';
    const GREATER_THAN_REPLACE = '>[a-zA-Z_0-9\\-\\+\\$!\\(\\)]+)';
    
    
    function captureParameters($capture_pattern,$route) {
    
        $user_pattern = $capture_pattern;
        if (LStringUtils::startsWith($user_pattern,'/')) 
        {
            $start_from_beginning = true;
            $user_pattern = substr($user_pattern,1);
        } else {
            $start_from_beginning = false;
        }
        if (LStringUtils::startsWith($route, '/')) $route = substr($route,1);
        
        $user_pattern = str_replace('.', self::DOT_REPLACE, $user_pattern);
        $user_pattern = str_replace('/', self::SLASH_REPLACE, $user_pattern);
        $user_pattern = str_replace('*', self::STAR_REPLACE, $user_pattern);
        $user_pattern = str_replace('<', self::LESS_THAN_REPLACE, $user_pattern);
        $user_pattern = str_replace('>', self::GREATER_THAN_REPLACE, $user_pattern);
        if ($start_from_beginning) {
            $user_pattern = '/^'.$user_pattern.'/i'; //begin and end
        } else {
            $user_pattern = '/'.$user_pattern.'$/i'; //begin and end
        }
        try {
            $result = preg_match($user_pattern,$route,$matches);

            if ($result) {
                foreach ($matches as $k => $v)
                {
                    if (is_numeric($k)) unset($matches[$k]);
                }
                return $matches;
            }
            else throw new \Exception("Unable to capture [".$capture_pattern."] data from route [".$route."]");
        } catch (\Exception $ex) {
            throw new \Exception("Capture pattern contains invalid characters : ".$capture_pattern);
        }
    }
    
    
}

