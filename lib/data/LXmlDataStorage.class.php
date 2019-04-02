<?php

class LXmlDataStorage implements LIDataStorage {
    
    private $root_path = null;
    
    public function delete(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.xml';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        if (is_file($my_path1)) @unlink($my_path1);
    }

    public function init(string $root_path) {
        $this->root_path = $root_path;
    }

    public function initWithDefaults() {
        $this->root_path = $_SERVER['PROJECT_DIR'].LConfigReader::simple('/classloader/data_folder');
    }

    public function isInitialized() {
        return $this->root_path!=null;
    }

    public function is_saved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.xml';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        //add xml support
        
        return is_file($my_path1);
    }

    public function load(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.xml';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $dom = new \DOMDocument();
        $dom->load($my_path1);
        
        $result_tree = new LTreeMap();
        
        $root_node = $dom->documentElement;
        
        for ($i = 0 ; $i < $root_node->childNodes->count() ; $i++) {
            $node = $root_node->childNodes->item($i);
            
            if ($node instanceof \DOMElement) {
                $path = $node->getAttribute('path');
                $value = "";
                for ($j = 0 ; $j < $node->childNodes->count(); $j++) {
                    $nested_node = $node->childNodes->item($j);
                    $value .= $nested_node->ownerDocument->saveHTML($nested_node);
                }
            
                $result_tree->set($path,$value);
            }
        }
                
        return $result_tree->getRoot();
    }

    public function save(string $path, array $data) {
        throw new \Exception("Xml data storage save operation is not supported!");
    }

}
