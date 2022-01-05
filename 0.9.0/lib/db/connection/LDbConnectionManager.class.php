<?php

class LDbConnectionManager {
    
    const CONNECTION_TYPE_MYSQL = 'mysql';
    const CONNECTION_TYPE_SQLITE = 'sqlite';
    
    private static $connections = [];
    
    /**
     * Restituisce l'handle di una determinata connessione, come da configurazione.
     * 
     * @param string $connection_name Il nome della connessione
     * @return mixed L'handle per effettuare query al database
     */
    public static function get($connection_name = 'default') {
        
        if (!isset(self::$connections[$connection_name])) {
            self::$connections[$connection_name] = self::createAndOpen($connection_name);    
        } 
        
        return self::$connections[$connection_name]->getHandle();
        
    }
    
    public static function getConnectionString($connection_name = 'default') {
        if (!isset(self::$connections[$connection_name])) {
            self::$connections[$connection_name] = self::createAndOpen($connection_name);    
        }
        
        $params = LConfigReader::simple('/database/'.$connection_name);
        
        return self::$connections[$connection_name]->getConnectionString($params);
    }
    
    private static function createAndOpen($connection_name) {
        
        $params = LConfigReader::simple('/database/'.$connection_name);
        
        $type = $params['type'];
        switch ($type) {
            case self::CONNECTION_TYPE_MYSQL : $conn = new LMysqlConnection($params);break;
            case self::CONNECTION_TYPE_SQLITE : $conn = new LSqliteConnection($params);break;
            
            default : throw new \Exception('Unrecognized connection type : '.$type);
        }
        $conn->open();
        return $conn;
    }
    
    /**
     * Chiude tutte le connessioni al database aperte.
     */
    public static function dispose() {
        
        foreach (self::$connections as $conn) {
            
            if ($conn->isOpen())  {
                $conn->close();
            }
            
        }
        
        self::$connections = [];
    }
    
    
}
