<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LProjectUrlAliasDbAddCommand implements LICommand {
	

    public function execute() {
        
        if (LParameters::count()!=2) {
            echo "Two parameters needed : the name of the public route and the name of the wanted route. \n";
            return;
        }
        
        $public_route = LParameters::getByIndex(0);
        $wanted_route = LParameters::getByIndex(1);
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $result = $url_alias_db_utils->addRoute($public_route, $wanted_route);
        
        echo $result."\n";
    }

}