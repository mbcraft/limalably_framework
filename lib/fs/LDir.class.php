<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*
 * Rappresenta un puntatore a un determinato percorso, in questo caso una directory
 */
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

    function explore($inspector)
    {
        $result = [];

        if (!$this->exists()) return $result;

        $path = $this->getPath();

        if (LStringUtils::startsWith($path,$inspector->getExcludedPaths())) return $result;
        
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
       
        return new LDir(self::TMP_DIR);
    }

    function newTempFile($prefix='tmp_') {

        if (!$this->exists()) $this->touch();

        $result = tempnam($this->getFullPath(),$prefix);
        if ($result) return new LFile($result);
        else return false;
    }

    function getContentHash($excluded_paths=[]) {

        if (isset(self::$content_hash_cache[$this->__path])) return self::$content_hash_cache[$this->__path];

        $elements = $this->listAll();

        $all_hashes = "";

        foreach ($elements as $elem) {
            if (!LStringUtils::startsWith($elem->getPath(),$excluded_paths)) {
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
        
        if (!file_exists($this->__full_path.$name)) {
            $result = @mkdir($this->__full_path.$name, LFileSystemElement::getDefaultPermissionsOctal(),true);
        
            if ($result==true) {
                chmod($this->__full_path.$name, LFileSystemElement::getDefaultPermissionsOctal());
                return new LDir($this->__path.$name);
            }
        }
        else return new LDir($this->__full_path.$name);

    }
/*
 * TESTED
 */
    function isEmpty()
    {
        if (!$this->exists()) return true;

        return count($this->listAll())===0;
    }

    function makeEmpty() {

        if (!$this->exists()) return false;

        $elements = $this->listAll();

        foreach ($elements as $el) {
            $el->delete(true);
        }

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
        if (!$this->exists()) throw new \LIOException("Directory does not exists, can't list elements.");

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
        return new LFile($this->__full_path.'/'.$name);
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
            $dir_content = $this->listAll(LDir::SHOW_HIDDEN_FILES);
            foreach ($dir_content as $elem)
            {
                if ($elem instanceof LDir)
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
        if (!$this->exists()) return false;

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
     * Copia una cartella all'interno di un'altra cartella
     */
    function copy($dest_dir)
    {
        $dest_dir_ok = null;

        if (is_string($dest_dir))
            $dest_dir_ok = new LDir($dest_dir);
        if ($dest_dir instanceof LDir)
            $dest_dir_ok = $dest_dir;

        if ($dest_dir_ok)
        {                      
            $all_elems = $this->listAll();

            foreach ($all_elems as $elem)
            {
                if ($elem instanceof LFile) {
                    $elem->copy($dest_dir_ok);
                    continue;
                }
                if ($elem instanceof LDir)
                {
                    $subdir = $dest_dir_ok->newSubdir($elem->getName());
                    $elem->copy($subdir);
                    continue;
                }
                throw new \LIOException("Unable to copy element of class : ".get_class($elem));
            }
        } else throw new \LIOException("dest_dir is not a valid path or LDir instance!");

    }

    function isParentOf($folder)
    {
        if ($folder instanceof LDir)
            $d = $folder;
        else
            $d = new LDir($folder);

        $path_a = $this->getFullPath();
        $path_b = $d->getFullPath();

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

?>