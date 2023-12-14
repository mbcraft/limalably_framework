<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LProjectUrlAliasDbRemoveCommand implements LICommand {

    private function execute() {
        
        if (LParameters::count()!=1) {
            echo "One index of the entry to remove is needed. Use list command to list available url alias db routes.\n";
            return;
        }
                
        $index = LParameters::getByIndex(0);
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $result = $url_alias_db_utils->removeRouteByIndex($index);
        
        echo $result."\n";
    }

}