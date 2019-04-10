<?php
/* This software is released under the BSD license. Full text at project root -> license.txt */

/*
 * Rappresenta un puntatore a un determinato percorso, in questo caso una directory
 */
class LDir extends LFileSystemElement
{
    const FILTER_ALL_DIRECTORIES = 1;
    const FILTER_ALL_FILES = 2;
    const FILTER_ALL_ELEMENTS = 3;

    const DEFAULT_EXCLUDES = "NO_HIDDEN_FILES";

    const NO_HIDDEN_FILES = "NO_HIDDEN_FILES";
    static $noHiddenFiles = array("/\A\..*\Z/");
    const SHOW_HIDDEN_FILES = "SHOW_HIDDEN_FILES";
    static $showHiddenFiles = array("/\A[\.][\.]?\Z/");

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

        $target_path = $parent_dir->getPath()."/".$new_name;

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
        return count($this->listFiles())===0;
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
                if ($this->isDir())
                    $partial_path = $this->__path.$element;
                if ($filter & self::FILTER_ALL_DIRECTORIES == self::FILTER_ALL_DIRECTORIES) {
                    if (LFileSystemUtils::isDir($this->__path.$element))
                        $all_dirs[] = new LDir($partial_path);
                }
                if ($filter & self::FILTER_ALL_FILES == self::FILTER_ALL_FILES) {
                    if (LFileSystemUtils::isFile($this->__path.'/'.$element))
                        $all_files[] = new LFile($partial_path);
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
    
    function findElementsEndingWith($string,$filter = self::FILTER_ALL_ELEMENTS)
    {
        $dot_escaped = str_replace(".", "[\.]", $string);
        return $this->findElements("/".$dot_escaped."\Z/",$filter);
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
                if ($filter & self::FILTER_ALL_DIRECTORIES == self::FILTER_ALL_DIRECTORIES) {
                    if (LFileSystemUtils::isDir($this->__path.$element))
                        $all_dirs[] = new LDir($partial_path);
                }
                if ($filter & self::FILTER_ALL_FILES == self::FILTER_ALL_FILES) {
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
            $dir_content = $this->listFiles(LDir::SHOW_HIDDEN_FILES);
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
            
            $all_files = $this->listFiles();
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

}

?>