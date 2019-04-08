<?php

class LUploadedFile implements ArrayAccess {

    static function fix_file_array($data) {
        $result = array();
        foreach ($data as $key1 => $value1)
            foreach ($value1 as $key2 => $value2)
                $result[$key2][$key1] = $value2;
        return $result;
    }

    static function isFileUpload($array_data) {
        return isset($array_data['name']) && isset($array_data['type']) && isset($array_data['tmp_name']) && isset($array_data['error']) && isset($array_data['size']) && $array_data['name'] && $array_data['size'];
    }

    private $name, $type, $tmp_name, $error, $size;

    function __construct($name, $type, $tmp_name, $error, $size) {
        $this->name = $name;
        $this->type = $type;
        $this->tmp_name = $tmp_name;
        $this->error = $error;
        $this->size = $size;
    }
    
    function hasUploadedFile() {
        return $this->error != UPLOAD_ERR_NO_FILE;
    }
    
    function hasError() {
        return $this->error != UPLOAD_ERR_OK;
    }

    function getErrorString() {

        switch ($this->error) {

            case UPLOAD_ERR_OK: return "There is no error, the file uploaded with success.";
            case UPLOAD_ERR_INI_SIZE: return "The uploaded file exceeds the upload_max_filesize directive in php.ini.";
            case UPLOAD_ERR_FORM_SIZE: return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.";
            case UPLOAD_ERR_PARTIAL: return "The uploaded file was only partially uploaded.";
            case UPLOAD_ERR_NO_FILE : return "No file was uploaded.";
            case UPLOAD_ERR_NO_TMP_DIR: return "Missing a temporary folder.";
            case UPLOAD_ERR_CANT_WRITE: return "Failed to write file to disk.";
            case UPLOAD_ERR_EXTENSION: return "A PHP extension stopped the file upload.";

            default : throw new \Exception("Unknown file upload error code.");
        }
    }
    
    function saveFileTo($folder,$name=null) {
        
        $final_folder_path = LStringUtils::startsWith($folder, '/') ? $folder : $_SERVER['PROJECT_DIR'].$folder;
        
        if (!LStringUtils::endsWith($final_folder_path, '/')) $final_folder_path = $final_folder_path . '/';
        
        if (!is_writable($final_folder_path)) throw new \Exception("Can't save file into folder : ".$final_path);
        
        if ($name) {
            $file_path = $final_folder_path.$name;
        } else {
            $file_path = $final_folder_path.$this->name;
        }
        return move_uploaded_file($this->tmp_name, $file_path);
    }

    function getName() {
        return $this->name;
    }

    function getTmpName() {
        return $this->tmp_name;
    }

    function getErrorCode() {
        return $this->error;
    }

    function getSize() {
        return $this->size;
    }

    public function offsetExists($offset): bool {
        if ($offset == 'name' || $offset == 'type' || $offset == 'tmp_name' || $offset == 'error' || $offset == 'size')
            return true;
        else
            return false;
    }

    public function offsetGet($offset) {
        switch ($offset) {
            case 'name' : return $this->name;
            case 'type' : return $this->type;
            case 'tmp_name' : return $this->tmp_name;
            case 'error' : return $this->error;
            case 'size' : return $this->size;
            default : throw new \Exception("Unable to get " . $offset . " from file upload object.");
        }
    }

    public function offsetSet($offset, $value): void {
        throw new \Exception("Can't set " . $offset . " in file upload object : read only data.");
    }

    public function offsetUnset($offset): void {
        throw new \Exception("Can't unset " . $offset . " in file upload object : read only data.");
    }

}
