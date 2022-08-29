<?php



class LIniDatFileDataStorage extends LAbstractDataStorage {
	protected function getFileExtension() {
        return ".ifs";
    }
    
    public function loadArray(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        if (LStringUtils::endsWith($path, '.ifs')) {
            $my_path1 = $this->root_path.$path;
        }
        else {
            $my_path1 = $this->root_path.$path.'.ifs';
        }
        $my_path1 = str_replace('//', '/', $my_path1);
        
        $result = parse_ini_file($my_path1, false, INI_SCANNER_RAW);
        
        if ($result===false) LErrorList::saveFromErrors ('ifs', "Error parsing ifs file : ".$my_path1.". The data is not valid. Use \" to delimit strings.");

        $my_dat_path = $this->root_path.'dat/';

        $final_result = [];

        foreach ($result as $k => $v) {
        	$final_result [$k] = file_get_contents($my_dat_path.$path.'_'.$v.'.dat');
        }
        
        return $final_result;
    }

    public function load(string $path) {
        $result_array = $this->loadArray($path);
        
        $result_tree = new LTreeMap();
        
        foreach ($result_array as $key => $value) {
            $result_tree->set($key, $value);
        }
        
        return $result_tree->getRoot();
    }

    function delete(string $path) {
        if (!$this->isInitialized()) $this->initWithDefaults ();
        
        $my_ifs_path = $this->root_path.$path.$this->getFileExtension();
        $my_ifs_path = str_replace('//', '/', $my_ifs_path);
        
        $my_dat_path = $this->root_path.'dat/';

        if (is_file($my_ifs_path)) {
        	@unlink($my_ifs_path);

        	$file_list = scandir($my_dat_path);

        	foreach ($file_list as $filename) {
        		if ($filename != '.' && $filename != '..') {
        			if (strpos($filename,$path.'_')!==false) {
        				@unlink($my_dat_path.$filename);
        			}
        		}
        	}
        }
    }

    public function save(string $path, array $data) {
        
    	$ifs_path = $this->prepareAndGetStorageFilePath($path);

    	$my_dat_path = $this->root_path.'dat/';

        if (!is_dir($my_dat_path)) {
            mkdir($my_dat_path, 0777, true);
            chmod($my_dat_path, 0777);
        }

        $ini_lines = [];

        $my_data = [];

        $this->recursiveFlatDataIntoTreePath($my_data,$data,"");
        $count = 0;
        foreach ($my_data as $k => $v) {
        	$count ++;
            $ini_lines [] = $k." = ".$count."\n";
            $dat_path = $my_dat_path.$path.'_'.$count.'.dat';
            file_put_contents($dat_path,$v,LOCK_EX);
        }

        $content = implode("",$ini_lines);

        file_put_contents($ifs_path, $content, LOCK_EX);
    }
}