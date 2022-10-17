<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/


if (!class_exists("LIOException")) {

	//begin--
	class LIOException extends \Exception
	{
	    function  __construct($message, $code=null, $previous=null) {
	        parent::__construct($message);
	    }
	}
	//end--
}

if (!class_exists("LFileSystemElement")) {

	//begin ---
	define('DS','/');

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
	    
	    public function __construct($path)
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

	        if (LFileSystemUtils::isDir($this->__full_path) && !LStringUtils::endsWith($this->__full_path,'/')) {
	            $this->__path .= '/';
	            $this->__full_path .= '/';
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
	            if ($relative_to instanceof LDir)
	                $path = $relative_to->getPath();
	            else
	                $path = $relative_to;
	            if (strpos($this->__path,$path)===0)
	            {
	                return $this->prepareRelativePath(substr($this->__path,strlen($path)));
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

	//end--
}

if (!class_exists("LFileSystemUtils")) {

	//begin--
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


	}
	//end--


}

if (!class_exists("LDir")) {

	//begin--
	class LDir extends LFileSystemElement
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
	    
	    function visit($visitor)
	    {
	        $visitor->visit($this);
	        
	        $all_folders = $this->listFolders();
	        
	        foreach ($all_folders as $fold)
	        {
	            $visitor->visit($fold);
	        }
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
	        preg_match_all("/\//", $this->__path,$matches);
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
	       
	        return new LDir(self::TMP_DIR);
	    }

	    function newTempFile($prefix='tmp_') {

	        if (!$this->exists()) $this->touch();

	        $result = tempnam($this->getFullPath(),$prefix);
	        if ($result) return new LFile($result);
	        else return false;
	    }

	    function getContentHash() {

	        if (isset(self::$content_hash_cache[$this->__path])) return self::$content_hash_cache[$this->__path];

	        $elements = $this->listAll();

	        $all_hashes = "";

	        foreach ($elements as $elem) {
	            $all_hashes .= $elem->getContentHash();
	        }

	        $result = sha1($all_hashes);

	        self::$content_hash_cache[$this->__path] = $result;

	        return $result;
	    }


	    function getParentDir()
	    {
	        $parent_dir = dirname($this->__full_path);
	        
	        return new LDir($parent_dir);
	    }

	    /*
	   * Rinomina il file senza effettuare spostamenti di sorta.
	   * */
	    function rename($new_name)
	    {
	        if (strstr($new_name,"/")!==false)
	            throw new \LIOException("The new name contains invalid characters : / !!");

	        $parent_dir = $this->getParentDir();

	        $target_path = $parent_dir->getFullPath()."/".$new_name;

	        $target_dir = new LDir($target_path);
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
	        if (LFileSystemUtils::isDir($this->__path.'/'.$name))
	        {
	            //directory already exists
	            //echo "Directory already exists : ".$this->__full_path."/".$name;
	            return new LDir($this->__path.'/'.$name);
	        }
	        if (LFileSystemUtils::isFile($this->__path.'/'.$name))
	        {
	            throw new \LIOException("A file with this name already exists");
	        }
	        //directory or files do not exists
	        
	        $result = @mkdir($this->__full_path.$name, LFileSystemElement::getDefaultPermissionsOctal(),true);
	        
	        
	        if ($result==true) {
	            chmod($this->__full_path.$name, LFileSystemElement::getDefaultPermissionsOctal());
	            return new LDir($this->__path.$name);
	        }
	        else
	        {
	            throw new \LIOException("Unable to create dir : ".$this->__full_path.$name);
	        }

	    }
	/*
	 * TESTED
	 */
	    function isEmpty()
	    {
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
	                    if (LFileSystemUtils::isDir($final_path))
	                        $all_dirs[] = new LDir($final_path.'/');
	                }
	                if (($filter & self::FILTER_ALL_FILES) == self::FILTER_ALL_FILES) {
	                    if (LFileSystemUtils::isFile($final_path))
	                        $all_files[] = new LFile($final_path);
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
	                    if (LFileSystemUtils::isDir($this->__path.$element))
	                        $all_dirs[] = new LDir($partial_path);
	                }
	                if (($filter & self::FILTER_ALL_FILES) == self::FILTER_ALL_FILES) {
	                    if (LFileSystemUtils::isFile($this->__path.DS.$element))
	                        $all_files[] = new LFile($partial_path);
	                }
	            }                

	        }
	      
	        return array_merge($all_dirs, $all_files);
	    }

	    function newFile($name)
	    {
	        return new LFile($this->__path.'/'.$name);
	    }

	    /*
	     * Cancella la cartella. $recursive è true, cancella anche tutto il contenuto ricorsivamente.
	     * Ritorna true se l'operazione è riuscita, false altrimenti.
	     */
	    function delete($recursive = false)
	    {
	        if ($recursive)
	        {
	            $dir_content = $this->listAll(LDir::SHOW_HIDDEN_FILES);
	            foreach ($dir_content as $elem)
	            {
	                if ($elem instanceof LDir)
	                    $elem->delete(true);
	                else
	                    $elem->delete();
	            }
	        }

	        return @rmdir($this->__full_path);
	    }
	    
	    function hasSingleSubdir()
	    {
	        $content = $this->listFolders();
	        if (count($content)==1)
	        {
	            $dir_elem = $content[0];
	            if ($dir_elem->isDir()) return true;
	        }
	        return false;
	    }
	    
	    function getSingleSubdir()
	    {
	        $content = $this->listFolders();
	        if (count($content)==1)
	        {
	            $dir_elem = $content[0];
	            if ($dir_elem->isDir()) return $dir_elem;
	            throw new \LIOException("The element inside the folder is not a folder.");
	        }
	        throw new \LIOException("Unable to find a single subdir. Too many folders found:".count($content));
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
	     * Copia una cartella all'interno di un'altra sottocartella
	     */
	    function copy($path,$new_name=null)
	    {
	        if ($path instanceof LDir)
	            $target_dir = $path;
	        else
	            $target_dir = new LDir($path);

	        if ($target_dir instanceof LDir)
	        {          
	            if ($new_name==null)
	                $new_name = $this->getName();
	            
	            $copy_dir = $target_dir->newSubdir($new_name);
	            
	            $all_files = $this->listAll();
	            foreach ($all_files as $elem)
	            {
	                $elem->copy($copy_dir);
	            }
	        }

	    }

	    function isParentOf($folder)
	    {
	        if ($folder instanceof LDir)
	            $d = $folder;
	        else
	            $d = new LDir($folder);

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

	    function randomFromHere($autocache=true,$include_sub_dirs=false)
	    {
	        if (!$this->exists())
	            Log::error ("FileUtils::randomFromFolder", "La cartella $path non esiste!!");
	        
	        if (!$this->isDir())
	            Log::error ("FileUtils::randomFromFolder", "Il percorso $path non rappresenta una cartella!!");
	            
	        $results = $this->listFiles();
	               
	        $valid_results = array();
	        
	        foreach ($results as $dir_elem)
	        {
	            if ($dir_elem->isDir() && $include_sub_dirs)
	                $valid_results[] = $dir_elem;
	            if ($dir_elem->isFile())
	                $valid_results[] = $dir_elem;
	        }
	        
	        if (count($valid_results)==0)
	            LLog::error("FileUtils::randomFromFolder","Non sono stati trovati risultati validi!!");
	        
	        $selected = $valid_results[rand(0,count($valid_results)-1)];
	        
	        $final_result = $selected->getFullPath();
	        if ($autocache)
	            $final_result.= "?mtime=".$selected->getModificationTime();
	        
	        return $final_result;
	    }

	}
	//end--

}


