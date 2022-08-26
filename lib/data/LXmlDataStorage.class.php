<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LXmlDataStorage extends LAbstractDataStorage implements LIDataStorage {
    
    protected function getFileExtension() {
        return ".xml";
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

    private function recursiveFlatDataIntoTreePath(&$result,$node,$current_node_prefix) {

        foreach ($node as $k => $v) {

            if (is_array($v)) {
                $this->recursiveFlatDataIntoTreePath($result,$v,$current_node_prefix."/".$k);
            } else {
                $result[$current_node_prefix."/".$k] = $v;
            }

        }

    }

    public function save(string $path, array $data) {
        
        $flat_data = [];

        $this->recursiveFlatDataIntoTreePath($flat_data,$data,"");

        $content = '<?xml version="1.0" encoding="utf-8"?>'."\r\n";

        $content .= "<data>\r\n";

        foreach ($flat_data as $k => $v) {
            $content .= '<entry path="'.$k.'">'.$v.'</entry>'."\r\n";
        }

        $content .= "</data>\r\n";
        
        file_put_contents($this->prepareAndGetStorageFilePath($path), $content, LOCK_EX);
    }

}
