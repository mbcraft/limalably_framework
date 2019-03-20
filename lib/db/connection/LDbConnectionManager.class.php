<?php

class LDbConnectionManager {
    
    const CONNECTION_TYPE_MYSQL = 'mysql';
    const CONNECTION_TYPE_SQLITE = 'sqlite';
    
    private static $connections = [];
    
    public static function get($connection_name = 'default') {
        
        if (!isset(self::$connections[$connection_name])) {
            self::$connections[$connection_name] = self::createAndOpen($connection_name);    
        } 
        
        return self::$connections[$connection_name]->getHandle();
        
    }
    
    private static function createAndOpen($connection_name) {
        $params = LConfig::view('/database/'.$connection_name);
        $type = $params['type'];
        switch ($type) {
            case self::CONNECTION_TYPE_MYSQL : $conn = new LMysqlConnection($params);break;
            case self::CONNECTION_TYPE_SQLITE : $conn = new LSqliteConnection($params);break;
            default : throw new \Exception('Unrecognized connection type : '.$type);
        }
        $conn->open();
        return $conn;
    }
    
    public static function dispose() {
        foreach (self::$connections as $conn) {
            if ($conn->isOpen()) $conn->close();
        }
        self::$connections = [];
    }
    
    
}
