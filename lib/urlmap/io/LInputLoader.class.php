<?php

class LInputLoader {
    
    function loadDataInTreeMap($load_node,$treemap) {
        foreach ($load_node as $key => $value) {
            
            $treemap->set($key,$value);
            
        }
    }
    
}
