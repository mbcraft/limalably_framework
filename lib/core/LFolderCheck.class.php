<?php

class LFolderCheck {
    
    private $folder_list = null;
    
    private $spec_list = null;
    
    function __construct($folder_or_list,$spec) {
        if (!is_array($folder_or_list)) {
            $this->folder_list = [$folder_or_list];
        }
        else {
            $this->folder_list = $folder_or_list;
        }
        
        $this->spec_list = explode(',',$spec);
    } 
    
    function getFolderList() {
        return $this->folder_list;
    }
    
    function getSpecList() {
        return $this->spec_list;
    }
    
}
