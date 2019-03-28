<?php

class LRouteCapture {
    
    const DOT_REPLACE = '\\.';
    const SLASH_REPLACE = '\\/';
    const STAR_REPLACE = '([a-zA-Z_0-9\\-\\+\\$!\\(\\)]+)';
    const LESS_THAN_REPLACE = '(?<';
    const GREATER_THAN_REPLACE = '>[a-zA-Z_0-9\\-\\+\\$!\\(\\)]+)';
    
    function captureParametersFromRoute($user_pattern) {
        return $this->captureParameters($user_pattern, $_SERVER['ROUTE']);
    }
    
    function captureParameters($user_pattern,$route) {
    
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
        
        $result = preg_match($user_pattern,$route,$matches);
        
        if ($result) {
            foreach ($matches as $k => $v)
            {
                if (is_numeric($k)) unset($matches[$k]);
            }
            return $matches;
        }
        else return null;
    }
    
    
}

