<?php

class LAtlas {
    
    static function db($connection_name='default') {
        
        $connection = LDbConnectionManager::get($connection_name);
        
        return Atlas\Orm\Atlas::new($connection);
    }
    
    
}