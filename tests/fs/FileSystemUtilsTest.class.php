<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class FileSystemUtilsTest extends LTestCase
{

    function testCurrentAndParentDirectory()
    {
        $this->assertTrue(LFileSystemUtils::isCurrentDirName("."),"Qualcosa non funziona nei file system utils!");
        $this->assertTrue(LFileSystemUtils::isParentDirName(".."),"Qualcosa non funziona nei file system utils!");
    }

    function testIsDir()
    {
        $this->assertTrue(LFileSystemUtils::isDir($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/"),"Qualcosa non funziona nei file system utils!");
        
    }

    function testIsFile()
    {
        $this->assertTrue(LFileSystemUtils::isFile($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/FileSystemUtilsTest.class.php"),"Qualcosa non funziona nei file system utils!");
        $this->assertTrue(LFileSystemUtils::isFile($_SERVER['FRAMEWORK_DIR'].FsTestLib::TEST_DIR."/fs/test_dir/content_dir/.hidden_file"),"Qualcosa non funziona nei file system utils!");
    }

}


?>