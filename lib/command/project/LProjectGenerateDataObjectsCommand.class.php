<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LProjectGenerateDataObjectsCommand {
	
    public function execute() {
        
        if (isset($_SERVER['argv'][2])) {
            $connection_name = $_SERVER['argv'][2];
        } else {
            $connection_name = 'default';
        }
        
        LAtlasSkeletonGenerator::generate($connection_name);
        
    }

}