<?php

trait LFormatLog {
    static function formatLog($message,$level,$format) {
        $my_date = date('d/m/Y H:i:s');
        
        $format = str_replace('%date', $my_date, $format);
        $format = str_replace('%level', $level, $format);
        $format = str_replace('%user', LEnvironmentUtils::getServerUser(), $format);
        $format = str_replace('%route', LEnvironmentUtils::getRoute(), $format);
        $format = str_replace('%message', $message, $format);
        return $format;
        
        
    }
}
