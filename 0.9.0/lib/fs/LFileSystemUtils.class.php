<?php

/*START-PHP-CONTENT*/

class FileSystemUtils
{
    static function isCurrentDirName($name)
    {
        return $name==".";
    }

    static function isParentDirName($name)
    {
        return $name=="..";
    }

    static function isFile($path)
    {
        $base_folder = isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
        
        if (strpos($path,'/')===0) {
            
        } else {
            $path = $base_folder.$path;
        }
        
        return is_file($path);
    }

    static function isDir($path)
    {
        $base_folder = isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
        
        if (strpos($path,'/')===0) {
            
        } else {
            $path = $base_folder.$path;
        }
        
        return is_dir($path);
    }

    static function getWorkingDirectory()
    {
        return new LDir(getcwd());
    }

    static function setWorkingDirectory($new_dir)
    {
        if ($new_dir instanceof LDir)
            chdir($new_dir->__full_path);
        else
            chdir($new_dir);
    }

    static function getFreeDiskSpace()
    {
        $path = isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
        
        return disk_free_space($path);
    }

    static function getTotalDiskSpace()
    {
        $path = isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
        
        return disk_total_space($path);
    }


}

/*END-PHP-CONTENT*/

?>