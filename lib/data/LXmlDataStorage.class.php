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
        
        $this->root_path = LEnvironmentUtils::getBaseDir().LConfigReader::simple('/misc/data_folder');
    }

    public function isInitialized() {
        return $this->root_path!=null;
    }

    public function isValidFilename($filename) {
        return LStringUtils::endsWith($filename, '.xml');
    }
    
    public function isSaved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.'.xml';
        $my_path1 = str_replace('//', '/', $my_path1);
        
        //add xml support
        
        return is_file($my_path1);
    }
    
    public function loadArray(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($path, '.xml')) {
            $my_path1 = $this->root_path.$path;
        }
        else {
            $my_path1 = $this->root_path.$path.'.xml';
        }
        $my_path1 = str_replace('//', '/', $my_path1);
                
        $dom = new \DOMDocument();
        $dom->load($my_path1);
        
        $result = [];
        
        $root_node = $dom->documentElement;
        
        for ($i = 0 ; $i < $root_node->childNodes->length ; $i++) {
            $node = $root_node->childNodes->item($i);
            
            if ($node instanceof \DOMElement) {
                $path = $node->getAttribute('path');
                $value = "";
                for ($j = 0 ; $j < $node->childNodes->length; $j++) {
                    $nested_node = $node->childNodes->item($j);
                    $value .= $nested_node->ownerDocument->saveHTML($nested_node);
                }
            
                $result[$path] = $value;
            }
        }
                
        return $result;
    }

    public function load(string $path) {
        
        $result_array = $this->loadArray($path);
        
        $result_tree = new LTreeMap();
        
        foreach ($result_array as $path => $value) {
            $result_tree->set($path,$value);
        }
                
        return $result_tree->getRoot();
    }

    public function save(string $path, array $data) {
        throw new \Exception("Xml data storage save operation is not supported!");
    }

}
