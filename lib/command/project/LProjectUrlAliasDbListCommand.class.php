<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LProjectUrlAliasDbListCommand implements LICommand {
	

    private function execute() {
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $elements = $url_alias_db_utils->listRoutes();
        
        if (empty($elements)) {
            echo "No routes found in url alias db.\n";
            Limalably::finish(0);
        }
        
        echo "Routes found in url alias db : ".count($elements)."\n\n";
        
        foreach ($elements as $k => $el) {
            echo "$k : $el \n";
        }
    }
}