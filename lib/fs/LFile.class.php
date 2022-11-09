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
    static $content_hash_cache = [];

    const EXECUTE_RESULT_FORMAT_COMMAND_LINE = "command_line";
    const EXECUTE_RESULT_FORMAT_WEB = "web_simple";
    const EXECUTE_RESULT_FORMAT_WEB_LI = "web_li";
    const EXECUTE_RESULT_FORMAT_RAW = "raw";

    function setOwner($user_name) {
        chown($this->__full_path,$user_name);
    }

    function setGroup($group_name) {
        chgrp($this->__full_path,$group_name);
    }

    function execute($result_format) {
        
        $result_format_list = [
            self::EXECUTE_RESULT_FORMAT_COMMAND_LINE,self::EXECUTE_RESULT_FORMAT_WEB,self::EXECUTE_RESULT_FORMAT_RAW];

        if (!in_array($result_format,$result_format_list)) throw new \LIOException("Invalid result format for LFile::execute!");

        $result = [];

        exec($this->__full_path,$result);

        switch ($result_format) {
            case self::EXECUTE_RESULT_FORMAT_COMMAND_LINE : return implode("\n",$result);
            case self::EXECUTE_RESULT_FORMAT_WEB_SIMPLE : return implode("<br />",$result)."<br />";
            case self::EXECUTE_RESULT_FORMAT_WEB_LI : {

                $final_result = "";

                foreach ($result as $rl) {
                    $final_result = "<li>".$rl."</li>";
                }


                return $final_result;
            }
            case self::EXECUTE_RESULT_FORMAT_RAW : return $result;
        }

        throw new \LIOException("Unmanaged result_format in case.");
        
    }

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

    function getLastModificationTime() {
        return filemtime($this->__full_path);
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

    function copy($dest_dir_or_file)
    {      
        if ($dest_dir_or_file instanceof LDir)
        {
            return copy($this->__full_path,$dest_dir_or_file->__full_path.$this->getFilename());
        }
        if ($dest_dir_or_file instanceof LFile) {
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

?>