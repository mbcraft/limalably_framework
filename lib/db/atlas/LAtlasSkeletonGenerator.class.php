<?php

use Atlas\Cli\Config;
use Atlas\Cli\Fsio;
use Atlas\Cli\Logger;
use Atlas\Cli\Skeleton;

class LAtlasSkeletonGenerator {

    static function generate($connection_name='default') {

        $database_config = LConfigReader::simple('/database/'.$connection_name);
        
        $input = [
            "pdo" => [
                LDbConnectionManager::getConnectionString($connection_name)
            ],
            'namespace' => $database_config['atlas']['namespace'],
            'directory' => $database_config['atlas']['directory']
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
        
        if (isset($database_config['atlas']['transform'])) {
            $input['transform'] = $database_config['atlas']['transform'];
        }
        
        if (isset($database_config['atlas']['templates'])) {
            $input['templates'] = $database_config['atlas']['templates'];
        }
        
        $command = new Skeleton(
                new Config($input),
                new Fsio(),
                new Logger()
        );

        $code = $command();
        exit($code);
    }

}
