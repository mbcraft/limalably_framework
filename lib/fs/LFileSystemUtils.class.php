<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LFileSystemUtils
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
        $base_folder = isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
        
        if (strpos($path,'/')===0) {
            
        } else {
            $path = $base_folder.$path;
        }
        
        return is_file($path);
    }

    static function isDir(string $path)
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

    static function isPermissionsFlagsValid(string $permissions_flags) {
        if (strlen($permissions_flags)!=10) return false;

        if ($permissions_flags[0]!='-') return false;

        if ($permissions_flags[1]!='-' && $permissions_flags[1]!='r') return false;
        if ($permissions_flags[2]!='-' && $permissions_flags[2]!='w') return false;
        if ($permissions_flags[3]!='-' && $permissions_flags[3]!='x') return false;

        if ($permissions_flags[4]!='-' && $permissions_flags[4]!='r') return false;
        if ($permissions_flags[5]!='-' && $permissions_flags[5]!='w') return false;
        if ($permissions_flags[6]!='-' && $permissions_flags[6]!='x') return false;

        if ($permissions_flags[7]!='-' && $permissions_flags[7]!='r') return false;
        if ($permissions_flags[8]!='-' && $permissions_flags[8]!='w') return false;
        if ($permissions_flags[9]!='-' && $permissions_flags[9]!='x') return false;

        return true;
    }


}

?>