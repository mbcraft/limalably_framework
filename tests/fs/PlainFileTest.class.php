<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class StaticTestDumpRegistry
{
    public static $my_var = 1;
}

class PlainFileTest extends LTestCase
{
    function testLastAccessTime()
    {
        $f1 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_1.dat");
        $f2 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_2.dat");

        //$this->assertEqual("1306482979",$f1->getLastAccessTime());
        //$this->assertEqual("1306482979",$f2->getLastAccessTime());
    }

    function testContentHash()
    {
        $f1 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/test_file.txt");

        $this->assertEqual("bca20547e94049e1ffea27223581c567022a5774",$f1->getContentHash(),"L'hash del file non corrisponde!!");
    }

    function testModificationTime()
    {
        $f1 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_1.dat");
        $f2 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_2.dat");

        //$this->assertEqual("1306338653",$f1->getModificationTime());
        //$this->assertEqual("1306338653",$f2->getModificationTime());
    }

    function testAccessLessThanOrEqualModificationTime()
    {
        $f1 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_1.dat");
        $f2 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/another_dir/dont_touch_me_or_tests_will_fail_2.dat");

        //$this->assertTrue($f1->getLastAccessTime()<$f1->getModificationTime());
        //$this->assertTrue($f1->getLastAccessTime()>$f2->getModificationTime());
    }

    function testSetGetContent()
    {
        $f = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/test_file.txt");

        $current_content = $f->getContent();

        $this->assertEqual("Test content",$current_content,"Il contenuto non corrisponde!");

        $f->setContent("");

        $this->assertEqual("",$f->getContent(),"Il contenuto non corrisponde!");

        $f->setContent("BLA BLA BLA\nBLA BLA\nBLA BLA BLA BLA\nBLA BLA BLA");

        $this->assertEqual("BLA BLA BLA\nBLA BLA\nBLA BLA BLA BLA\nBLA BLA BLA",$f->getContent(),"Il contenuto non corrisponde!");

        $f->setContent("Test content");

        $this->assertEqual("Test content",$f->getContent(),"Il contenuto non corrisponde!");
    }


    function testFilename()
    {
        $f_txt = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/test_file.txt");
        $this->assertEqual("test_file.txt", $f_txt->getFilename(),"Il nome del file non corrisponde!");

        $f_css = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/css_test.css");
        $this->assertEqual("css_test.css", $f_css->getFilename(),"Il nome del file non corrisponde!");

        $f_plug_txt = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/ext_test.plug.txt");
        $this->assertEqual("ext_test.plug.txt", $f_plug_txt->getFilename(),"Il nome del file non corrisponde!");
    }

    function testExtensionFullAndLast()
    {
        $f_txt = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/test_file.txt");
        $this->assertEqual("txt", $f_txt->getFullExtension(),"L'estensione completa non corrisponde!");

        $this->assertEqual("txt", $f_txt->getExtension(),"L'estensione non corrisponde!");

        $f_css = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/css_test.css");
        $this->assertEqual("css", $f_css->getFullExtension(),"L'estensione completa non corrisponde!");

        $this->assertEqual("css", $f_css->getExtension(),"L'estensione non corrisponde!");

        $f_plug_txt = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/ext_test.plug.txt");
        $this->assertEqual("plug.txt", $f_plug_txt->getFullExtension(),"L'estensione completa non corrisponde!");
   
        $this->assertEqual("txt", $f_plug_txt->getExtension(),"L'estensione non corrisponde!");


    }

    function testGetSize()
    {
        $f_test_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/test_file.txt");
        $f_ext_test = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/ext_test.plug.txt");

        $this->assertEqual(12,$f_test_file->getSize(),"La dimensione del file non corrisponde!");
        $this->assertEqual(0,$f_ext_test->getSize(),"La dimensione del file non corrisponde!");
    } 

    function testCreateNewFile()
    {
        $f_new = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/new_file.txt");

        $this->assertNotEqual("", $f_new->getFullPath(),"Il percorso completo è nullo!");
        $this->assertNotNull($f_new->getFullPath(),"Il percorso completo è nullo!");

        //$current_mtime = $f_new->getModificationTime();

        $this->assertFalse($f_new->exists(),"Il file esiste!!");
        $f_new->touch();

        //$new_mtime = $f_new->getModificationTime();

        //$this->assertNotEqual($current_mtime, $new_mtime);

        $this->assertTrue($f_new->exists(),"Il file non è stato creato!");
        $f_new->delete();
        
        $this->assertFalse($f_new->exists(),"Il file esiste!!");
        
    }
    
