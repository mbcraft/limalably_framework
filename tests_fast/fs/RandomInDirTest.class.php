<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class RandomInDirTest extends LTestCase
{
    function testRandomFromFolderNoMtime()
    {
        $count = 0;
        
        $css_count = 0;
        $ext_count = 0;
        $test_count = 0;

        $d = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/");
        
        for($i=0;$i<30;$i++)
        {
            $result = $d->randomFromHere(false);

            $this->assertNotNull($result,"Non sono stati trovati risultati col random nella cartella!");
            
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/css_test.css") $css_count+=1;
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/ext_test.plug.txt") $ext_count += 1;
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/test_file.txt") $test_count += 1;
        
            $sum = $css_count+$ext_count+$test_count;
            $this->assertTrue($count==$sum-1,"Il numero dei risultati trovati non coincide!");
            $count +=1;
        }
    }
    
    function testRandomFromFolderWithFoldersNoMtime()
    {
        $count = 0;
        
        $another_count = 0;
        $css_count = 0;
        $ext_count = 0;
        $test_count = 0;

        $d = new LDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/");
        
        for($i=0;$i<30;$i++)
        {
            
            $result = $d->randomFromHere(false,true);

            $this->assertNotNull($result,"Non è stato trovato nessun risultato col random nella cartella!");
            
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/another_dir/") $another_count+=1;
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/css_test.css") $css_count+=1;
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/ext_test.plug.txt") $ext_count += 1;
            if ($result==$_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/test_file.txt") $test_count += 1;
        
            $sum = $another_count+$css_count+$ext_count+$test_count;
            $this->assertTrue($count==$sum-1,"Il numero dei risultati trovati non coincide!");
            $count +=1;
        }
    }


}
?>