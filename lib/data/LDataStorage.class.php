<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


class LDataStorage implements LIDataStorage {
    
    private $my_json_storage;
    private $my_xml_storage;
    private $my_ifs_storage;
    private $my_ini_storage;
    
    function __construct() {
        $this->my_json_storage = new LJsonDataStorage();
        $this->my_xml_storage = new LXmlDataStorage();
        $this->my_ifs_storage = new LIniDatFileDataStorage();
        $this->my_ini_storage = new LIniDataStorage();
    }
    
    function isInitialized() {
        return $this->my_json_storage->isInitialized();
    }
    
    function init(string $root_path) {
        $this->my_json_storage->init($root_path);
        $this->my_xml_storage->init($root_path);
        $this->my_ifs_storage->init($root_path);
        $this->my_ini_storage->init($root_path);
    }
    
    function initWithDefaults() {
        $this->my_json_storage->initWithDefaults();
        $this->my_xml_storage->initWithDefaults();
        $this->my_ifs_storage->initWithDefaults();
        $this->my_ini_storage->initWithDefaults();
    }
    
    function loadArray(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $current_data = [];
         
        if ($this->my_json_storage->isSaved($path)) {
            $current_data = array_merge($current_data,$this->my_json_storage->loadArray($path));
        }

        if ($this->my_xml_storage->isSaved($path)) {
            $current_data = array_merge($current_data,$this->my_xml_storage->loadArray($path));
        }

        if ($this->my_ifs_storage->isSaved($path)) {
            $current_data = array_merge($current_data,$this->my_ifs_storage->loadArray($path));
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
         
        if ($this->my_ifs_storage->isSaved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_ifs_storage->load($path));
        }

        if ($this->my_ini_storage->isSaved($path)) {
            $current_data = array_replace_recursive($current_data,$this->my_ini_storage->load($path));
        }
        
        return $current_data;

    }
    
    public function isValidFilename($filename) {
        return $this->my_ini_storage->isValidFilename($filename) || $this->my_json_storage->isValidFilename($filename) || $this->my_xml_storage->isValidFilename($filename) || $this->my_ifs_storage->isValidFilename($filename);
    }
    
    function isSaved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        return $this->my_json_storage->isSaved($path) || $this->my_xml_storage->isSaved($path) || $this->my_ini_storage->isSaved($path) || $this->my_ifs_storage->isSaved($path);
    }

    private function dataHasNewlines(string $value) {
        return preg_match("/[\r\n]/",$value,$matches)!==0;
    }

    private function dataIsNotTooLong(string $value) {
        return strlen($value)<10000;
    }

    private function dataHasTags(string $value) {
        $matches = [];

        preg_match_all("/(\<(?<closing>\/?)(?<tagname>\w+)\s*(?<autoclose>\/?)\>)/",$value,$matches, PREG_UNMATCHED_AS_NULL);

        $match_count = count($matches[0]);

        return $match_count>0;
    }

    private function dataHasWellFormedTags(string $value) {

        $matches = [];

        preg_match_all("/(\<(?<closing>\/?)(?<tagname>\w+)\s*(?<autoclose>\/?)\>)/",$value,$matches, PREG_UNMATCHED_AS_NULL);

        $match_count = count($matches[0]);

        $tag_stack = [];

        for ($i=0;$i<$match_count;$i++) {
            $tag_name = $matches['tagname'][$i];
            $is_autoclose = $matches['autoclose'][$i]!=null;
            $is_closing = $matches['closing'][$i]!=null;
            if ($is_closing && $is_autoclose) return false;
            $is_begin = !$is_autoclose && !$is_closing;

            if ($is_autoclose) continue;
            if ($is_begin) array_push($tag_stack,$tag_name);
            if ($is_closing) {
                $current_el = array_pop($tag_stack);
                if ($current_el==$tag_name) continue;
                else return false;
            }
        
        }

        if (!empty($tag_stack)) return false;

        return true;

    }
    
    function save(string $path,array $data) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $this->my_json_storage->delete($path);
        $this->my_xml_storage->delete($path);
        $this->my_ifs_storage->delete($path);
        $this->my_ini_storage->delete($path);
        
        //salvo sempre in json per comodità
        $data_without_newlines = [];
        $data_with_newlines = [];

        foreach ($data as $k => $v) {
            if ($this->dataHasNewlines($v)) {
                $data_with_newlines[$k] = $v;
            } else {
                $data_without_newlines[$k] = $v;
            }
        }

        $data_with_wellformed_tags = [];
        $data_other = [];

        foreach ($data_with_newlines as $k => $v) {
            if (
                $this->dataIsNotTooLong($v) && 
                (
                    ($this->dataHasTags($v) && $this->dataHasWellFormedTags($v)) || 
                    (!$this->dataHasTags($v))
                )
            ) {
                $data_with_wellformed_tags[$k] = $v;
            } else {
                $data_other[$k] = $v;
            }
        }

        $this->my_json_storage->save($path,$data_without_newlines);
        $this->my_xml_storage->save($path,$data_with_wellformed_tags);
        $this->my_ifs_storage->save($path,$data_other);
        //ini is not used up to now
    }
    
    function delete(string $path) {
        $this->my_json_storage->delete($path);
        $this->my_xml_storage->delete($path);
        $this->my_ifs_storage->delete($path);
        $this->my_ini_storage->delete($path);
    }
    
}
