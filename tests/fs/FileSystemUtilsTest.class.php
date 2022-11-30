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


    function testPermissionsFlags() {

        $p1 = '-rwxrwxrwx';
        $p2 = '----------';
        $p3 = '-r--r--r--';
        $p4 = '--w--w--w-';
        $p5 = '---x--x--x';

        $p6 = 'a---------';
        $p7 = '-b--------';

        $this->assertTrue(LFileSystemUtils::isPermissionsFlagsValid($p1),"I flag non sono validi!");
        $this->assertTrue(LFileSystemUtils::isPermissionsFlagsValid($p2),"I flag non sono validi!");
        $this->assertTrue(LFileSystemUtils::isPermissionsFlagsValid($p3),"I flag non sono validi!");
        $this->assertTrue(LFileSystemUtils::isPermissionsFlagsValid($p4),"I flag non sono validi!");
        $this->assertTrue(LFileSystemUtils::isPermissionsFlagsValid($p5),"I flag non sono validi!");

        $this->assertFalse(LFileSystemUtils::isPermissionsFlagsValid($p6),"I flag sono validi!");
        $this->assertFalse(LFileSystemUtils::isPermissionsFlagsValid($p7),"I flag sono validi!");

    }
}


?>