<?php



class LProjectUrlAliasDbListCommand implements LICommand {
	

    private function execute() {
        $this->setCommandAsExecuted();
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $elements = $url_alias_db_utils->listRoutes();
        
        if (empty($elements)) {
            echo "No routes found in url alias db.\n";
            Lymz::finish(0);
        }
        
        echo "Routes found in url alias db : ".count($elements)."\n\n";
        
        foreach ($elements as $k => $el) {
            echo "$k : $el \n";
        }
    }
}