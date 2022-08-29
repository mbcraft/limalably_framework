<?php


abstract class LAbstractDataStorage {

	protected $root_path = null;
	
	public function init(string $root_path) {
        $this->root_path = $root_path;
    }

    public function initWithDefaults() {
        if (class_exists('LEnvironmentUtils')) {
            $this->root_path = LEnvironmentUtils::getBaseDir().LConfigReader::simple('/misc/data_folder');
        } else {
            if (class_exists('LErrorList')) {
                LErrorList::saveFromErrors("Class LEnvironmentUtils was not available.");
            } else throw new \Exception("Class LEnvironmentUtils is not available, the method does not work!");
        }
    }

    public function isInitialized() {
        return $this->root_path!=null;
    }

    public function isValidFilename($filename) {
        return LStringUtils::endsWith($filename, $this->getFileExtension());
    }
    
    public function isSaved(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.$this->getFileExtension();
        $my_path1 = str_replace('//', '/', $my_path1);
        
        return is_file($my_path1);
    }

    function delete(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_path1 = $this->root_path.$path.$this->getFileExtension();
        $my_path1 = str_replace('//', '/', $my_path1);
        
        if (is_file($my_path1)) @unlink($my_path1);
    }

    protected function prepareAndGetStorageFilePath(string $path) {
    	if (!$this->isInitialized()) $this->initWithDefaults ();
    	
        $my_path1 = $this->root_path.$path.$this->getFileExtension();
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $my_dir = dirname($my_path1);
        if (!is_dir($my_dir)) {
            mkdir($my_dir, 0777, true);
            chmod($my_dir, 0777);
        }

        return $my_path1;
    }

    protected function recursiveFlatDataIntoTreePath(&$result,$node,$current_node_prefix) {

        foreach ($node as $k => $v) {

            if (is_array($v)) {
                $this->recursiveFlatDataIntoTreePath($result,$v,$current_node_prefix."/".$k);
            } else {
                $result[$current_node_prefix."/".$k] = $v;
            }

        }

    }

    protected abstract function getFileExtension();

} 