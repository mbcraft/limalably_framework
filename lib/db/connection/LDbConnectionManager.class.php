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

        if (!self::$last_connection_used) self::$last_connection_used = self::get();

        return self::$last_connection_used;
    }

    public static function defineConnection(string $connection_name,array $params) {
        if (isset(self::$connections[$connection_name])) throw new \Exception("Connection with name ".$connection_name." is already defined!");

        self::checkConnectionParams($params);

        $driver = $params['driver'];
        switch ($driver) {
            case self::CONNECTION_TYPE_MYSQL : $conn = new LMysqlConnection($params);break;
            case self::CONNECTION_TYPE_SQLITE : $conn = new LSqliteConnection($params);break;
            
            default : throw new \Exception('Unrecognized connection driver : '.$driver);
        }

        self::$connections[$connection_name] = $conn;

        return $conn;
    }

    public static function has($connection_name) {
        
        return LConfigReader::has('/database/'.$connection_name);

    }


    /**
     * Restituisce l'handle di una determinata connessione, come da configurazione.
     * 
     * @param string $connection_name Il nome della connessione
     * @return mixed L'handle per effettuare query al database
     */
    public static function get($connection_name = null) {

        if (!$connection_name) {
            $db_list = LConfigReader::simple('/database');
            if (count($db_list)==1) return self::get(array_keys($db_list)[0]);
            else throw new \Exception("Unable to uniquely determine database connection.");
        }
        
        if (is_object($connection_name)) return $connection_name;

        if (is_string($connection_name)) {

            if (!isset(self::$connections[$connection_name])) {
                self::$connections[$connection_name] = self::loadFromConfig($connection_name);    
            } 
            
            if (!isset(self::$connections[$connection_name])) throw new \Exception("Connection with name '".$connection_name."' is not defined!");

            $result = self::$connections[$connection_name];

            self::$last_connection_used = $result;

            if (!$result->isOpen()) $result->open();

            return $result;

        }

        throw new \Exception("Unable to find suitable connection to use with name : ".$connection_name);
        
    }
    
    public static function getConnectionString(string $connection_name = 'default') {
        if (!isset(self::$connections[$connection_name])) {
            self::$connections[$connection_name] = self::createAndOpen($connection_name);    
        }
        
        $params = LConfigReader::simple('/database/'.$connection_name);
        
        return self::$connections[$connection_name]->getConnectionString($params);
    }
    
    private static function checkConnectionParams(array $params) {
        if (!isset($params['driver'])) throw new \Exception("'host' key is not defined in connection parameters!");
        if (!isset($params['host'])) throw new \Exception("'host' key is not defined in connection parameters!");
        if (!isset($params['username'])) throw new \Exception("'host' key is not defined in connection parameters!");
        if (!isset($params['password'])) throw new \Exception("'host' key is not defined in connection parameters!");
        if (!isset($params['db_name'])) throw new \Exception("'host' key is not defined in connection parameters!");
    }

    private static function loadFromConfig(string $connection_name) {
        
        if (class_exists('LConfigReader')) {
            $params = LConfigReader::simple('/database/'.$connection_name);
        } else 
        throw \Exception("Connection with name '".$connection_name."' is not defined and LConfigReader class is not available!");

        return self::defineConnection($connection_name,$params);   

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
