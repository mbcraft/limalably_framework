<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class PlainDirTest extends LTestCase
{

    function testHasSubdirs()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/advances_dir_list/");
        $this->assertFalse($d->hasSubdirs(),"Sono state trovate sottocartelle in una cartella che non ne ha!!");
    
        $d2 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_source/");
        $this->assertTrue($d2->hasSubdirs(),"Non sono state trovate sottocartelle in una cartella che ne ha!!");
        
    }

    function testFindFilesBasic()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/advances_dir_list/");
        
        $only_menu_ini = $d->findFiles("/[_]*menu[\.]ini/");
        $this->assertTrue(count($only_menu_ini)==1,"Il numero dei file trovati non corrisponde!!");       
        
    }
    
    function testFindFilesStartingWithBasic()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/advances_dir_list/");
        
        $only_the_starting = $d->findFilesStartingWith("the");
        $this->assertTrue(count($only_the_starting)==1,"Il numero dei file trovati non corrisponde!!");       
        
    }

    function testFindFilesEndingWithBasic()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/advances_dir_list/");
        
        $only_image_png = $d->findFilesEndingWith("image.png");
        
        $this->assertTrue(count($only_image_png)==2,"Il numero dei file trovati non corrisponde!!");       
        
    }
    
    function testSubdirs()
    {
        $root = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/");
        
        $subfolder = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir");
        
        $this->assertTrue($root->hasSubdirOrSame($subfolder),"La directory non ha la subdirectory attesa!");
        $this->assertFalse($subfolder->hasSubdirOrSame($root),"La sottodirectory è sobfolder di root!");
        $this->assertTrue($root->hasSubdirOrSame($root),"La root non subfolder o uguale a se stessa!");
        $this->assertTrue($subfolder->hasSubdirOrSame($subfolder),"La subfolder non è subfolder o uguale a se stessa!");
         
    }

    function testDirLevel()
    {
        $level_0 = new LDir("/");
        $this->assertEqual($level_0->getLevel(),0,"Il livello 0 della directory non e' corretto : ".$level_0->getLevel());
        
        $level_1 = new LDir("/test/");
        $this->assertEqual($level_1->getLevel(),1,"Il livello 1 della directory non e' corretto : ".$level_1->getLevel());
        
        $level_3 = new LDir("/test/js/mooo/");
        $this->assertEqual($level_3->getLevel(),3,"Il livello 3 della directory non e' corretto : ".$level_3->getLevel());
        
    }
    
    function testEquals()
    {
        $dir1= new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/");
        
        $dir2 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir");
        
        $this->assertTrue($dir1->equals($dir2),"Le directory non coincidono!!");
    }

    function testRootTestDirectory()
    {
        $d1 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/");

        $this->assertTrue($d1->exists(),"La directory di test non esiste!!!");
        $this->assertTrue($d1->isDir(),"La directory di test non è una directory!!!");
        $this->assertFalse($d1->isFile(),"La directory di test è un file!!!");
        
        $this->assertFalse($d1->isEmpty(),"La directory di test è vuota!!!");
    }

    function testEmptyDirectory()
    {
        $d2 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/empty_dir");

        $d2->touch();

        $this->assertTrue($d2->exists(),"La cartella non esiste!");
        $this->assertTrue($d2->isDir(),"L'elemento non è una cartella!");
        $this->assertFalse($d2->isFile(),"L'elemento è un file ma non dovrebbe esserlo!");
        //$this->assertTrue($d2->isEmpty()); //.svn ???
    }

    function verifyContentDir($content_dir)
    {
        $this->assertTrue($content_dir->isDir(),"L'elemento non è una cartella!");
        $this->assertFalse($content_dir->isEmpty(),"La cartella risulta essere vuota!");
    }

    function verifyEmptyDir($empty_dir)
    {
        $this->assertTrue($empty_dir->isDir(),"La directory vuota non risulta vuota!");

        $subdir = $empty_dir->newSubdir("test");
        
        $this->assertTrue($subdir->isEmpty(),"La subdirectory non risulta vuota!");

        $this->assertTrue($subdir->delete(),"Impossibile cancellare una cartella vuota.");

        $this->assertFalse($subdir->exists(),"La cartella esiste dopo averla cancellata!");
    }

    function testGetParentDir()
    {
        $d1 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/empty_dir");
        
        $parent = $d1->getParentDir();
        
        $this->assertEqual($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/",$parent->getFullPath(),"Le directory non corrispondono!");
    }

    function testDirectoryContent()
    {
        $d1 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/");

        $this->assertTrue($d1->isDir(),"L'elemento non è una directory!");

        $content = $d1->listAll();

        $this->assertEqual(3,count($content),"Il conteggio delle cartelle non corrisponde!"); //.svn ???

        foreach ($content as $dir)
        {
            if ($dir->getName()=="content_dir")
                $this->verifyContentDir($dir);
            else
                $this->verifyEmptyDir($dir);
        }
    }


    function testHasSingleSubdir()
    {
        $dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/single_subdir/");
        
        $this->assertTrue($dir->hasSingleSubdir(),"La cartella non ha un'unica sottocartella!");
        
        $dir2 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/");
        
        $this->assertTrue($dir2->hasSingleSubdir(),"La cartella non ha un'unica sottocartella!");
        
        $dir3 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/single_subdir/blablablax/");
        
        $this->assertFalse($dir3->hasSingleSubdir(),"La cartella ha un'unica sottocartella ma non dovrebbe avercela!");
    }

    function testGetSingleSubdir()
    {
        $dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/single_subdir/");
        
        $sub_dir = $dir->getSingleSubdir();
        
        $this->assertEqual($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/single_subdir/blablablax/",$sub_dir->getFullPath(),"I percorsi delle cartelle non coincidono!");
    }

    function testGetSingleSubdirFailManyElements()
    {
        $dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/content_dir/");
        
        try
        {
            $sub_dir = $dir->getSingleSubdir();
            $this->fail("Il metodo getSingleSubdir non ha lanciato l'eccezione prevista.");
        }
        catch (Exception $ex)
        {
        } 
    }

    function testGetSingleSubdirFailSingleFile()
    {
        $dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/test_dir/single_subdir/blablablax/");
        
        try
        {
            $sub_dir = $dir->getSingleSubdir();
            $this->fail("Il metodo getSingleSubdir non ha lanciato l'eccezione prevista.");
        }
        catch (Exception $ex)
        {
        } 
    }
    
    function testCopy()
    {
        $source_dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_source/");
    
        $target_dir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_target/");
        
        //pulisco la cartella di destinazione
        foreach ($target_dir->listAll() as $f)
            $f->delete(true);
        
        $source_dir_elems = $source_dir->listAll();
        foreach ($source_dir_elems as $elem)
        {
            $elem->copy($target_dir);
        }
        
        $tiny_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_target/my_tiny_file.txt");
        $this->assertTrue($tiny_file->exists(),"Il file non è stato copiato!!");
        $this->assertEqual($tiny_file->getContent(),"TINY TINY TINY","Il contenuto del file non corrisponde!!");
    
        $my_subdir = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_target/my_subdir");
        $this->assertTrue($my_subdir->exists(),"La sottocartella non è stata copiata!!");
        
        $another_file = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/copy_target/my_subdir/another_file.txt");
        $this->assertTrue($another_file->exists(),"Il file non è stato copiato!!");
        $this->assertEqual($another_file->getContent(),"BLA BLA BLA","Il contenuto del file non corrisponde!!");
    
        foreach ($target_dir->listFiles() as $f)
            $f->delete(true);
    }


    function testTouch()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/touch_test2/my_new_dir/");
        $this->assertFalse($d->exists(),"La directory esiste già!");
        $d->touch();
        $this->assertTrue($d->exists(),"La directory non è stata creata!");
        try
        {
            $d->touch();
        //devo poter fare touch senza eccezioni su una directory che già esiste
        }
        catch (Exception $ex)
        {
            $this->fail("Impossibile fare touch() su una cartella già esistente senza lanciare un'eccezione!!");
        }
        
        $d->delete();
        $this->assertFalse($d->exists(),"La directory non è stata cancellata!");
        
        
    }

    function testTouchSubdirs()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/touch_test/my_new_dir/another_dir/again/");
        $this->assertFalse($d->exists(),"La directory esiste già!");
        $d->touch();
        $this->assertTrue($d->exists(),"La directory non è stata creata!");
        try
        {
            $d->touch();
            //devo poter fare touch senza eccezioni su una directory che già esiste
        }
        catch (Exception $ex)
        {
            $this->fail("Impossibile fare touch() su una cartella già esistente senza lanciare un'eccezione!!");
        }

        $d->delete();
        $this->assertFalse($d->exists(),"La directory non è stata cancellata!");

        $d_root = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/touch_test/my_new_dir/");
        $d_root->delete(true);
        $this->assertFalse($d_root->exists(),"La directory root dell'albero esiste ancora!!");

    }

    function testRenameDirs()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/dir/");
        $d->touch();

        $this->assertTrue($d->exists(),"La directory non e' stata creata!!");

        $f1 = $d->newFile("my_file.txt");
        $f1->setContent("Ciao!!");

        $this->assertTrue($f1->exists(),"Il file non e' stato creato nella cartella!!");

        $d2 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/target/");
        $d2->delete(true);
        $this->assertFalse($d2->exists(),"La directory esiste gia'!!");
        $this->assertTrue($d->rename("target"),"Il rename non è andato a buon fine!");

        $this->assertFalse($d->exists(),"La directory non e' stata rinominata con successo!!");
        $this->assertTrue($d2->exists(),"La directory non e' stata rinominata con successo!!");
        $f2 = new LFile($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/target/my_file.txt");
        $this->assertTrue($f2->exists(),"Il file non e' stato spostato insieme alla directory!!");

        $d3 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/existing_dir/");
        $this->assertFalse($d2->rename("existing_dir"),"Il rename e' stato effettuato su una directory che gia' esiste!!");

        $this->assertFalse($d2->isEmpty(),"La directory non spostata non contiene piu' il suo file!!");
        $this->assertTrue($d3->isEmpty(),"La directory gia' esistente e' stata riempita con pattume!!");

        $this->expectException("LIOException");
        $d4 = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/rename_test/another_target/buh/");
        $this->assertFalse($d2->rename("another_target/buh"),"Rename con spostamento andato a buon fine!!");

        $d2->delete(true);
    }

    function testPatternHiddenFiles()
    {
        $pattern = LDir::$showHiddenFiles;
        
        $this->assertTrue(preg_match($pattern[0],"."),"Il pattern non corrisponde!");
        $this->assertTrue(preg_match($pattern[0],".."),"Il pattern non corrisponde!");
        $this->assertFalse(preg_match($pattern[0],".htaccess"),"Il pattern non corrisponde!");
        $this->assertFalse(preg_match($pattern[0],"prova.txt"),"Il pattern non corrisponde!");
    }

    function testPatternNoHiddenFiles()
    {
        $pattern = LDir::$noHiddenFiles;
        
        $this->assertTrue(preg_match($pattern[0],"."),"Il pattern non corrisponde!");
        $this->assertTrue(preg_match($pattern[0],".."),"Il pattern non corrisponde!");
        $this->assertTrue(preg_match($pattern[0],".htaccess"),"Il pattern non corrisponde!");
        $this->assertFalse(preg_match($pattern[0],"prova.txt"),"Il pattern non corrisponde!");
    }

    function testListFiles()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/list_files_test/");
                
        $this->assertEqual(count($d->listFiles()),1,"Il numero di file col list di default non corrisponde!!");
        $this->assertEqual(count($d->listFiles(LDir::DEFAULT_EXCLUDES)),1,"Il numero di file col list di default non corrisponde!!");
        
        $this->assertEqual(count($d->listFiles(LDir::NO_HIDDEN_FILES)),1,"Il numero di file col list di default non corrisponde!!");
        
        $this->assertTrue(count($d->listFiles(LDir::SHOW_HIDDEN_FILES))==2,"Il numero di file col list dei file nascosti non corrisponde!!");
           
        $expected_names = array(".htaccess","plain.txt","a_dir");
        $files = $d->listFiles(LDir::SHOW_HIDDEN_FILES);

        foreach ($files as $f)
        {
            if ($f->isDir())
                $this->assertTrue(in_array($f->getName(),$expected_names),"Il nome della cartella non è stata trovata!!");
            else
                $this->assertTrue(in_array($f->getFilename(),$expected_names),"Il nome del file non è stato trovato!");
        }
    }
     

    function testDeleteEmptyWithHidden()
    {
        if (isset($_SERVER["HTTP_HOST"])) {

            $is_local = strpos($_SERVER["HTTP_HOST"],".")===false;

            if ($is_local)
            {
                $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/delete_test_dir_empty/the_dir/");
                $this->assertTrue($d->exists(),"La cartella dal eliminare non esiste!!");

                

                if (count($d->listFiles(LDir::SHOW_HIDDEN_FILES))==0)
                {
                    $d->delete();
                    $this->assertFalse($d->exists(),"La cartella dal eliminare e' stata eliminata!!");
                }
                else
                {
                    $d->delete();
                    $this->assertTrue($d->exists(),"La cartella dal eliminare non e' stata eliminata!!");
                }
                $d->touch();
            }
        }
    }

    function testDeleteRealEmpty()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/delete_test_dir_empty/real_empty_dir/");
        $this->assertFalse($d->exists(),"La cartella dal eliminare non esiste!!");

        $d->touch();

        $this->assertTrue($d->exists(),"La cartella da eliminare non è stata creata!!");
        $d->delete();
        $this->assertFalse($d->exists(),"La cartella da eliminare non è stata eliminata!!");

    }

    function testDeleteRecursive()
    {
        $d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/delete_test_dir/");

        $d->touch();

        $this->assertTrue($d->exists(),"La cartella dal eliminare non esiste!!");
        $this->assertTrue($d->isEmpty(),"La cartella da popolare non e' vuota!!");

        $the_dir = $d->newSubdir("the_dir");

        $blabla = $the_dir->newFile("blabla.ini");
        $blabla->setContent("[section]\n\nchiave=valore\n\n");
        $hidden_test = $the_dir->newSubdir("hidden_test");
        $htaccess = $hidden_test->newFile(".htaccess");
        $htaccess->setContent("RewriteEngine on\n\n");
        
        $prova = $hidden_test->newFile("prova.txt");
        $prova->setContent("Questo e' un file con un testo di prova");
        
        $the_dir->delete(true);
        $this->assertFalse($the_dir->exists(),"La directory non e' stata eliminata!!");
        $this->assertTrue($d->isEmpty(),"Il contenuto della cartella non e' stato rimosso completamente!!");

    }

    function testGetPathRelative()
    {
        $my_included_file = new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/include_teiop/");

        $rel_path = $my_included_file->getRelativePath(new LDir($_SERVER['FRAMEWORK_DIR']."tests"));
        $this->assertEqual("fs/include_teiop/",$rel_path,"Il percorso relativo non viene elaborato correttamente!! : ".$rel_path);

        $rel_path = $my_included_file->getRelativePath(new LDir($_SERVER['FRAMEWORK_DIR']."tests/fs/"));
        $this->assertEqual("include_teiop/",$rel_path,"Il percorso relativo non viene elaborato correttamente!! : ".$rel_path);

        $this->expectException("LIOException");
        $this->assertNotEqual("/include_teiop/",$my_included_file->getRelativePath(new LDir("/pluto/tests/fs/include_test")),"Il percorso relativo non viene elaborato correttamente!!");

    }

     
}

?>