    function testCopy()
    {
        $source_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_source/my_tiny_file.txt");
        $target_dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_target/");
        
        $target_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_target/my_tiny_file.txt");
        $this->assertFalse($target_file->exists(),"Il file esiste già prima di essere copiato!");
        $source_file->copy($target_dir);
        $this->assertTrue($target_file->exists(),"Il file non è stato copiato!!");
        $target_file->delete();
        $this->assertFalse($target_file->exists(),"Il file non è stato eliminato!");
        
    }

    function testIncludeFileOnce()
    {
        $my_var = 1;

        $my_included_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/include_test/include_me_once.php.inc");

        $this->assertTrue($my_included_file->exists(),"Il file non esiste!");
        $this->assertEqual($my_var,1,"La variabile e' stata modificata!!");

        $this->assertFalse(function_exists("this_is_a_new_function"),"La funzione da caricare e' gia' presente!!");

        $my_included_file->includeFileOnce();

        $this->assertTrue(function_exists("this_is_a_new_function"),"La funzione da caricare non e' stata caricata!!");

        $my_included_file->includeFileOnce();

        $this->assertEqual($my_var,1,"La variabile e' stata incrementata!!");
    }

    function testIncludeFile()
    {
        $my_included_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/include_test/include_me.php.inc");

        $this->assertEqual(StaticTestDumpRegistry::$my_var,1,"La variabile e' stata modificata!!");

        $my_included_file->includeFile();

        $this->assertEqual(StaticTestDumpRegistry::$my_var,2,"La variabile non e' stata incrementata!!");

        $my_included_file->includeFile();

        $this->assertEqual(StaticTestDumpRegistry::$my_var,3,"La variabile non e' stata incrementata!!");
    }

    function testGetPathRelative()
    {
        $my_included_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/include_test/include_me.php.inc");

        $this->assertEqual("fs/include_test/include_me.php.inc",$my_included_file->getRelativePath(new LDir($_SERVER['FRAMEWORK_DIR']."tests")),"Il percorso relativo non viene elaborato correttamente!!");

        $this->assertEqual("include_me.php.inc",$my_included_file->getRelativePath(new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/include_test")),"Il percorso relativo non viene elaborato correttamente!!");

        $this->expectException("LIOException");
        $this->assertEqual("include_me.php.inc",$my_included_file->getRelativePath(new LDir("/pluto/tests/fs/include_test")),"Il percorso relativo non viene elaborato correttamente!!");

    }

    function testIncludeAndDelete()
    {
        $f = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/files_to_include/include_and_delete_me.php.inc");

        $this->assertTrue($f->exists(),"Il file da includere e cancellare non esiste!!");

        $this->assertFalse(class_exists("IncludeDeletedClass"),"La classe IncludeDeletedClass esiste prima dell'inclusione del file.");

        $f->requireFileOnce();

        $this->assertTrue(class_exists("IncludeDeletedClass"),"La classe IncludeDeletedClass non e' stata caricata dopo l'inclusione del file.");
       
        $content = $f->getContent();

        $f->delete();

        $this->assertFalse($f->exists(),"Il file da includere e cancellare non e' stato eliminato!!");

        $f->touch();

        $f->setContent($content);

        $this->assertTrue($f->exists(),"Il file da includere e cancellare non e' stato rigenerato!!");

    }

    function testBlackHoleExists()
    {
        $f = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/BlackHoleTest.class.php");

        $this->assertTrue($f->exists(),"Il test black hole e' stato eliminato!!");
    }

    function testRenameFiles()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/a/");
        $d->touch();

        $f1 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/a/my_file.txt");
        $this->assertFalse($f1->exists(),"Il file f1 esiste!!");

        $f1->setContent("Ciao!!");

        $this->assertTrue($f1->exists(),"Il file f1 non esiste!!");

        $f3 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/a/another_name_again.txt");
        $this->assertFalse($f3->exists(),"Il file f3 esiste gia'!!");
        $f1->rename("another_name_again.txt");

        $this->assertFalse($f1->exists(),"Il file f1 esiste ancora!!");

        $this->assertTrue($f3->exists(),"Il rename non e' andato a buon fine!!");

        $f3->delete();
    }

}

?>