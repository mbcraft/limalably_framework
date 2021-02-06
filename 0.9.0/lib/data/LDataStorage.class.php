<?php

class LDataStorage implements LIDataStorage {
    
    private $my_json_storage;
    private $my_xml_storage;
    private $my_ini_storage;
    
    function __construct() {
        $this->my_json_storage = new LJsonDataStorage();
        $this->my_xml_storage = new LXmlDataStorage();
        $this->my_ini_storage = new LIniDataStorage();
        
    }
    
    function isInitialized() {
        return $this->my_json_storage->isInitialized();
    }
    
    function init(string $root_path) {
        $this->my_json_storage->init($root_path);
        $this->my_xml_storage->init($root_path);
        $this->my_ini_storage->init($root_path);
        
    }
    
    function initWithDefaults() {
        $this->my_json_storage->initWithDefaults();
        $this->my_xml_storage->initWithDefaults();
        $this->my_ini_storage->initWithDefaults();
    }
    
    function loadArray(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $current_data = [];
                
        if ($this->my_xml_storage->isSaved($path)) {
            $current_data = array_merge($current_data,$this->my_xml_storage->loadArray($path));
        }
         
        if ($this->my_ini_storage->isSaved($path)) {
            $current_data = array_merge($current_data,$this->my_ini_storage->loadArray($path));
        }
        
        return $current_data;
    }
    
    function load(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $current_data = [];
        
        if ($this->my_json_storage->isSaved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_json_storage->load($path));
        }
        
        if ($this->my_xml_storage->isSaved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_xml_storage->load($path));
        }
         
        if ($this->my_ini_storage->isSaved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_ini_storage->load($path));
        }
        
        return $current_data;

    }
    
    public function isValidFilename($filename) {
        return $this->my_ini_storage->isValidFilename($filename) || $this->my_json_storage->isValidFilename($filename) || $this->my_xml_storage->isValidFilename($filename);
    }
    
    function isSaved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        return $this->my_json_storage->isSaved($path) || $this->my_xml_storage->isSaved($path) || $this->my_ini_storage->isSaved($path);
    }
    
    function save(string $path,array $data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $this->my_json_storage->delete($path);
        $this->my_xml_storage->delete($path);
        $this->my_ini_storage->delete($path);
        
        //salvo sempre in json per comodità
        $this->my_json_storage->save($path,$data);
    }
    
    function delete(string $path) {
        $this->my_json_storage->delete($path);
        $this->my_xml_storage->delete($path);
        $this->my_ini_storage->delete($path);
    }
    
}