if (!class_exists("LFile")) {

	//begin--
	/*
	 * Rappresenta un puntatore a un determinato percorso, in questo caso un file
	 */
	class LFile extends LFileSystemElement
	{
	    static $content_hash_cache = [];

	    function getDirectory()
	    {
	        return new LDir(dirname($this->__full_path));
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
	            throw new \LIOException("The name contains forbidden characters : / !!");

	        $this_dir = $this->getDirectory();

	        $target_path = $this_dir->getPath()."/".$new_name;

	        $target_file = new LFile($target_path);
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

	    function getContentHash()
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
	        return @unlink($this->__full_path);
	    }

	    function isEmpty()
	    {
	        return $this->getSize()==0;
	    }

	    function copy($target_dir,$new_name=null)
	    {      
	        if ($target_dir instanceof LDir)
	        {
	            if ($new_name==null)
	                $new_name = $this->getFilename();
	            return copy($this->__full_path,$target_dir->__full_path.'/'.$new_name);
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
	        if (!$this->exists()) throw new \LIOException("Unable to open file reader at path : ".$this->__full_path.". The file does not exist!!");


	        $handle = fopen($this->__full_path,"r");

	        if ($handle===false) throw new \LIOException("Unable to open file reader at path : ".$this->__full_path.".");

	        if (flock($handle, LOCK_SH))
	        {
	            return new LFileReader($handle);
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

	        if ($handle===false) throw new \LIOException("Unable to open file writer at path : ".$this->__full_path);

	        if (flock($handle,LOCK_EX))
	        {
	            return new LFileWriter($handle);
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
	//end--

}

if (!class_exists('LZipUtils')) {

	//begin--
	class LZipUtils
	{
	    public static function expandArchive($zip_file,$target_folder)
	    {
	        $zip_archive = new ZipArchive();
	     
	        if ($zip_file instanceof LFile)
	            $real_zip_file = $zip_file;
	        else
	            $real_zip_file = new LFile($zip_file);
	        
	        
	        if ($target_folder instanceof LDir)
	            $target_dir = $target_folder;
	        else
	            $target_dir = new LDir($target_folder);
	        
	        $zip_archive->open($real_zip_file->getFullPath());
	        
	        $zip_archive->extractTo($target_dir->getFullPath());
	        
	        $zip_archive->close();
	    }
	    
	    public static function createArchive($save_file,$folder_to_zip,$local_dir="/")
	    {
	        if ($folder_to_zip instanceof LDir)
	            $dir_to_zip = $folder_to_zip;
	        else
	            $dir_to_zip = new LDir($folder_to_zip);
	        
	        $zip_archive = new ZipArchive();

	        $zip_archive->open($save_file->getFullPath(),  ZipArchive::CREATE);

	        LZipUtils::recursiveZipFolder($zip_archive, $dir_to_zip,$local_dir);

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
	                LZipUtils::recursiveZipFolder($zip_archive, $dir_entry,$local_dir.$dir_entry->getName().'/');
	            }
	        }
	    }
	}
	//end--


}

//starting deployer controller ---

class DeployerController {

	private $deployer_file;
	private $root_dir;

	const PASSWORD = /*!PWD!*/""/*!PWD!*/;

	const SUCCESS_RESULT = ":)";
	const FAILURE_RESULT = ":(";

	function __construct() {
		$this->deployer_file = new LFile(__FILE__);
		$this->root_dir = new LDir(__DIR__);
	}

	private $visit_result = [];

	private $excluded_paths = [];
	private $included_paths = [];

	public function visit($dir) {

		if (!in_array($dir->getPath(),$this->excluded_paths)) {
			$this->visit_result[$dir->getPath()] = $dir->getContentHash();

			$files = $dir->listFiles();

			foreach ($files as $f) {
				if (!in_array($f->getPath(),$this->excluded_paths)) {
					$this->visit_result[$f->getPath()] = $f->getContentHash();
				}
			}
		}

	}

	public function listElements($password,$folder) {
		if ($this->accessGranted($password)) {

			$dir = new LDir($folder);

			if ($dir->exists() && $dir->isReadable()) {

				$folder_list = $dir->listFolders();
				$file_list = $dir->listFiles();

				$data = [];
				foreach ($folder_list as $f) $data[] = $f->getName().'/';
				foreach ($file_list as $f) $data[] = $f->getName();

				return ["result" => self::SUCCESS_RESULT,"data" => $data];

			} else return $this->failure("Unable to find folder : ".$folder);

		} else return $this->failure("Wrong password.");
	}

	public function listHashes($password,$excluded_paths,$included_paths) {
		if ($this->accessGranted($password)) {
			$this->excluded_paths = $excluded_paths;
			$this->included_paths = $included_paths;

			if (count($this->included_paths)>0) {
				foreach ($this->included_paths as $dp) {
					$my_dir = new LDir($dp);

					$my_dir->visit($this);
				}
			} else {
				$this->root_dir->visit($this);
			}

			return ["result" => self::SUCCESS_RESULT,"data" => $this->visit_result];

		} else return $this->failure("Wrong password.");
	}

	public function deleteFile($password,$path) {
		if ($this->accessGranted($password)) {

			$f = new LFile($this->root_dir->getFullPath().$path);

			if ($f->exists()) {
				$f->delete();

				return ["result" => self::SUCCESS_RESULT];
			} else return $this->failure("File to delete does not exist.");


		} else return $this->failure("Wrong password.");
	}

	public function makeDir($password,$path) {
		if ($this->accessGranted($password)) {

			$dest = new LDir($this->root_dir->getFullPath().$path);

			$dest->touch();

			if ($dest->exists()) return ["result" => self::SUCCESS_RESULT];
			else return $this->failure("Unable to create directory.");

		} else return $this->failure("Wrong password.");
	}

	public function deleteDir($password,$path,$recursive) {
		if ($this->accessGranted($password)) {

			$dest = new LDir($this->root_dir->getFullPath().$path);

			if (!$dest->exists()) return $this->failure("Directory to delete does not exist.");

			$dest->delete($recursive);

			if (!$this->exists()) return ["result" => self::SUCCESS_RESULT];
			else return $this->failure("Unable to delete directory.");

		} else return $this->failure("Wrong password.");
	}

	public function copyFile($password,$path) {
		if ($this->accessGranted($password)) {
			if (isset($_FILES['f']) && $_FILES['f']['error'] == UPLOAD_ERR_OK) {

				$content = file_get_contents($_FILES['f']['tmp_name']);

				$dest = new LFile($this->root_dir->getFullPath().$path);

				$dir = $dest->getDirectory();

				if (!$dir->exists()) return $this->failure("Parent directory does not exist.");

				$dest->setContent($content);

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

			$source = new LDir($this->root_dir->getFullPath().$path);

			if (!$source->exists()) return $this->failure("Directory to zip and get does not exist.");

			$zip_file = $this->root_dir->newFile("my_dir.zip");

			if ($zip_file->exists()) $zip_file->delete(); //...

			LZipUtils::createArchive($zip_file,$source);

			if (!$zip_file->exists()) return $this->failure("Unable to create zip file.");

			return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

		} else $this->failure("Wrong password.");
	}

	public function changePassword($old_password,$new_password) {
		if ($this->accessGranted($password)) {
			$deployer_content = $this->deployer_file->getContent();

			$tokens = explode('\/*!PWD!*/',$deployer_content);

			$tokens[1] = '"'.$new_password.'"';

			$deployer_content = implode('\/*!PWD!*/',$tokens);

			$this->deployer_file->setContent($deployer_content);

			return ["result" => self::SUCCESS_RESULT];
		} else return $this->failure("Wrong password.");
	}

	public function hello($password=null) {
		if ($this->accessGranted($password)) {
			return ["result" => self::SUCCESS_RESULT];
		} else return $this->failure("Wrong password.");
	}

	private function accessGranted($password) {
		if (($this->hasPassword() && self::PASSWORD==$password) || !$password) return true;
		else return false;
	}

	private function failure(string $message) {
		return ["result" => self::FAILURE_RESULT,"message" => $message];
	}

	private function hasPassword() {
		return self::PASSWORD!=null;
	}

    private function getRequestMethod() {
        if (isset($_SERVER['REQUEST_METHOD'])) return $_SERVER['REQUEST_METHOD'];
        else return 'CLI';
    }

    public function processRequest() {
    	if (isset($_POST['METHOD'])) {
    		$method = $_POST['METHOD'];

    		switch ($method) {
    			case 'HELLO' : {
					if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in HELLO request."));

					echo json_encode($this->hello($password));
    				break;
    			}
    			case 'CHANGE_PASSWORD' : {
    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in CHANGE_PASSWORD request."));

					if (isset($_POST['NEW_PASSWORD'])) $new_password = $_POST['NEW_PASSWORD'];
					else echo json_encode($this->failure("NEW_PASSWORD field missing in CHANGE_PASSWORD request."));

					$this->changePassword($password,$new_password);

					break;
    			}
    			case 'LIST_ELEMENTS' : {

    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in LIST_ELEMENTS request."));

					if (isset($_POST['FOLDER'])) $folder = $_POST['FOLDER'];
					else echo json_encode($this->failure("FOLDER field missing in LIST_ELEMENTS request."));

					echo json_encode($this->listElements($password,$folder));

    				break;
    			}
    			case 'LIST_HASHES' : {
    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in LIST_HASHES request."));

					if (isset($_POST['EXCLUDED_PATHS'])) $excluded_paths = explode(',',$_POST['EXCLUDED_PATHS']);
					else echo json_encode($this->failure("EXCLUDED_PATHS field missing in LIST_HASHES request."));

					if (isset($_POST['INCLUDED_PATHS'])) $included_paths = explode(',',$_POST['INCLUDED_PATHS']);
					else echo json_encode($this->failure("INCLUDED_PATHS field missing in LIST_HASHES request."));					

					echo json_encode($this->listHashes($password,$excluded_paths,$included_paths));

					break;
    			}
    			case 'COPY_FILE' : {
    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in COPY_FILE request."));

					if (isset($_POST['PATH'])) $path = $_POST['PATH'];
					else echo json_encode($this->failure("PATH field missing in COPY_FILE request."));

					echo json_encode($this->copyFile($password,$path));

					break;
    			}
    			case 'DELETE_DIR' : {
    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in DELETE_DIR request."));

					if (isset($_POST['PATH'])) $path = $_POST['PATH'];
					else echo json_encode($this->failure("PATH field missing in DELETE_DIR request."));

					if (isset($_POST['RECURSIVE'])) $recursive = $_POST['RECURSIVE'] == 'true' ? true : false;
					else echo json_encode($this->failure("RECURSIVE field missing in DELETE_DIR request."));

					echo json_encode($this->deleteDir($password,$path));

					break;
    			}
    			case 'DELETE_FILE' : {
    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in DELETE_FILE request."));

					if (isset($_POST['PATH'])) $path = $_POST['PATH'];
					else echo json_encode($this->failure("PATH field missing in DELETE_FILE request."));

					echo json_encode($this->deleteFile($password,$path));

					break;
    			}
    			case 'DOWNLOAD_DIR' : {
    				if (isset($_POST['PASSWORD'])) $password = $_POST['PASSWORD'];
					else echo json_encode($this->failure("PASSWORD field missing in DOWNLOAD_DIR request."));

					if (isset($_POST['PATH'])) $path = $_POST['PATH'];
					else echo json_encode($this->failure("PATH field missing in DOWNLOAD_DIR request."));

					$result = $this->downloadDir($password,$path);

					if ($result['result']==self::SUCCESS_RESULT) {
						$f = $result['data'];

				        header('Content-Description: File Transfer');
				        header('Content-Type: '.mime_content_type ($f->getFullPath()));
				        
				        $content_disposition = 'inline';
				        
				        header('Content-Disposition: '.$content_disposition.'; filename="my_dir.zip"');
				        header('Expires: 0');
				        header('Cache-Control: must-revalidate');
				        header('Pragma: public');
				        header('Content-Length: ' . filesize($f->getFullPath());
				        header('Connection: close');
				        flush(); // Flush system output buffer
				        readfile($f->getFullPath());
				        exit();
					}

					break;
    			}
    			default : echo json_encode($this->failure("Unknown method : ".$method));
    		}
    	} else {
    		echo json_encode($this->failure("METHOD parameter not set in POST."));
    	}
    }
}

//--launch

if (isset($_POST['METHOD'])) {

	$controller = new DeployerController();
	$controller->processRequest();

}