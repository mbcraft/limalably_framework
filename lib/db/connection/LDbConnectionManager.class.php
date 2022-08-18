<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LDbConnectionManager {
    
    const CONNECTION_TYPE_MYSQL = 'mysql';
    const CONNECTION_TYPE_SQLITE = 'sqlite';
    
    private static $connections = [];
    
    private static $last_connection_used = null;


    public static function getLastConnectionUsed() {
        return self::$last_connection_used;
    }

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
        
        $result = self::$connections[$connection_name];

        self::$last_connection_used = $result;

        return $result;
        
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
        
        $driver = $params['driver'];
        switch ($driver) {
            case self::CONNECTION_TYPE_MYSQL : $conn = new LMysqlConnection($params);break;
            case self::CONNECTION_TYPE_SQLITE : $conn = new LSqliteConnection($params);break;
            
            default : throw new \Exception('Unrecognized connection driver : '.$driver);
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
