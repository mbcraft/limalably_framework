<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

if (!defined('FRAMEWORK_NAME')) define ('FRAMEWORK_NAME','limalably');
if (!defined('FRAMEWORK_DIR_NAME')) define ('FRAMEWORK_DIR_NAME','limalably_framework');

if (!function_exists('array_remove_key_or_value')) {
    function array_remove_key_or_value(array $data,$to_remove) {

        if ($data===null) return null;

        $result = [];

        foreach ($data as $key => $value) {
            if ($key!==$to_remove && $value!==$to_remove) {
                $result[$key] = $value;
            } 
        }

        return $result;
    }
}

function limalably_deployer_fatal_handler() {

    if (isset($_SERVER['EXIT'])) {
        exit();
    } else {

        $errfile = "unknown file";
        $errstr = "shutdown";
        $errno = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if ($error !== NULL) {
            $errno = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr = $error["message"];

            limalably_deployer_report($errno, $errstr, $errfile, $errline);
        }
    }
}

function limalably_deployer_report(int $errno, string $errstr, string $errfile, int $errline, array $errcontext=[]) {

    $available_constants = [E_COMPILE_ERROR, E_COMPILE_WARNING, E_CORE_ERROR, E_CORE_WARNING, E_ERROR, E_PARSE, E_NOTICE, E_WARNING, E_RECOVERABLE_ERROR, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE];

    foreach ($available_constants as $constant) {
        if (($errno & $constant) == $constant)
            break;
    }

    $warning = false;

    switch ($constant) {
        case E_COMPILE_ERROR : $type = 'compile';
            break;
        case E_COMPILE_WARNING : $type = 'compile';
            $warning = true;
            break;
        case E_CORE_ERROR : $type = 'core';
            break;
        case E_CORE_WARNING : $type = 'core';
            $warning = true;
            break;
        case E_ERROR : $type = 'error';
            break;
        case E_PARSE : $type = 'parse';
            break;
        case E_NOTICE: $type = 'notice';
            $warning = true;
            break;
        case E_WARNING: $type = 'warning';
            $warning = true;
            break;
        case E_RECOVERABLE_ERROR : $type = 'recoverable_error';
            break;
        case E_USER_ERROR : $type = 'user_error';
            break;
        case E_USER_WARNING : $type = 'user_warning';
            $warning = true;
            break;
        case E_USER_NOTICE : $type = 'user_notice';
            $warning = true;
            break;
        default : $type = 'unknown_error_type';
            break;
    }

    $msg = "Error type: " . $type . " - ";
    $msg .= "Error : " . $errstr . " - ";
    $msg .= "File : " . $errfile . " - ";
    $msg .= "Line number : " . $errline;

    echo json_encode(['result' => DeployerController::FAILURE_RESULT,'message' => $msg]);

    exit(0);
}

set_error_handler('limalably_deployer_report', E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);

class DIOException extends \Exception
{
    function  __construct($message, $code=null, $previous=null) {
        parent::__construct($message);
    }
}

if (!defined('DS')) define('DS','/');

abstract class DFileSystemElement
{
    protected $__full_path;
    protected $__path;
    
    private static $defaultPermissionsRwx = "-rwxr-xr-x";

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
    
    public function __construct($path)
    {
        //SAFETY NET, rimuovo tutti i .. all'interno del percorso.
        $path = str_replace('/..', "", $path);
        //pulizia doppie barre dai percorsi
        $path = str_replace("//", "/", $path);
        
        $this->__path = $path;
        
        if (strpos($path,'/')===0) {
            $this->__full_path = $path;

            $base_folder = $_SERVER['DEPLOYER_PROJECT_DIR'];

            if (strpos($this->__full_path,$base_folder)===0) $this->__path = substr($this->__full_path,strlen($base_folder));
        } else {
            $base_folder = $_SERVER['DEPLOYER_PROJECT_DIR'];
            $this->__full_path = $base_folder.$path;
        }

        if (DFileSystemUtils::isDir($this->__full_path) && !DStringUtils::endsWith($this->__full_path,'/')) {
            $this->__path .= '/';
            $this->__full_path .= '/';
        }

        if (strpos($this->__path,'/')===0) $this->__path = substr($this->__path,1);
    }

    function equals($file_or_dir)
    {
        if ($file_or_dir instanceof DFileSystemElement)
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

    function isReadable() {
        return $this->hasPermissions("-r--------");
    }

    function isWritable() {
        return $this->hasPermissions("--w-------");
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
        if ($this->isDir())
        {
            if ($new_name!=null) {
                $dest = new LDir($target_dir_or_file->getFullPath());

                $dest->touch();
            }
            else {
                $name = $this->getName();

                $target_dir_or_file->touch();

                $dest = new LDir($target_dir_or_file->getFullPath().'/'.$name.'/');
            }

        }
        else
        {
            $dest = $target_dir_or_file;
        }

        return rename($this->getFullPath(),$dest->getFullPath());
    }

    abstract function copy($location);

    function dump()
    {
        echo "DUMP DFileSystemElement : ".$this->__full_path;
    }

    function getPath() {
        return $this->__path;
    }

    function getFullPath()
    {
        return $this->__full_path;
    }

    private function prepareRelativePath($path) {
        if (strpos($path,'/')===0) return substr($path,1);
        else return $path;
    } 

    function getRelativePath($relative_to=null)
    {
        if ($relative_to==null)
            return $this->prepareRelativePath($this->__path);
        else
        {
            if ($relative_to instanceof DDir)
                $path = $relative_to->getPath();
            else
                $path = $relative_to;
            if (strpos($this->__path,$path)===0)
            {
                return $this->prepareRelativePath(substr($this->__path,strlen($path)));
            }
            else throw new \DIOException("The path does not begin with the specified path : ".$this->__path." does not begin with ".$path);
        }
    }

    function __toString()
    {
        return $this->getFullPath();
    }

    abstract function getName();

}

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
        $base_folder = $_SERVER['DEPLOYER_PROJECT_DIR'];
        
        if (strpos($path,'/')===0) {
            
        } else {
            $path = $base_folder.$path;
        }
        
