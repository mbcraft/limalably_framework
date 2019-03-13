<?php

class LEnvironmentUtils {
    
    public static function getServerUser() {
        return isset($_SERVER['USER']) ? $_SERVER['USER'] : $_ENV['APACHE_RUN_USER'];
    }
    
    public static function getRemoteIp() {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }
    
}