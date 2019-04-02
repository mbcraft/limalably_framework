<?php

class LDataStorage implements LIDataStorage {
    
    private $my_json_storage;
    private $my_xml_storage;
    private $my_ini_storage;
    
    function __construct() {
        $this->my_json_storage = new LJsonDataStorage();
        /*
        $this->my_xml_storage = new LXmlDataStorage();
        $this->my_ini_storage = new LIniDataStorage();
        */
    }
    
    function isInitialized() {
        return $this->my_json_storage->isInitialized();
    }
    
    function init(string $root_path) {
        $this->my_json_storage->init($root_path);
        /*
        $this->my_xml_storage->init($root_path);
        $this->my_ini_storage->init($root_path);
        */
    }
    
    function initWithDefaults() {
        $this->my_json_storage->initWithDefaults();
        /*
        $this->my_xml_storage->initWithDefaults();
        $this->my_ini_storage->initWithDefaults();
        */
    }
    
    function load(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $current_data = [];
        
        if ($this->my_json_storage->is_saved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_json_storage->load($path));
        }
        /*
        if ($this->my_xml_storage->is_saved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_xml_storage->load($path));
        }
         
        if ($this->my_ini_storage->is_saved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_ini_storage->load($path));
        }
        */
        return $current_data;

    }
    
    function is_saved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        return $this->my_json_storage->is_saved($path); // || $this->my_xml_storage->is_saved($path) || $this->my_ini_storage->is_saved($path);
    }
    
    function save(string $path,array $data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $this->my_json_storage->delete($path);
        //$this->my_xml_storage->delete($path);
        //$this->my_ini_storage->delete($path);
        
        $this->my_json_storage->save($path,$data);
    }
    
    function delete(string $path) {
        $this->my_json_storage->delete($path);
        //$this->my_xml_storage->delete($path);
        //$this->my_ini_storage->delete($path);
    }
    
}