        return is_file($path);
    }

    static function isDir(string $path)
    {
        $base_folder = $_SERVER['DEPLOYER_PROJECT_DIR'];
        
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
        $path = $_SERVER['DEPLOYER_PROJECT_DIR'];
        
        return disk_free_space($path);
    }

    static function getTotalDiskSpace()
    {
        $path = $_SERVER['DEPLOYER_PROJECT_DIR'];
        
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


class DDir extends DFileSystemElement
{
    const TMP_DIR = "temp/";

    const FILTER_ALL_DIRECTORIES = 1;
    const FILTER_ALL_FILES = 2;
    const FILTER_ALL_ELEMENTS = 3;

    const DEFAULT_EXCLUDES = "NO_HIDDEN_FILES";

    const NO_HIDDEN_FILES = "NO_HIDDEN_FILES";
    static $noHiddenFiles = array("/\A\..*\Z/");
    const SHOW_HIDDEN_FILES = "SHOW_HIDDEN_FILES";
    static $showHiddenFiles = array("/\A[\.][\.]?\Z/");

    static $content_hash_cache = [];

    function __construct($path)
    {
        if ($path!="") {
            if (substr($path,strlen($path)-1,1)!='/') 
                    $path=$path.'/';
        }
        parent::__construct($path);
        
    }
    
    function explore($inspector)
    {
        $result = [];

        if (!$this->exists()) return $result;

        $path = $this->getPath();

        if (DStringUtils::startsWith($path,$inspector->getExcludedPaths())) return $result;
        
        $result = $inspector->visit($this);
        
        $all_folders = $this->listFolders();
        
        foreach ($all_folders as $fold)
        {
            $r = $fold->explore($inspector);

            $pre_result = array_remove_key_or_value($r,'');

            $result = array_merge($pre_result,$result);
        }

        $final_result = array_remove_key_or_value($result,'');

        return $final_result;
    }
    
    /*
     * Ritorna il livello della directory :
     * / : 0
     * /test/ : 1
     * /test/js/mooo/ : 3
     */
    function getLevel()
    {
        $matches = [];
        preg_match_all("/\//", $this->__full_path,$matches);
        return count($matches[0])-1;
    }
    
    function touch()
    {
        if (!$this->exists())
        {
            @mkdir($this->__full_path,  self::getDefaultPermissionsOctal(),true);
            chmod($this->__full_path,  self::getDefaultPermissionsOctal());
        }
        else
            touch($this->__full_path);
    }

    static function getTempDir() {
       
        return new DDir(self::TMP_DIR);
    }

    function newTempFile($prefix='tmp_') {

        if (!$this->exists()) $this->touch();

        $result = tempnam($this->getFullPath(),$prefix);
        if ($result) return new DFile($result);
        else return false;
    }

    function getContentHash($excluded_paths=[]) {

        if (isset(self::$content_hash_cache[$this->__path])) return self::$content_hash_cache[$this->__path];

        $elements = $this->listAll();

        $all_hashes = "";

        foreach ($elements as $elem) {
            if (!DStringUtils::startsWith($elem->getPath(),$excluded_paths)) {
                $all_hashes .= $elem->getContentHash($excluded_paths);
            }
        }

        $result = sha1($all_hashes);

        self::$content_hash_cache[$this->__path] = $result;

        return $result;
    }


    function getParentDir()
    {
        $parent_dir = dirname($this->__full_path);
        
        return new DDir($parent_dir);
    }

    /*
   * Rinomina il file senza effettuare spostamenti di sorta.
   * */
    function rename($new_name)
    {
        if (strstr($new_name,"/")!==false)
            throw new \DIOException("The new name contains invalid characters : / !!");

        $parent_dir = $this->getParentDir();

        $target_path = $parent_dir->getFullPath()."/".$new_name;

        $target_dir = new DDir($target_path);
        if ($target_dir->exists()) return false;

        return rename($this->__full_path,$target_dir->getFullPath());
    }
    
    function getName()
    {
        return $this->getDirName();
    }

    function getDirName()
    {
        return basename($this->__full_path);
    }
        
    function hasSubdirOrSame($subdir)
    {         
        while (strlen($subdir->getFullPath()) >= strlen($this->getFullPath()))
        {

            if ($this->equals($subdir))
                return true;
            else
                return $this->hasSubdirOrSame($subdir->getParentDir());
        }
        return false;             
    }
    
    function newSubdir($name)
    {
        if (DFileSystemUtils::isDir($this->__path.'/'.$name))
        {
            //directory already exists
            //echo "Directory already exists : ".$this->__full_path."/".$name;
            return new DDir($this->__path.'/'.$name);
        }
        if (DFileSystemUtils::isFile($this->__path.'/'.$name))
        {
            throw new \DIOException("A file with this name already exists");
        }
        //directory or files do not exists
        
        if (!file_exists($this->__full_path)) {
            $result = @mkdir($this->__full_path.$name, LFileSystemElement::getDefaultPermissionsOctal(),true);
        
            if ($result==true) {
                chmod($this->__full_path.$name, LFileSystemElement::getDefaultPermissionsOctal());
                return new DDir($this->__path.$name);
            }
        }
        else return new DDir($this->__full_path);

    }
/*
 * TESTED
 */
    function isEmpty()
    {
        if (!$this->exists()) return true;
        
        return count($this->listAll())===0;
    }

    function listAll($myExcludes=self::DEFAULT_EXCLUDES) {
        return $this->listElements($myExcludes,self::FILTER_ALL_ELEMENTS);
    }

    function listFolders($myExcludes=self::DEFAULT_EXCLUDES) {
        return $this->listElements($myExcludes,self::FILTER_ALL_DIRECTORIES);
    }

    function listFiles($myExcludes=self::DEFAULT_EXCLUDES) {
        return $this->listElements($myExcludes,self::FILTER_ALL_FILES);
    }
 /*
 * TESTED
 */
    function listElements($myExcludes=self::DEFAULT_EXCLUDES,$filter = self::FILTER_ALL_FILES)
    {   
        if (!$this->exists()) throw new \DIOException("Directory does not exist, can't list elements.");
        
        $excludesSet = false;
        
        if (!$excludesSet && $myExcludes === self::NO_HIDDEN_FILES) 
        {
            $excludesSet = true;
            $excludes = self::$noHiddenFiles;
        }
        
        if (!$excludesSet && $myExcludes === self::SHOW_HIDDEN_FILES) 
        {
            $excludesSet = true;
            $excludes = self::$showHiddenFiles;
        }
        if (!$excludesSet)
            $excludes = $myExcludes;

        $all_results = scandir($this->__full_path);

        $all_dirs = array();
        $all_files = array();
        
        foreach ($all_results as $element)
        {            
            $skip = false;
            foreach ($excludes as $pt)
            {
                if (preg_match($pt, $element)) 
                {
                    $skip = true;
                }
            }

            //è da saltare?
            if (!$skip)
            {
                $final_path = $this->__full_path.$element;
                
                if (($filter & self::FILTER_ALL_DIRECTORIES) == self::FILTER_ALL_DIRECTORIES) {
                    if (DFileSystemUtils::isDir($final_path))
                        $all_dirs[] = new DDir($final_path.'/');
                }
                if (($filter & self::FILTER_ALL_FILES) == self::FILTER_ALL_FILES) {
                    if (DFileSystemUtils::isFile($final_path))
                        $all_files[] = new DFile($final_path);
                }
            }                

        }
      
        return array_merge($all_dirs, $all_files);

    }
    
    function findElementsStartingWith($string,$filter = self::FILTER_ALL_ELEMENTS)
    {
        $dot_escaped = str_replace(".", "[\.]", $string);
        return $this->findElements("/\A".$dot_escaped."/",$filter);
    }
    
    function findFilesStartingWith($string)
    {
        $dot_escaped = str_replace(".", "[\.]", $string);
        return $this->findElements("/\A".$dot_escaped."/",self::FILTER_ALL_FILES);
    }
    

    function findElementsEndingWith($string,$filter = self::FILTER_ALL_ELEMENTS)
    {
        $dot_escaped = str_replace(".", "[\.]", $string);
        return $this->findElements("/".$dot_escaped."\Z/",$filter);
    }

    function findFilesEndingWith($string)
    {
        $dot_escaped = str_replace(".", "[\.]", $string);
        return $this->findElements("/".$dot_escaped."\Z/",self::FILTER_ALL_FILES);
    }

    function findFiles($myIncludes) {
        return $this->findElements($myIncludes,self::FILTER_ALL_FILES);
    }


    
    function findElements($myIncludes,$filter = self::FILTER_ALL_ELEMENTS)
    {
        if (is_array($myIncludes))
            $includes = $myIncludes;
        else
            $includes = array($myIncludes);
        
        $all_results = scandir($this->__full_path);

        $all_dirs = array();
        $all_files = array();
        
        foreach ($all_results as $element)
        {            
            $include = false;
            $done = false;
            foreach ($includes as $pt)
            {
                if (!$done && preg_match($pt, $element)) 
                {
                    $include = true;
                    $done = true;
                }
            }

            //è da saltare?
            if ($include)
            {
                if ($this->isDir())
                    $partial_path = $this->__path.$element;
                if (($filter & self::FILTER_ALL_DIRECTORIES) == self::FILTER_ALL_DIRECTORIES) {
                    if (DFileSystemUtils::isDir($this->__path.$element))
                        $all_dirs[] = new DDir($partial_path);
                }
                if (($filter & self::FILTER_ALL_FILES) == self::FILTER_ALL_FILES) {
                    if (DFileSystemUtils::isFile($this->__path.DS.$element))
                        $all_files[] = new DFile($partial_path);
                }
            }                

        }
      
        return array_merge($all_dirs, $all_files);
    }

    function newFile($name)
    {
        return new DFile($this->__full_path.'/'.$name);
    }

    /*
     * Cancella la cartella. $recursive è true, cancella anche tutto il contenuto ricorsivamente.
     * Ritorna true se l'operazione è riuscita, false altrimenti.
     */
    function delete($recursive = false)
    {
        $result = true;

        if ($recursive)
        {
            $dir_content = $this->listAll(DDir::SHOW_HIDDEN_FILES);
            foreach ($dir_content as $elem)
            {
                if ($elem instanceof DDir)
                    $result &= $elem->delete(true);
                else
                    $result &= $elem->delete();
            }
        } 

        $result &= @rmdir($this->__full_path);

        return $result;
    }
    
    function hasOnlyOneSubdir()
    {
        $content = $this->listFolders();
        if (count($content)==1)
        {
            $dir_elem = $content[0];
            if ($dir_elem->isDir()) return true;
        }
        return false;
    }
    
    function getOnlyOneSubdir()
    {
        $content = $this->listFolders();
        if (count($content)==1)
        {
            $dir_elem = $content[0];
            if ($dir_elem->isDir()) return $dir_elem;
            throw new \DIOException("The element inside the folder is not a folder.");
        }
        throw new \DIOException("Unable to find a single subdir. Too many folders found:".count($content));
    }

    function hasSubdirs()
    {
        $content = $this->listFolders();
        foreach ($content as $f)
        {
            if ($f->isDir()) return true;
        }
        return false;
    }
    
    /*
     * Copia una cartella all'interno di un'altra cartella
     */
    function copy($dest_dir)
    {
        $dest_dir_ok = null;

        if (is_string($dest_dir))
            $dest_dir_ok = new DDir($dest_dir);
        if ($dest_dir instanceof DDir)
            $dest_dir_ok = $dest_dir;

        if ($dest_dir_ok)
        {                      
            $all_elems = $this->listAll();

            foreach ($all_elems as $elem)
            {
                if ($elem instanceof DFile) {
                    $elem->copy($dest_dir_ok);
                    continue;
                }
                if ($elem instanceof DDir)
                {
                    $subdir = $dest_dir_ok->newSubdir($elem->getName());
                    $elem->copy($subdir);
                    continue;
                }
                throw new \DIOException("Unable to copy element of class : ".get_class($elem));
            }
        } else throw new \DIOException("dest_dir is not a valid path or LDir instance!");

    }

    function isParentOf($folder)
    {
        if ($folder instanceof DDir)
            $d = $folder;
        else
            $d = new DDir($folder);

        $path_a = $this->getPath();
        $path_b = $d->getPath();

        return strpos($path_b,$path_a)===0;
    }

    function toArray()
    {
        $result = array();
        
        $result["full_path"] = $this->getFullPath();
        $result["path"] = $this->getPath();
        $result["name"] = $this->getDirName();
        $result["type"] = "dir";
        $result["empty"] = $this->isEmpty();

        return $result;
    }

}


class DFile extends DFileSystemElement
{
    static $content_hash_cache = [];

    function getDirectory()
    {
        return new DDir(dirname($this->__full_path));
    }
    
    function getName()
    {
        $result = pathinfo($this->__full_path);
        return $result['filename'];
    }

  /*
   * Rinomina il file senza effettuare spostamenti di sorta.
   * */
    function rename($new_name)
    {
        if (strstr($new_name,"/")!==false)
            throw new \DIOException("The name contains forbidden characters : / !!");

        $this_dir = $this->getDirectory();

        $target_path = $this_dir->getPath()."/".$new_name;

        $target_file = new DFile($target_path);
        if ($target_file->exists()) return false;

        return rename($this->__full_path,$target_file->getFullPath());
    }

/*
 * TESTED
 */
    function getFilename()
    {
        $result = pathinfo($this->__full_path);
        return $result['filename'].".".$result['extension'];
    }
/*
 * TESTED
 *
 * eg : .jpg
 */
    function getExtension()
    {
        $result = pathinfo($this->__full_path);
        
        return $result['extension'];
    }
/*
 * TESTED
 */
    function getFullExtension()
    {
        $matches = [];
        $filename = $this->getFilename();
        $result = preg_match("/\.(.+)/",$filename,$matches);

        return $matches[1];
    }
/*
 * TESTED
 */
    function getContent()
    {
        return file_get_contents($this->__full_path);
    }

    function getContentHash($excluded_paths=[])
    {
        if (isset(self::$content_hash_cache[$this->__path])) return self::$content_hash_cache[$this->__path];

        $result = sha1_file($this->__full_path);

        self::$content_hash_cache[$this->__path] = $result;

        return $result;
    }
/*
 * TESTED
 */
    function setContent($content)
    {
        file_put_contents($this->__full_path, $content, LOCK_EX);
    }

    function getSize()
    {
        return filesize($this->__full_path);
    }
/*
 * TESTED
 */
    function delete()
    {
        if (DFileSystemUtils::isDir($this->__full_path)) throw new \DIOException("This is a directory and it should not be!");

        return @unlink($this->__full_path);
    }

    function isEmpty()
    {
        return $this->getSize()==0;
    }

    function copy($dest_dir_or_file)
    {      
        if ($dest_dir_or_file instanceof DDir)
        {
            return copy($this->__full_path,$dest_dir_or_file->__full_path.$this->getFilename());
        }
        if ($dest_dir_or_file instanceof DFile) {
            return copy($this->__full_path,$dest_dir_or_file->__full_path);
        }
    }

    function filenameMatches($pattern)
    {
        return preg_match($pattern, $this->getFilename())!=0;
    }
    

    function touch()
    {
        touch($this->__full_path);
    }

    public function openReader()
    {
        if (!$this->exists()) throw new \DIOException("Unable to open file reader at path : ".$this->__full_path.". The file does not exist!!");


        $handle = fopen($this->__full_path,"r");

        if ($handle===false) throw new \DIOException("Unable to open file reader at path : ".$this->__full_path.".");

        if (flock($handle, LOCK_SH))
        {
            return new DFileReader($handle);
        }
        else 
        {
            fclose($this->my_handle);
            return null;
        }
    }

    public function openWriter()
    {
        $handle = fopen($this->__full_path,"c+");

        if ($handle===false) throw new \DIOException("Unable to open file writer at path : ".$this->__full_path);

        if (flock($handle,LOCK_EX))
        {
            return new DFileWriter($handle);
        }
        else 
        {
            fclose($this->my_handle);
            return null;
        }
    }

    public function getIncludePath()
    {
        $my_path = $this->getFullPath();

        return $my_path;
    }

    public function includeFile()
    {
        $my_path = $this->getFullPath();

        include($my_path);
    }
    
    public function includeFileOnce()
    {
        $my_path = $this->getFullPath();

        include_once($my_path);
    }
    
    public function requireFileOnce()
    {
        $my_path = $this->getFullPath();

        require_once($my_path);
    }

    public function toArray()
    {
        $result = array();

        $result["full_path"] = $this->getFullPath();
        $result["path"] = $this->getPath();
        $result["name"] = $this->getName();
        $result["extension"] = $this->getExtension();
        $result["full_extension"] = $this->getFullExtension();
        $result["type"] = "file";

        $size = $this->getSize();

        $result["size"] = $size;

        $result["size_auto"] = $size." bytes";
        if ($size>1024*2)
            $result["size_auto"] = ($size/1024)." KB";
        if ($size>(1024^2)*2)
            $result["size_auto"] = ($size/(1024^2))." MB";
        if ($size>((1024^3)*2))
            $result["size_auto"] = ($size/(1024^3))." GB";

        return $result;
    }

}


class DFileReader
{
    protected $my_handle;
    protected $open;
    
    function __construct($handle)
    {
        $this->my_handle = $handle;
        $this->open = true;
    }

    protected function checkClosed()
    {
        if (!$this->open) throw new \DIOException("The stream is closed!!");
    }

    function isOpen()
    {
        return $this->open;
    }

    function scanf($format)
    {
        $this->checkClosed();

        return fscanf($this->my_handle,$format);
    }
    
    function read($length)
    {
        $this->checkClosed();

        return fread($this->my_handle,$length);
    }
    
    function readLine()
    {
        $this->checkClosed();

        $line = fgets($this->my_handle);
        return preg_replace("/\r?\n\Z/","",$line);
    }
    
    function readChar()
    {
        $this->checkClosed();

        return fgetc($this->my_handle);
    }
    
    function readCSV($delimiter=",")
    {
        $this->checkClosed();

        return fgetcsv($this->my_handle,$delimiter);
    }

    function reset()
    {
        $this->checkClosed();

        rewind($this->my_handle);
    }
    
    function seek($location)
    {
        $this->checkClosed();

        fseek($this->my_handle,$location,SEEK_SET);
    }
    
    function skip($offset)
    {
        $this->checkClosed();

        fseek($this->my_handle,$offset,SEEK_CUR);
    }
    
    function pos()
    {
        $this->checkClosed();

        return ftell($this->my_handle);
    }
        
    function isEndOfStream()
    {
        $this->checkClosed();

        return feof($this->my_handle);
    }
    
    function close()
    {
        if ($this->open)
        {
            fflush($this->my_handle);
            flock($this->my_handle,LOCK_UN);
            fclose($this->my_handle);

            $this->open = false;
            $this->my_handle = null;
        }
        else
            throw new \DIOException("Reader/Writer already closed.");

    }
    
    function getHandler()
    {
        return $this->my_handle;
    }
}


class DFileWriter extends DFileReader
{
    const CR = "\r";
    const LF = "\n";

    static function newTmpFile()
    {
        return new DFileWriter(tmpfile());
    }

    /*
     * Uso eval per simulare printf
     * */
    function printf($format)
    {
        $this->checkClosed();

        $args = func_get_args();
        $printf_args = array_slice($args,1);

        $p = 'fprintf($this->my_handle,$format';
        $i = 0;
        foreach ($printf_args as $arg)
        {

            $p.=',$printf_args['.$i.']';
            $i++;
        }
        $p.=");";
        eval($p);
    }
    
    function write($string)
    {
        $this->checkClosed();

        fwrite($this->my_handle, $string);
    }

    function writeln($string)
    {
        $this->checkClosed();

        fwrite($this->my_handle,$string.self::CR.self::LF);
    }
    
    function writeCSV($values,$delimiter=",")
    {
        $this->checkClosed();

        fputcsv($this->my_handle, $values,$delimiter);
    }

    function truncate($size)
    {
        $this->checkClosed();

        ftruncate($this->my_handle, $size);
    }
    
}


class DZipUtils
{
    public static function expandArchive($zip_file,$target_folder)
    {
        $zip_archive = new ZipArchive();
     
        if ($zip_file instanceof DFile)
            $real_zip_file = $zip_file;
        else
            $real_zip_file = new DFile($zip_file);
        
        
        if ($target_folder instanceof DDir)
            $target_dir = $target_folder;
        else
            $target_dir = new DDir($target_folder);
        
        $zip_archive->open($real_zip_file->getFullPath());
        
        $zip_archive->extractTo($target_dir->getFullPath());
        
        $zip_archive->close();
    }
    
    public static function createArchive($save_file,$folder_to_zip,$local_dir="/")
    {
        if ($save_file->exists()) $save_file->delete(); 

        if ($folder_to_zip instanceof DDir)
            $dir_to_zip = $folder_to_zip;
        else
            $dir_to_zip = new DDir($folder_to_zip);
        
        if (!class_exists('ZipArchive')) throw new \Exception("Can't use zip files, ZipArchive class missing.");

        $zip_archive = new ZipArchive();

        $zip_archive->open($save_file->getFullPath(),  ZipArchive::CREATE);

        DZipUtils::recursiveZipFolder($zip_archive, $dir_to_zip,$local_dir);

        $zip_archive->close();
    }
    
    private static function recursiveZipFolder($zip_archive,$current_folder,$local_dir)
    {        
        foreach ($current_folder->listAll() as $dir_entry)
        {
            if ($dir_entry->isFile())
            {
                $zip_archive->addFile($dir_entry->getFullPath(),$local_dir.$dir_entry->getFilename());
            }
            else
            {
                $zip_archive->addEmptyDir($local_dir.$dir_entry->getName().'/');
                DZipUtils::recursiveZipFolder($zip_archive, $dir_entry,$local_dir.$dir_entry->getName().'/');
            }
        }
    }
}

class DStringUtils {
    
    static function underscoredToCamelCase($string)
    {
            $string[0] = strtoupper($string[0]);

            $func = create_function('$c', 'return strtoupper($c[1]);');
            return preg_replace_callback('/_([a-z])/', $func, $string);
    }
    /*
     * Questa funzione splitta i nomi camelcase mettendo gli underscore secondo la seguente regola :
     * 
     * FPDF -> fpdf
     * ContenutiTestualiController -> contenuti_testuali_controller
     * */
    static function camelCaseSplit($string,$skip_last=false,$join_part="_")
    {
        $matches = array();
        preg_match_all("/([A-Z]+[A-Z](?![a-z]))|([A-Z]+[a-z]*)/",$string,$matches); //black magic, do not touch ...
        $real_matches = $matches[0];

        $lower_matches = array();
        foreach ($real_matches as $mtc)
            $lower_matches[] = strtolower($mtc);

        if ($skip_last)
            array_pop($lower_matches);

        return join($join_part,$lower_matches);
    }

    static function trimEndingChars($string,$num)
    {
        if ($num>strlen($string)) throw new \Exception("Numero di caratteri piu' lungo della stringa!!");
        return substr($string,0,-$num);
    }
    
    static function startsWith($string,$needle) {
        if (is_array($needle)) {
            $result = false;
            foreach ($needle as $n) {
                $result |= strpos($string,$n)===0;
            }
            return $result;
        } else {
            return strpos($string,$needle)===0;
        }
    }
    
    static function endsWith($string,$needle) {
        if (is_array($needle)) {
            $result = false;
            foreach ($needle as $n) {
                $result |= strpos($string,$n,strlen($string)-strlen($n))===(strlen($string)-strlen($n));
            }
            return $result;
        }
        else {
            if (strlen($needle)>strlen($string)) return false;
            return strpos($string,$needle,strlen($string)-strlen($needle))===(strlen($string)-strlen($needle));
        }
    }
    
    static function contains($string,$needle) {
        if (is_array($needle)) {
            $result = false;
            foreach ($needle as $n) {
                $result |= strpos($string,$n)!==false;
            }
            return $result;
        }
        else {
            return strpos($string,$needle)!==false;
        }
    }
    
    static function getErrorMessage(string $error,string $file,int $line,bool $use_newline=true) {
        $NL = $use_newline ? "\n" : '<br>';
        $message = 'Error : '.$error.$NL;
        $message .= 'File : '.$file.' Line : '.$line.$NL;
        return $message;
    }
    
    static function getExceptionMessage(\Exception $ex,bool $print_stack_trace=true,bool $use_newline=true) {
        $exceptions = [$ex];
        if ($print_stack_trace) {
            while ($ex->getPrevious()!=null) {
                $ex = $ex->getPrevious();
                array_unshift ($exceptions, $ex);
            }
        }
        $message = '';
        foreach ($exceptions as $ex) {
            $message .= self::internalGetExceptionMessage($ex, $print_stack_trace, $use_newline);
        }
        return $message;
    }

    static function getCommentDelimitedReplacementsStringSeparator($var_name) {
        $i = 0;

        $result = $var_name[0];

        for ($i=1;$i<strlen($var_name);$i++) {
            $result .= '_'.$var_name[$i];
        }

        return $result;
    }
    
    private static function internalGetExceptionMessage(\Exception $ex,bool $print_stack_trace,bool $use_newline) {
        $NL = $use_newline ? "\n" : '<br>';
        $message = $ex->getMessage().$NL;
        $message .= 'File : '.$ex->getFile().' Line : '.$ex->getLine().$NL;
        if ($print_stack_trace) {
            $message .= 'Stack Trace : '.$ex->getTraceAsString().$NL;
        }
        return $message;
    }
    
    
    public static function getNewlineString() {
        if ($_SERVER['ENVIRONMENT'] == 'script')
            return "\n";
        else
            return '<br>';
    }    
}

interface DIInspector {

    public function visit($dir);

    public function getExcludedPaths();

    public function getIncludedPaths();

}

class ContentHashInspector implements DIInspector {

    private $excluded_paths = [];
    private $included_paths = [];

    public function setExcludedPaths($excluded_paths) {
        $this->excluded_paths = $excluded_paths;
    }

    public function getExcludedPaths() {
        return $this->excluded_paths;
    }

    public function setIncludedPaths($included_paths) {
        $this->included_paths = $included_paths;
    }

    public function getIncludedPaths() {
        return $this->included_paths;
    }

    public function visit($dir) {

        $result = [];

        if ($dir->exists() && !in_array($dir->getPath(),$this->excluded_paths)) {

            if ($dir->getPath()!="") {
               $result[$dir->getPath()] = $dir->getContentHash($this->excluded_paths);
            }

            $files = $dir->listFiles();

            foreach ($files as $f) {
                if (!in_array($f->getPath(),$this->excluded_paths)) {
                    $result[$f->getPath()] = $f->getContentHash($this->excluded_paths);
                }
            }
        }

        return $result;

    }
}

class PermissionsFixerInspector implements DIInspector {

    private $excluded_paths = [];
    private $included_paths = [];

    function __construct($permissions_to_set) {
        $this->permissions_to_set = $permissions_to_set;
    }

    public function setExcludedPaths($excluded_paths) {
        $this->excluded_paths = $excluded_paths;
    }

    public function getExcludedPaths() {
        return $this->excluded_paths;
    }

    public function setIncludedPaths($included_paths) {
        $this->included_paths = $included_paths;
    }

    public function getIncludedPaths() {
        return $this->included_paths;
    }

    public function visit($dir) {

        $result = [];

        if ($dir->exists() && !in_array($dir->getPath(),$this->excluded_paths)) {

            $files = $dir->listFiles();

            foreach ($files as $f) {
                if (!in_array($f->getPath(),$this->excluded_paths)) {
                    $f->setPermissions($this->permissions_to_set);
                }
            }
        }

        return $result;

    }
}


//misc important variables ---

$current_dir = __DIR__;

if (!DStringUtils::endsWith($current_dir,'/')) $current_dir.='/';

$_SERVER['DEPLOYER_PROJECT_DIR'] = $current_dir;

//starting deployer controller ---

class DeployerController {

    const BUILD_NUMBER = 69;

    const DEPLOYER_VERSION = "1.5";

    const DEPLOYER_FEATURES = ['version','listElements','listHashes','deleteFile','makeDir','deleteDir','copyFile','downloadDir','setEnv','getEnv','listEnv','hello','fileExists','writeFileContent','readFileContent','listDb','backupDbStructure','backupDbData','migrateAll','migrateReset','migrateListDone','migrateListMissing','fixPermissions'];

	private $deployer_file;
	private $root_dir;

    //password
	private static $PWD = /*!P_W_D!*/""/*!P_W_D!*/; 

    //deployer path from root
    private static $DPFR = /*!D_P_F_R!*/"deployer.php"/*!D_P_F_R!*/; 

	const SUCCESS_RESULT = ":)";
	const FAILURE_RESULT = ":(";

	function __construct() {

		$this->deployer_file = new DFile(__FILE__);

        $path_parts = explode('/',self::$DPFR);

        $current_dir = new DDir(__DIR__);

        for ($i=0;$i<count($path_parts)-1;$i++) $current_dir = $current_dir->getParentDir();

		$this->root_dir = $current_dir;

        $_SERVER['DEPLOYER_PROJECT_DIR'] = $this->root_dir->getFullPath();

	}

    private function logWithFile(string $file_name,string $content) {
        $f = new DFile($file_name);
        $f->setContent($content);
    }

    private function hasPostParameter($param_name) {

        return isset($_POST[$param_name]);

    }

    private function isPostParameterDangerous($param_name) {
        $filtered_param_value = $this->getPostParameter($param_name);

        $raw_param_value = $_POST[$param_name];

        if ($filtered_param_value!=$raw_param_value) return true;
        else return false;
    }

    private function getPostParameter($param_name) {
        $value = $_POST[$param_name];

        $final_value = filter_var($value,FILTER_DEFAULT);

        return $final_value;
    }

    public function version($password) {
        if ($this->accessGranted($password)) {
            return ['result' => self::SUCCESS_RESULT,'version' => self::DEPLOYER_VERSION,'features' => self::DEPLOYER_FEATURES,'build' => self::BUILD_NUMBER];
        } else return $this->failure("Wrong password");
    }

    private function getFinalPathList($path_list) {

        $result = [];

        foreach ($path_list as $p) {
            if ($p==='@') $result[] = self::$DPFR;
                else $result[] = $p;
        }

        return $result;

    }

    private function containsDeployerPath($path_list) {
        return in_array('@',$path_list);
    }

    private function loadFrameworkBasicClasses() {

        $path_prefix = FRAMEWORK_DIR_NAME.'/';

        $f = new DFile($path_prefix.'framework_spec.php');
        if (!$f->exists()) return $path_prefix.'framework_spec.php not found.';
        $f->requireFileOnce();

        $f = new DFile($path_prefix.'lib/treemap/LTreeMap.class.php');
        if (!$f->exists()) return $path_prefix.'lib/treemap/LTreeMap.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/treemap/LTreeMapView.class.php');
        if (!$f->exists()) return $path_prefix.'lib/treemap/LTreeMapView.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/treemap/LStaticTreeMapBase.trait.php');
        if (!$f->exists()) return $path_prefix.'lib/treemap/LStaticTreeMapBase.trait.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/treemap/LStaticTreeMapRead.trait.php');
        if (!$f->exists()) return $path_prefix.'lib/treemap/LStaticTreeMapRead.trait.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/treemap/LStaticTreeMapWrite.trait.php');
        if (!$f->exists()) return $path_prefix.'lib/treemap/LStaticTreeMapWrite.trait.php not found.';
        $f->requireFileOnce();

        //config
        $f = new DFile($path_prefix.'lib/config/LConfig.class.php');
        if (!$f->exists()) return $path_prefix.'lib/config/LConfig.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/config/LConfigReader.class.php');
        if (!$f->exists()) return $path_prefix.'lib/config/LConfigReader.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/config/LExecutionMode.class.php');
        if (!$f->exists()) return $path_prefix.'lib/config/LExecutionMode.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/config/LEnvironmentUtils.class.php');
        if (!$f->exists()) return $path_prefix.'lib/config/LEnvironmentUtils.class.php not found.';
        $f->requireFileOnce();

        //core
        $f = new DFile($path_prefix.'lib/core/LErrorReportingInterceptors.class.php');
        if (!$f->exists()) return $path_prefix.'lib/core/LErrorReportingInterceptors.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/core/LInvalidParameterException.class.php');
        if (!$f->exists()) return $path_prefix.'lib/core/LInvalidParameterException.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/core/LResult.class.php');
        if (!$f->exists()) return $path_prefix.'lib/core/LResult.class.php not found.';
        $f->requireFileOnce();

        $f = new DFile($path_prefix.'lib/core/LClassLoader.class.php');
        if (!$f->exists()) return $path_prefix.'lib/core/LClassLoader.class.php not found.';
        $f->requireFileOnce();

        //utils
        $f = new DFile($path_prefix.'lib/utils/LStringUtils.class.php');
        if (!$f->exists()) return $path_prefix.'lib/utils/LStringUtils.class.php not found.';
        $f->requireFileOnce();
        $f = new DFile($path_prefix.'lib/utils/LJsonUtils.class.php');
        if (!$f->exists()) return $path_prefix.'lib/utils/LJsonUtils.class.php not found.';
        $f->requireFileOnce();

        $f = new DFile($path_prefix.'lib/db/functions.php');
        if (!$f->exists()) return $path_prefix.'lib/db/functions.php not found.';
        $f->requireFileOnce();

        if (!LConfig::initCalled()) LConfig::init();

        if (!LClassLoader::initCalled()) LClassLoader::init();

        return true;

    }

    public function listDb($password) {

        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) {

                    return $this->failure($result);
                }
            } catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            $db_list = LConfigReader::simple('/database');

            $result_data = [];

            foreach ($db_list as $db_name => $db_data) {
                $result_data[] = $db_name;
            }

            return ["result" => self::SUCCESS_RESULT,"data" => $result_data];

        } else return $this->failure("Wrong password.");
    }

    public function backupDbStructure($password,$connection_name) {
        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) return $this->failure($result);
            }
            catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            if (!LDbConnectionManager::has($connection_name)) return $this->failure("Unable to find db connection with name : ".$connection_name);

            $connection = LDbConnectionManager::get($connection_name);

            $zip_dir = new DDir('temp/backup/db/structure/'.$connection_name.'/');
            if ($zip_dir->exists()) $zip_dir->delete(true);
            sleep(3);
            $zip_dir->touch();

            if (!$zip_dir->exists()) return $this->failure("Unable to create temporary dir necessary for storing and compressing files.");

            try {
                $db = db($connection_name);

                $table_list = table_list()->go($db);

                foreach ($table_list as $tb) {
                    $query = create_table($tb)->show()->go($db);
                    
                    $qf = $zip_dir->newFile($tb.'__structure.sql');
                    $qf->setContent($query."\n\n");
                }
            } catch (\Exception $ex) {

                return $this->failure("Exception during query phase : ".$ex->getMessage());
            } 

            $zip_file = new DFile('temp/backup/db/structure/'.$connection_name.'_structure_bkp.zip');

            DZipUtils::createArchive($zip_file,'temp/backup/db/structure/'.$connection_name.'/','');

            return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

        } else return $this->failure("Wrong password.");
    }

    public function backupDbData($password,$connection_name) {
        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) return $this->failure($result);
            }
            catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            if (!LDbConnectionManager::has($connection_name)) return $this->failure("Unable to find db connection with name : ".$connection_name);

            $connection = LDbConnectionManager::get($connection_name);

            $zip_dir = new DDir('temp/backup/db/data/'.$connection_name.'/');
            if ($zip_dir->exists()) $zip_dir->delete(true);
            $zip_dir->touch();

            if (!$zip_dir->exists()) return $this->failure("Unable to create temporary dir necessary for storing and compressing files.");

            try {
                $db = db($connection_name);

                $table_list = table_list()->go($db);

                foreach ($table_list as $tb) {
                    $iterator = select('*',$tb)->iterator($db);
                    
                    $qf = $zip_dir->newFile($tb.'__data.sql');
                    $wr = $qf->openWriter();

                    while ($iterator->hasNext()) {
                        $data = $iterator->next();

                        $query = insert($tb,array_keys($data),array_values($data)).";";

                        $wr->writeln($query);
                        $wr->writeln("");

                    }

                    $wr->close();
                }
            } catch (\Exception $ex) {

                return $this->failure("Exception during query phase : ".$ex->getMessage());
            } 

            $zip_file = new DFile('temp/backup/db/data/'.$connection_name.'_data_bkp.zip');

            DZipUtils::createArchive($zip_file,'temp/backup/db/data/'.$connection_name.'/','');

            return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

        } else return $this->failure("Wrong password.");
    }

    public function migrateAll($password) {
        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) return $this->failure($result);
            }
            catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            LResult::disableOutput();

            LMigrationSupport::executeAllMigrations();

            return ['result' => self::SUCCESS_RESULT];

        } else return $this->failure("Wrong password.");
    }

    public function migrateReset($password) {
        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) return $this->failure($result);
            }
            catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            LResult::disableOutput();

            LMigrationSupport::resetAllMigrations();

            return ['result' => self::SUCCESS_RESULT];

        } else return $this->failure("Wrong password.");
    }

    public function migrateListDone($password) {
        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) return $this->failure($result);
            }
            catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            LResult::disableOutput();

            $mh_list = LMigrationSupport::printAllExecutedMigrations();

            $result = [];

            foreach ($mh_list as $mh) $result[] = ">> ".$mh;

            return ['result' => self::SUCCESS_RESULT,'data' => $result];

        } else return $this->failure("Wrong password.");
    }

    public function migrateListMissing($password) {
        if ($this->accessGranted($password)) {

            try {
                $result = $this->loadFrameworkBasicClasses();

                if ($result!==true) return $this->failure($result);
            }
            catch (\Exception $ex) {
                return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
            }

            if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

            LResult::disableOutput();

            $mh_list = LMigrationSupport::printAllMissingMigrations();

            $result = [];

            foreach ($mh_list as $mh) $result[] = ">> ".$mh;

            return ['result' => self::SUCCESS_RESULT,'data' => $result];

        } else return $this->failure("Wrong password.");
    }

    public function fileExists($password,$path) {
        if ($this->accessGranted($password)) {

            $final_path = $this->root_dir->getFullPath().$path;

            if (file_exists($final_path)) return ["result" => self::SUCCESS_RESULT,"data" => 'true'];
            else return ["result" => self::SUCCESS_RESULT,"data" => 'false'];

        } else return $this->failure("Wrong password.");
    }

    public function readFileContent($password,$path) {
        if ($this->accessGranted($password)) {

            $f = new DFile($path);

            if (!$f->exists()) return $this->failure("File at path '".$path."' do not exists on this deployer instance.");

            if (!$f->isReadable()) return $this->failure("File at path '".$path."' is not readable on this deployer instance.");

            $content = $f->getContent();

            return ["result" => self::SUCCESS_RESULT,"data" => $content];

        } else return $this->failure("Wrong password.");
    }

    public function writeFileContent($password,$path,$content) {
        if ($this->accessGranted($password)) {

            $f = new DFile($path);

            $parent_dir = $f->getDirectory();

            if (!$parent_dir->exists()) $parent_dir->touch();

            if ($f->exists() && !$f->isWritable()) return $this->failure("File at path '".$path."' already exists but is not writable in this deployer instance.");

            $f->setContent($content);

            return ["result" => self::SUCCESS_RESULT];

        } else return $this->failure("Wrong password.");
    }

	public function listElements($password,$folder) {
		if ($this->accessGranted($password)) {

			if (!DStringUtils::endsWith($folder,'/')) return $this->failure("Folder name to list does not end with /.");

			$dir = new DDir($this->root_dir->getFullPath().$folder);

			if ($dir->exists() && $dir->isReadable()) {

				$folder_list = $dir->listFolders();
				$file_list = $dir->listFiles();

				$data = [];
				foreach ($folder_list as $f) $data[] = $f->getName().'/';
				foreach ($file_list as $f) $data[] = $f->getFilename();

				return ["result" => self::SUCCESS_RESULT,"data" => $data];

			} else return ["result" => self::SUCCESS_RESULT,"data" => []];

		} else return $this->failure("Wrong password.");
	}

	public function listHashes($password,$excluded_paths,$included_paths) {
		if ($this->accessGranted($password)) {

            if ($this->containsDeployerPath($excluded_paths)) {
                $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
            }

            if ($this->containsDeployerPath($included_paths)) {
                $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
            }

            $inspector = new ContentHashInspector();

            $inspector->setExcludedPaths($this->getFinalPathList($excluded_paths));
            $inspector->setIncludedPaths($this->getFinalPathList($included_paths));

            $pre_include_result = [];

			if (count($inspector->getIncludedPaths())>0) {
				foreach ($inspector->getIncludedPaths() as $path) {
					$my_dir = new DDir($this->root_dir->getFullPath().$path);

					$pre_include_result = array_merge($my_dir->explore($inspector),$pre_include_result);
				}
			} else {
				$pre_include_result = $this->root_dir->explore($inspector);
			}

            $pre_result = array_remove_key_or_value($pre_include_result,'');

            $final_result = [];

            if (empty($inspector->getExcludedPaths()))
                $final_result = $pre_result;
            else {
                foreach ($pre_result as $path => $hash) {
                    $skip = false;
                    foreach ($inspector->getExcludedPaths() as $excluded) {
                        if (DStringUtils::startsWith($path,$excluded)) 
                            $skip = true;
                        if (DStringUtils::startsWith($path,'config/deployer/'))
                            $skip = true;
                    }

                    if (!$skip) $final_result[$path] = $hash;
                }
            }
            $final_result_2 = array_remove_key_or_value($final_result,'');

            return ["result" => self::SUCCESS_RESULT,"data" => $final_result_2];

		} else return $this->failure("Wrong password.");
	}

    public function fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths) {
        if ($this->accessGranted($password)) {

            if (!DFileSystemUtils::isPermissionsFlagsValid($permissions_to_set)) return $this->failure("Permissions flags are not in a valid form!");

            if ($this->containsDeployerPath($excluded_paths)) {
                $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
            }

            if ($this->containsDeployerPath($included_paths)) {
                $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
            }

            $inspector = new PermissionsFixerInspector($permissions_to_set);

            $inspector->setExcludedPaths($this->getFinalPathList($excluded_paths));
            $inspector->setIncludedPaths($this->getFinalPathList($included_paths));

            $pre_include_result = [];

            if (count($inspector->getIncludedPaths())>0) {
                foreach ($inspector->getIncludedPaths() as $path) {
                    $my_dir = new DDir($this->root_dir->getFullPath().$path);

                    $my_dir->explore($inspector);
                }
            } else {
                $this->root_dir->explore($inspector);
            }

            return ["result" => self::SUCCESS_RESULT];

        } else return $this->failure("Wrong password.");
    }

	public function deleteFile($password,$path) {
		if ($this->accessGranted($password)) {

            if (DStringUtils::endsWith($path,'/')) return $this->failure("Use deleteDir to delete directories. Actual path found is : ".$path);

            $full_path = $this->root_dir->getFullPath().$path;

            if (DFileSystemUtils::isDir($full_path)) return $this->failure("Actual path is a directory and should be deleted using deleteDir. Path is : ".$path);

			$f = new DFile($this->root_dir->getFullPath().$path);

			if ($f->exists()) {

				$result = $f->delete();

                if ($f->getPath()==$this->deployer_file->getPath() && $result) {
                    self::$PWD = "";
                }

				return ["result" => self::SUCCESS_RESULT];
			} else return $this->failure("File to delete does not exist.");


		} else return $this->failure("Wrong password.");
	}

	public function makeDir($password,$path) {
		if ($this->accessGranted($password)) {

			if (!DStringUtils::endsWith($path,'/')) return $this->failure("Directory name to create does not end with /.");

			$dest = new DDir($this->root_dir->getFullPath().$path);

			$dest->touch();

			if ($dest->exists()) return ["result" => self::SUCCESS_RESULT];
			else return $this->failure("Unable to create directory.");

		} else return $this->failure("Wrong password.");
	}

	public function deleteDir($password,$path,$recursive) {
		if ($this->accessGranted($password)) {

			if (!DStringUtils::endsWith($path,'/')) return $this->failure("The directory name does not ends with /.");

			$dest = new DDir($this->root_dir->getFullPath().$path);

			if (!$dest->exists()) return $this->failure("Directory to delete does not exist : ".$dest->getFullPath());

			$dest->delete($recursive);

			if (!$dest->exists()) return ["result" => self::SUCCESS_RESULT];
			else return $this->failure("Unable to delete directory.");

		} else return $this->failure("Wrong password.");
	}

	public function copyFile($password,$path) {

		if ($this->accessGranted($password)) {
			if (isset($_FILES['f']) && $_FILES['f']['error'] == UPLOAD_ERR_OK) {

				if (DStringUtils::endsWith($path,'/')) return $this->failure("File name should not end with a directory separator.");

				$content = file_get_contents($_FILES['f']['tmp_name']);

                if ($content===null) return $this->failure("Unable to read content of uploaded file!");

				$dest = new DFile($this->root_dir->getFullPath().$path);

				$dir = $dest->getDirectory();

				if (!$dir->exists()) return $this->failure("Parent directory does not exist.");

				$dest->setContent($content);

                $dest->setPermissions('-rw-r--r--');

				if ($dest->getSize()!=$_FILES['f']['size']) {
					$dest->delete();
					return $this->failure("Size of file is wrong after write.");
				}

				return ["result" => self::SUCCESS_RESULT];

			} else return $this->failure("File 'f' is not present or upload failure.");
		} 
		else return $this->failure("Wrong password.");
	}

	public function downloadDir($password,$path) {
		if ($this->accessGranted($password)) {

			if (!DStringUtils::endsWith($path,'/')) return $this->failure("Folder to zip does not ends with /.");

			$source = new DDir($this->root_dir->getFullPath().$path);

			if (!$source->exists()) return $this->failure("Directory to zip and get does not exist.");

			$zip_file = $this->root_dir->newFile("my_dir.zip");

			DZipUtils::createArchive($zip_file,$source);

			if (!$zip_file->exists()) return $this->failure("Unable to create zip file.");

			return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

		} else $this->failure("Wrong password.");
	}

    //using a trick to avoid replacement

    const ENV_VAR_NAME_MAP = ['PWD' => 'Deployer Password','DPFR' => 'Deployer path from root'];

    public function listEnv($password) {
        if ($this->accessGranted($password)) {
            return ['result' => self::SUCCESS_RESULT,'data' => self::ENV_VAR_NAME_MAP];
        } return $this->failure("Wrong password.");
    }

    public function getEnv($password,$env_var_name) {
        if ($this->accessGranted($password)) {
            if (!isset(self::ENV_VAR_NAME_MAP[$env_var_name])) return $this->failure("Unavailable environment variable to get : ".$env_var_name);

            $data = null;
            if ($env_var_name=='PWD') $data = self::$PWD;
            if ($env_var_name=='DPFR') $data = self::$DPFR;

            return ["result" => self::SUCCESS_RESULT,'data' => $data];
        } else return $this->failure("Wrong password.");
    }

	public function setEnv($password,$env_var_name,$env_var_value) {
		if ($this->accessGranted($password)) {
			$deployer_content = $this->deployer_file->getContent();

            if (!isset(self::ENV_VAR_NAME_MAP[$env_var_name])) return $this->failure("Unavailable environment variable to set : ".$env_var_name);

            if (strpos($env_var_value,',')!==false) {

                $var_value_array = explode(',',$env_var_value);

                $final_string = var_export($var_value_array,true);

                $final_string = str_replace("'",'"',$final_string);

            } else {
                $final_string = var_export($env_var_value,true);

                $final_string = str_replace("'",'"',$final_string);
            }

            $env_var_delimiter = DStringUtils::getCommentDelimitedReplacementsStringSeparator($env_var_name);

			$tokens = explode("/*!".$env_var_delimiter."!*/",$deployer_content);

			$tokens[1] = $final_string;

			$deployer_content = implode("/*!".$env_var_delimiter."!*/",$tokens);

			$this->deployer_file->setContent($deployer_content);

			//using a variable instead of a constant is necessary to be able to make unit test on it ... it's still ok :)
			self::$$env_var_name = $env_var_value;

			return ["result" => self::SUCCESS_RESULT];
		} else return $this->failure("Wrong password.");
	}

	public function hello($password=null) {
		if ($this->accessGranted($password)) {
			return ["result" => self::SUCCESS_RESULT];
		} else return $this->failure("Wrong password.");
	}



	private function accessGranted($password) {
	   if (($this->hasPassword() && self::$PWD==$password) || (!$this->hasPassword() && !$password)) 
            return true;
	   else {

            return false;
       }
        
	}

	public function failure(string $message) {
		return ["result" => self::FAILURE_RESULT,"message" => $message];
	}

	private function hasPassword() {
		return self::$PWD!=null;
	}

    private function getRequestMethod() {
        if (isset($_SERVER['REQUEST_METHOD'])) return $_SERVER['REQUEST_METHOD'];
        else return 'CLI';
    }

    public function preparePostResponse($data) {
        if (is_array($data)) {
            try {
                $final_result = json_encode($data);
                return $final_result;
            } catch (\Exception $ex) {
                return var_export($data);
            }
        }

        return "".$data;
    }

    private function sendFileFromResult($result) {
        if ($result['result']==self::SUCCESS_RESULT) {
            $f = $result['data'];

            if ($f->exists() && $f->getSize()>0) {

                header('Content-Description: File Transfer');
                header('Content-Type: '.mime_content_type ($f->getFullPath()));
                
                $content_disposition = 'inline';
                
                header('Content-Disposition: '.$content_disposition.'; filename="my_dir.zip"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . $f->getSize());
                header('Connection: close');
                flush(); // Flush system output buffer
                readfile($f->getFullPath());
                
                exit();
            } else {

                echo $this->preparePostResponse($this->failure("Unable to find zip file to send to client."));
                exit();
            }
        }
    }

    private function getResultMessage($result) {
        if (is_array($result) && isset($result['message'])) return $result['message'];
        else return "Unknown error";
    }

    private function isSuccess($result) {
        return is_array($result) && $result['result']==self::SUCCESS_RESULT;
    }

    public function processRequest() {
    	if ($this->hasPostParameter('METHOD')) {
    		$method = $this->getPostParameter('METHOD');

    		switch ($method) {
                case 'VERSION' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in VERSION request."));

                    echo $this->preparePostResponse($this->version($password));
                    break;
                }
    			case 'HELLO' : {
					if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in HELLO request."));

					echo $this->preparePostResponse($this->hello($password));
    				break;
    			}
                case 'FILE_EXISTS' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in FILE_EXISTS request."));

                    if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
                    else echo $this->preparePostResponse($this->failure("PATH field missing in FILE_EXISTS request."));

                    echo $this->preparePostResponse($this->fileExists($password,$path));

                    break;
                }
                case 'LIST_DB' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_DB request."));

                    echo $this->preparePostResponse($this->listDb($password));

                    break;
                }
                case 'BACKUP_DB_STRUCTURE' : {

                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in BACKUP_DB_STRUCTURE request."));

                    if ($this->hasPostParameter('CONNECTION_NAME')) $connection_name = $this->getPostParameter('CONNECTION_NAME');
                    else echo $this->preparePostResponse($this->failure("CONNECTION_NAME field missing in BACKUP_DB_STRUCTURE request."));

                    $result = $this->backupDbStructure($password,$connection_name);

                    if ($this->isSuccess($result)) {

                        $this->sendFileFromResult($result);
                    }
                    else {

                        echo $this->preparePostResponse($this->failure("Unable to correctly prepare file to send for backup db structure : ".$this->getResultMessage($result)));
                    }

                    break;
                }
                case 'BACKUP_DB_DATA' : {

                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in BACKUP_DB_DATA request."));

                    if ($this->hasPostParameter('CONNECTION_NAME')) $connection_name = $this->getPostParameter('CONNECTION_NAME');
                    else echo $this->preparePostResponse($this->failure("CONNECTION_NAME field missing in BACKUP_DB_DATA request."));

                    $result = $this->backupDbData($password,$connection_name);

                    if ($this->isSuccess($result)) {
                        $this->sendFileFromResult($result);
                    }
                    else {
                        echo $this->preparePostResponse($this->failure("Unable to correctly prepare file to send for backup db structure : ".$this->getResultMessage($result)));
                    }

                    break;
                }
                case 'MIGRATE_ALL' : {

                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_ALL request."));

                    echo $this->preparePostResponse($this->migrateAll($password));

                    break;
                }
                case 'MIGRATE_RESET' : {

                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_ALL request."));

                    echo $this->preparePostResponse($this->migrateReset($password));

                    break;
                }
                case 'MIGRATE_LIST_DONE' : {

                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_LIST_DONE request."));

                    echo $this->preparePostResponse($this->migrateListDone($password));

                    break;
                }
                case 'MIGRATE_LIST_MISSING' : {

                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_LIST_MISSING request."));

                    echo $this->preparePostResponse($this->migrateListMissing($password));

                    break;
                }
                case 'READ_FILE_CONTENT' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in READ_FILE_CONTENT request."));

                    if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
                    else echo $this->preparePostResponse($this->failure("PATH field missing in READ_FILE_CONTENT request."));

                    echo $this->preparePostResponse($this->readFileContent($password,$path));

                    break;
                }
                case 'WRITE_FILE_CONTENT' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in WRITE_FILE_CONTENT request."));

                    if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
                    else echo $this->preparePostResponse($this->failure("PATH field missing in WRITE_FILE_CONTENT request."));
                    
                    if ($this->hasPostParameter('CONTENT')) {
                        if ($this->accessGranted($password)) {
                            //content is unfiltered
                            $content = $_POST['CONTENT'];
                        } else {
                            if ($this->isPostParameterDangerous('CONTENT')) {
                            echo $this->preparePostResponse($this->failure('Warning!! CONTENT field with potential security issues and wrong password detected!!'));
                            return;
                            } else {
                                echo $this->preparePostResponse($this->failure("Wrong password."));
                                return;
                            }
                        }
                    }    
                    else {
                        echo $this->preparePostResponse($this->failure("CONTENT field missing in WRITE_FILE_CONTENT request."));
                        return;
                    }        

                    echo $this->preparePostResponse($this->writeFileContent($password,$path,$content));

                    break;
                }
                case 'SET_ENV' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in SET_ENV request."));

                    if ($this->hasPostParameter('ENV_VAR_NAME')) $env_var_name = $this->getPostParameter('ENV_VAR_NAME');
                    else echo $this->preparePostResponse($this->failure("ENV_VAR_NAME field missing in SET_ENV request."));

                    if ($this->hasPostParameter('ENV_VAR_VALUE')) $env_var_value = $this->getPostParameter('ENV_VAR_VALUE');
                    else echo $this->preparePostResponse($this->failure("ENV_VAR_VALUE field missing in SET_ENV request."));

                    echo $this->preparePostResponse($this->setEnv($password,$env_var_name,$env_var_value));

                    break;
                }
    			case 'GET_ENV' : {
    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in GET_ENV request."));

					if ($this->hasPostParameter('ENV_VAR_NAME')) $env_var_name = $this->getPostParameter('ENV_VAR_NAME');
					else echo $this->preparePostResponse($this->failure("ENV_VAR_NAME field missing in GET_ENV request."));

					echo $this->preparePostResponse($this->getEnv($password,$env_var_name));

					break;
    			}
                case 'LIST_ENV' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_ENV request."));

                    echo $this->preparePostResponse($this->listEnv($password));

                    break;
                }
    			case 'LIST_ELEMENTS' : {

    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_ELEMENTS request."));

					if (isset($_POST['FOLDER'])) $folder = $this->getPostParameter('FOLDER');
					else echo $this->preparePostResponse($this->failure("FOLDER field missing in LIST_ELEMENTS request."));

					echo $this->preparePostResponse($this->listElements($password,$folder));

    				break;
    			}
    			case 'LIST_HASHES' : {
    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_HASHES request."));

					if ($this->hasPostParameter('EXCLUDED_PATHS')) $excluded_paths = explode(',',$this->getPostParameter('EXCLUDED_PATHS'));
					else echo $this->preparePostResponse($this->failure("EXCLUDED_PATHS field missing in LIST_HASHES request."));

					if ($this->hasPostParameter('INCLUDED_PATHS')) $included_paths = explode(',',$this->getPostParameter('INCLUDED_PATHS'));
					else echo $this->preparePostResponse($this->failure("INCLUDED_PATHS field missing in LIST_HASHES request."));					

					echo $this->preparePostResponse($this->listHashes($password,$excluded_paths,$included_paths));

					break;
    			}
                case 'FIX_PERMISSIONS' : {
                    if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                    else echo $this->preparePostResponse($this->failure("PASSWORD field missing in FIX_PERMISSIONS request."));

                    if (isset($_POST['PERMISSIONS_TO_SET'])) $permissions_to_set = $this->getPostParameter('PERMISSIONS_TO_SET');
                    else echo $this->preparePostResponse($this->failure("PERMISSIONS_TO_SET field missing in FIX_PERMISSIONS request."));

                    if ($this->hasPostParameter('EXCLUDED_PATHS')) $excluded_paths = explode(',',$this->getPostParameter('EXCLUDED_PATHS'));
                    else echo $this->preparePostResponse($this->failure("EXCLUDED_PATHS field missing in FIX_PERMISSIONS request."));

                    if ($this->hasPostParameter('INCLUDED_PATHS')) $included_paths = explode(',',$this->getPostParameter('INCLUDED_PATHS'));
                    else echo $this->preparePostResponse($this->failure("INCLUDED_PATHS field missing in FIX_PERMISSIONS request."));                   

                    echo $this->preparePostResponse($this->fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths));

                    break;
                }
    			case 'COPY_FILE' : {

    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in COPY_FILE request."));

					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
					else echo $this->preparePostResponse($this->failure("PATH field missing in COPY_FILE request."));

					echo $this->preparePostResponse($this->copyFile($password,$path));

					break;
    			}
    			case 'MAKE_DIR' : {
    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MAKE_DIR request."));

					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
					else echo $this->preparePostResponse($this->failure("PATH field missing in MAKE_DIR request."));

					echo $this->preparePostResponse($this->makeDir($password,$path));

					break;
    			}
    			case 'DELETE_DIR' : {
    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in DELETE_DIR request."));

					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
					else echo $this->preparePostResponse($this->failure("PATH field missing in DELETE_DIR request."));

					if ($this->hasPostParameter('RECURSIVE')) $recursive = $this->getPostParameter('RECURSIVE') == 'true' ? true : false;
					else echo $this->preparePostResponse($this->failure("RECURSIVE field missing in DELETE_DIR request."));

					echo $this->preparePostResponse($this->deleteDir($password,$path,$recursive));

					break;
    			}
    			case 'DELETE_FILE' : {
    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in DELETE_FILE request."));

					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
					else echo $this->preparePostResponse($this->failure("PATH field missing in DELETE_FILE request."));

					echo $this->preparePostResponse($this->deleteFile($password,$path));

					break;
    			}
    			case 'DOWNLOAD_DIR' : {
    				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in DOWNLOAD_DIR request."));

					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
					else echo $this->preparePostResponse($this->failure("PATH field missing in DOWNLOAD_DIR request."));

					$result = $this->downloadDir($password,$path);

					$this->sendFileFromResult($result);

					break;
    			}
    			default : echo $this->preparePostResponse($this->failure("Unknown method : ".$method));
    		}
    	} else {
    		echo $this->preparePostResponse($this->failure("METHOD parameter not set in POST."));
    	}
    }
}

//--launch

if (isset($_POST['METHOD'])) {

    try {
       $controller = new DeployerController();

	   $controller->processRequest();

    } catch (\Exception $ex) {
        echo $controller->preparePostResponse($controller->failure("Server got an exception : ".$ex->getMessage()));
    }
} else echo "Hello :)";