<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

/*
 * Rappresenta un puntatore a un determinato percorso, in questo caso un file
 */
class LFile extends LFileSystemElement
{
    const TMP_DIR = "temp/";

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

    public static function newTempFile()
    {
        $dir = new LDir(self::TMP_DIR);
        $result = tempnam($dir->getFullPath(),"tmp_");
        if ($result) return new LFile($result);
        else return false;
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

?>