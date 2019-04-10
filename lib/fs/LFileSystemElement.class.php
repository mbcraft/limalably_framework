<?php

abstract class LFileSystemElement
{
    protected $__full_path;
    protected $__path;
    
    private static $defaultPermissionsRwx = "-rwxrwxrwx";

    public static function toOctalPermissions($rwx_permissions)
    {
        $mode = 00;
        if ($rwx_permissions[1] == 'r') $mode += 0400;
        if ($rwx_permissions[2] == 'w') $mode += 0200;
        if ($rwx_permissions[3] == 'x') $mode += 0100;
        else if ($rwx_permissions[3] == 's') $mode += 04100;
        else if ($rwx_permissions[3] == 'S') $mode += 04000;

        if ($rwx_permissions[4] == 'r') $mode += 040;
        if ($rwx_permissions[5] == 'w') $mode += 020;
        if ($rwx_permissions[6] == 'x') $mode += 010;
        else if ($rwx_permissions[6] == 's') $mode += 02010;
        else if ($rwx_permissions[6] == 'S') $mode += 02000;

        if ($rwx_permissions[7] == 'r') $mode += 04;
        if ($rwx_permissions[8] == 'w') $mode += 02;
        if ($rwx_permissions[9] == 'x') $mode += 01;
        else if ($rwx_permissions[9] == 't') $mode += 01001;
        else if ($rwx_permissions[9] == 'T') $mode += 01000;

        return $mode;
    }

    public static function toRwxPermissions($octal_permissions)
    {
        if (($octal_permissions & 0xC000) == 0xC000) {
            // Socket
            $info = 's';
        } elseif (($octal_permissions & 0xA000) == 0xA000) {
            // Symbolic Link
            $info = 'l';
        } elseif (($octal_permissions & 0x8000) == 0x8000) {
            // Regular
            $info = '-';
        } elseif (($octal_permissions & 0x6000) == 0x6000) {
            // Block special
            $info = 'b';
        } elseif (($octal_permissions & 0x4000) == 0x4000) {
            // Directory
            $info = 'd';
        } elseif (($octal_permissions & 0x2000) == 0x2000) {
            // Character special
            $info = 'c';
        } elseif (($octal_permissions & 0x1000) == 0x1000) {
            // FIFO pipe
            $info = 'p';
        } else {
            // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($octal_permissions & 0x0100) ? 'r' : '-');
        $info .= (($octal_permissions & 0x0080) ? 'w' : '-');
        $info .= (($octal_permissions & 0x0040) ?
                    (($octal_permissions & 0x0800) ? 's' : 'x' ) :
                    (($octal_permissions & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($octal_permissions & 0x0020) ? 'r' : '-');
        $info .= (($octal_permissions & 0x0010) ? 'w' : '-');
        $info .= (($octal_permissions & 0x0008) ?
                    (($octal_permissions & 0x0400) ? 's' : 'x' ) :
                    (($octal_permissions & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($octal_permissions & 0x0004) ? 'r' : '-');
        $info .= (($octal_permissions & 0x0002) ? 'w' : '-');
        $info .= (($octal_permissions & 0x0001) ?
                    (($octal_permissions & 0x0200) ? 't' : 'x' ) :
                    (($octal_permissions & 0x0200) ? 'T' : '-'));

        return $info;
    }

    public static function setDefaultPermissionsOctal($perms)
    {
        self::$defaultPermissionsRwx = self::toRwxPermissions($perms);
    }

    public static function getDefaultPermissionsOctal()
    {
        return self::toOctalPermissions(self::$defaultPermissionsRwx);
    }

    public static function setDefaultPermissionsRwx($perms)
    {
        self::$defaultPermissionsRwx = $perms;
    }

    public static function getDefaultPermissionsRwx()
    {
        return self::$defaultPermissionsRwx;
    }
    
    protected function __construct($path)
    {
        //SAFETY NET, rimuovo tutti i .. all'interno del percorso.
        $path = str_replace('/..', "", $path);
        //pulizia doppie barre dai percorsi
        $path = str_replace("//", "/", $path);
        
        $this->__path = $path;
        
        if (strpos($path,'/')===0) {
            $this->__full_path = $path;
        } else {
            $base_folder = isset($_SERVER['PROJECT_DIR']) ? $_SERVER['PROJECT_DIR'] : $_SERVER['FRAMEWORK_DIR'];
            $this->__full_path = $base_folder.$path;
        }
    }

    function equals($file_or_dir)
    {
        if ($file_or_dir instanceof LFileSystemElement)
            return $this->getFullPath() == $file_or_dir->getFullPath();
        else 
            return false;
    }
    
    function isDir()
    {
        return is_dir($this->__full_path);
    }

    function isFile()
    {
        return is_file($this->__full_path);
    }

    function exists()
    {
        return file_exists($this->__full_path);
    }

    function getLastAccessTime()
    {
        return fileatime($this->__full_path);
    }

    function getModificationTime()
    {
        return filemtime($this->__full_path);
    }

    function setPermissions($rwx_permissions)
    {
        $octal_permissions = self::toOctalPermissions($rwx_permissions);

        chmod($this->__full_path, $octal_permissions);
    }

    function hasPermissions($rwx_permissions)
    {
        $current_perms = $this->getPermissions();

        for ($i=0;$i<strlen($current_perms);$i++)
        {
            if ($rwx_permissions[$i]!=="-")
                if ($rwx_permissions[$i]!==$current_perms[$i])
                    return false;
        }
        return true;
    }
    
    function getPermissions()
    {
        $perms = fileperms($this->__full_path);

        return self::toRwxPermissions($perms);
    }

    /*
     * Rinomina l'elemento lasciando invariata la sua posizione (cartella padre).
     * */
    abstract function rename($new_name);
    /*
     * Sposta nella posizione di target, se target esiste viene sovrascritto.
     * */
    function move_to($target_dir,$new_name=null)
    {
        if ($new_name!=null)
            $name = $new_name;
        else
            $name = $this->getName();

        if ($this->isDir())
        {
            $dest = new LDir($target_dir->getPath()."/".$name);
        }
        else
        {
            $dest = new LFile($target_dir->getPath()."/".$name);
        }

        $target_dir->touch();

        return rename($this->getFullPath(),$dest->getFullPath());
    }

    abstract function copy($location);

    function dump()
    {
        echo "DUMP LFileSystemElement : ".$this->__full_path;
    }

    function getFullPath()
    {
        return $this->__full_path;
    }

    function getOriginalPath($relative_to=null)
    {
        if ($relative_to==null)
            return $this->__path;
        else
        {
            if ($relative_to instanceof LDir)
                $path = $relative_to->getPath();
            else
                $path = $relative_to;
            if (strpos($this->__path,$path)===0)
            {
                return substr($this->__path,strlen($path));
            }
            else throw new \LIOException("The path does not begin with the specified path : ".$this->__path." does not begin with ".$path);
        }
    }

    function __toString()
    {
        return $this->getFullPath();
    }

    abstract function getName();

}

?>