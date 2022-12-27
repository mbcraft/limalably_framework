<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

use Atlas\Cli\Config;
use Atlas\Cli\Fsio;
use Atlas\Cli\Logger;
use Atlas\Cli\Skeleton;

class LAtlasSkeletonGenerator {

    static function generate(string $connection_name='default') {

        $db_config_atlas = LConfigReader::simple('/database/'.$connection_name.'/atlas');
        
        $database_config = LConfigReader::simple('/database/'.$connection_name);
        
        $input = [
            "pdo" => [
                LDbConnectionManager::getConnectionString($connection_name)
            ],
            'namespace' => $db_config_atlas['namespace'],
            'directory' => $db_config_atlas['directory']
        ];
        
        if (isset($database_config['username'])) {
            $input['pdo'][] = $database_config['username'];
        } else {
            if (isset($database_config['password'])) throw new \Exception("Username is missing from database configuration!");
        }
        
        if (isset($database_config['password'])) {
            $input['pdo'][] = $database_config['password'];
        } else {
            if (isset($database_config['username'])) throw new \Exception("Password is missing from database configuration");
        }
        
        if (isset($db_config_atlas['transform'])) {
            $input['transform'] = $db_config_atlas['transform'];
        }
        
        if (isset($db_config_atlas['templates'])) {
            $input['templates'] = $db_config_atlas['templates'];
        }
        
        $command = new Skeleton(
                new Config($input),
                new Fsio(),
                new Logger()
        );

        $code = $command();
        Limalably::finish($code);
    }

}
