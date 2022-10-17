<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class DFileSystemUtils
{
    static function isCurrentDirName(string $name)
    {
        return $name==".";
    }

    static function isParentDirName(string $name)
    {
        return $name=="..";
    }

    static function isFile(string $path)
    {
        $base_folder = $_SERVER['DEPLOYER_DIR'];
        
        if (strpos($path,'/')===0) {
            
        } else {
            $path = $base_folder.$path;
        }
        
        return is_file($path);
    }

    static function isDir(string $path)
    {
        $base_folder = $_SERVER['DEPLOYER_DIR'];
        
        if (strpos($path,'/')===0) {
            
        } else {
            $path = $base_folder.$path;
        }
        
        return is_dir($path);
    }

    static function getWorkingDirectory()
    {
        return new DDir(getcwd());
    }

    static function setWorkingDirectory($new_dir)
    {
        if ($new_dir instanceof DDir)
            chdir($new_dir->__full_path);
        else
            chdir($new_dir);
    }

    static function getFreeDiskSpace()
    {
        $path = $_SERVER['DEPLOYER_DIR'];
        
        return disk_free_space($path);
    }

    static function getTotalDiskSpace()
    {
        $path = $_SERVER['DEPLOYER_DIR'];
        
        return disk_total_space($path);
    }


}

?>