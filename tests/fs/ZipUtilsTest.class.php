<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class ZipUtilsTest extends LTestCase
{
    function testCreateArchive()
    {
        //controllo l'esistenza delle cartelle di test da utilizzare
        $create_dir = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/create/");
        $this->assertTrue($create_dir->exists(),"La directory create non esiste!!");
        
        $save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/saved/");
        $save_dir->touch();
        $this->assertTrue($save_dir->exists(),"La directory save non esiste!!");
        
        
        $target_file = new LFile($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/saved/test_archive.zip");
        $this->assertFalse($target_file->exists(),"Lo zip esiste già!");
        
        $dir_to_zip = $_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/create/";
        
        LZipUtils::createArchive($target_file,$dir_to_zip);
        
        $this->assertTrue($target_file->exists(),"Il file zip non è stato creato!!");
        $this->assertTrue($target_file->getSize()>0,"Il file creato ha dimensione vuota!!");
        
        $target_file->delete();
        $this->assertFalse($target_file->exists(),"Il file zip non è stato eliminato!!");
        
        $saved_files = $save_dir->listFiles();
        foreach ($saved_files as $f)
        {
            $f->delete(true);
        }
    }
    
    function testExtractArchive()
    {
        $create_dir = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/create/");
        $this->assertTrue($create_dir->exists(),"La directory create non esiste!!");
        
        $save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/saved/");
        $save_dir->touch();
        $this->assertTrue($save_dir->exists(),"La directory save non esiste!!");
        
        $extract_dir = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/extract/");
        $extract_dir->touch();
        $this->assertTrue($extract_dir->exists(),"La directory extract non esiste!!");
        
        $extract_dir_files = $extract_dir->listFiles();
        foreach ($extract_dir_files as $f)
        {
            $f->delete(true);
        }
        
        $target_file = new LFile($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/saved/test_archive.zip");
        $this->assertFalse($target_file->exists(),"Lo zip esiste già!");
        
        $dir_to_zip = $_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/create/";
        
        LZipUtils::createArchive($target_file,$dir_to_zip);
        
        $this->assertTrue($target_file->exists(),"Il file zip non è stato creato!!");
        $this->assertTrue($target_file->getSize()>0,"Il file creato ha dimensione vuota!!");
        
        //ora estraggo l'archivio
        $extract_root = $_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/zip_test/extract/";
        
        
        LZipUtils::expandArchive($target_file, $extract_root);
        
        $this->assertEqual(count($extract_dir->listAll()),3,"Il numero dei file estratti non corrisponde!!");
        $f1 = new LFile($extract_root."my_file_01.php");
        $this->assertTrue($f1->exists(),"Il file my_file_01.php non e' stato estratto!!");
        $this->assertTrue(!$f1->isEmpty(),"Il file my_file_01.php e' vuoto!!");
        
        $d1 = new LDir($extract_root."another_dir/");
        $d2 = new LDir($extract_root."my_subdir/");
        $f2 = new LFile($extract_root."another_dir/blabla.ini");
        $this->assertTrue($f2->exists(),"Il file blabla.ini non e' stato estratto!!");
        $this->assertTrue(!$f2->isEmpty(),"Il file blabla.ini e' vuoto!!");
                
        $saved_files = $save_dir->listFiles();
        foreach ($saved_files as $f)
        {
            $f->delete(true);
        }
        
        $extracted_files = $extract_dir->listAll();
        foreach ($extracted_files as $f)
        {
            $f->delete(true);
        }
        
    }
}

?>