<?php

class LDbConnectionFactory {
    
    const CONNECTION_TYPE_MYSQL = 'mysql';
    const CONNECTION_TYPE_SQLITE = 'sqlite';
    
    public static function get($params) {
        $type = $params['type'];
        switch ($type) {
            case self::CONNECTION_TYPE_MYSQL : return new LMysqlConnection($params);
            case self::CONNECTION_TYPE_SQLITE : return new LSqliteConnection($params);
            default : throw new \Exception('Unrecognized connection type : '.$type);
        }
        
    }
    
    
}
