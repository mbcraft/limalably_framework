<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LUploadedFile implements ArrayAccess {

    static function normalizeFileUploads() {

        return self::normalizeArray($_FILES);
    }
    
    static function normalizeArray($data) {
        $t = new LTreeMap($data);
        $result = new LTreeMap();

        foreach ($data as $key => $value) {
            self::normalizeBranch($t, $key, '/', $value['name'] ,$result);
        }
        return $result->getRoot();
    }
    
    private static function normalizeBranch($treemap_data,$starting_part,$current_path,$current_value,$treemap_result) {

        if (is_string($current_value)) {
            $name = $treemap_data->get($starting_part.'/name'.$current_path);
            $type = $treemap_data->get($starting_part.'/type'.$current_path);
            $tmp_name = $treemap_data->get($starting_part.'/tmp_name'.$current_path);
            $error = $treemap_data->get($starting_part.'/error'.$current_path);
            $size = $treemap_data->get($starting_part.'/size'.$current_path);
            
            $uploaded_file = new LUploadedFile($name, $type, $tmp_name, $error, $size);

            $treemap_result->set($starting_part.$current_path,$uploaded_file);
        } else {
            foreach ($current_value as $key => $value) {
                self::normalizeBranch($treemap_data, $starting_part, $current_path.$key.'/', $value, $treemap_result);
            }
        }
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
    
    function moveFileTo($folder,$name=null) {
        
        if ($folder instanceof LDir) $folder = $folder->getPath();

        $final_folder_path = LStringUtils::startsWith($folder, '/') ? $folder : $_SERVER['PROJECT_DIR'].$folder;
        
        if (!LStringUtils::endsWith($final_folder_path, '/')) $final_folder_path = $final_folder_path . '/';
        
        if (!is_dir($final_folder_path)) {
            mkdir($final_folder_path,0777,true);
            chmod($final_folder_path,0777);
        }
        
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

    function getFullExtension() {
        $parts = explode('.',$this->getName());

        unset($parts[0]);

        return '.'.join('.',$parts);
    }

    function getLastExtension() {
        $parts = explode('.',$this->getName());

        return '.'.end($parts);
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

    function getMimeType() {
        return $this->type;
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
