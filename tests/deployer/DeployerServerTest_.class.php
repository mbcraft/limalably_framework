<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class DeployerServerTest extends LTestCase {
	
	const TEST_DIR = "tests";
	
	function reinit() {

		$d = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR."/deployer/tmp/");

		if ($d->exists()) $d->delete(true);

		$d->touch();

		$deployer = new LFile($_SERVER['FRAMEWORK_DIR']."tools/deployer.php");

		$deployer->copy($d);

		$f_deployer = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR."/deployer/tmp/deployer.php");

		$f_deployer->requireFileOnce();

	}

	function isSuccess($result) {
		return $result['result']==':)';
	}

	function pushFile($file) {

		if (!$file instanceof LFile) throw new \Exception("Parameter is not actually an LFile instance");

		if (isset($_FILES['f'])) unset($_FILES['f']);

		$_FILES['f'] = array();
		$_FILES['f']['error'] = UPLOAD_ERR_OK;
		$_FILES['f']['size'] = $file->getSize();
		$_FILES['f']['tmp_name'] = $file->getFullPath();
	}

    private function getResultMessage($result) {
        if (is_array($result) && isset($result['message'])) return $result['message'];
        else return "Unknown error";
    }
	
	function testListDb() {
		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata a hello non ha dato esito positivo!");

		$result = $deployer_controller->listDb("");

		$this->assertTrue($this->isSuccess($result),"La chiamata a listDb non ha dato esito positivo!");

		$this->assertEqual(count($result['data']),3,"Il numero di risultati non è quello atteso, != 3!");
	}

	function testBackupDbStructure() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata a hello non ha dato esito positivo!");

		$result = $deployer_controller->backupDbStructure("","hosting_dreamhost_tests");

		$this->assertTrue($this->isSuccess($result),"La chiamata a backupDbStructure non ha dato esito positivo : ".$this->getResultMessage($result));

		$this->assertTrue($result['data'] instanceof DFile,"L'elemento restituito non è un file!");

		$this->assertTrue($result['data']->getSize()>300,"Il file ritornato risulta essere vuoto!");

		echo "\nResult file is for backup db structure is : ".$result['data']->getSize()."\n";
	}

	
	function testBackupDbData() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata a hello non ha dato esito positivo!");

		$result = $deployer_controller->backupDbData("","hosting_dreamhost_tests");

		$this->assertTrue($this->isSuccess($result),"La chiamata a backupDbData non ha dato esito positivo!");

		$this->assertTrue($result['data'] instanceof DFile,"L'elemento restituito non è un file!");

		$this->assertTrue($result['data']->getSize()>300,"Il file ritornato risulta essere vuoto!");

		echo "\nResult file is for backup db data is : ".$result['data']->getSize()."\n";
	}
	
	//ok
	function testFileExists() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR."/deployer/tmp/file_that_exists.txt");
		$f->touch();

		$result = $deployer_controller->fileExists("","file_that_exists.txt");

		$this->assertTrue($this->isSuccess($result),"La chiamata è fallita ma non doveva!");

		$this->assertEqual($result['data'],'true',"La chiamata ha un risultato che non corrisponde a quello atteso!");

		$result = $deployer_controller->fileExists("","file_that_do_not_exists.txt");

		$this->assertTrue($this->isSuccess($result),"La chiamata è fallita ma non doveva!");

		$this->assertEqual($result['data'],'false',"La chiamata ha un risultato che non corrisponde a quello atteso!");

	}

	//ok
	function testReadFileContent() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->readFileContent("","file_that_do_not_exists.txt");

		$this->assertFalse($this->isSuccess($result),"Il file che non esiste non da esito negativo nella chiamata!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR."/deployer/tmp/file_with_content.txt");
		$f->setContent("Hello! :)");

		$result = $deployer_controller->readFileContent("","file_with_content.txt");

		$this->assertTrue($this->isSuccess($result),"La chiamata è fallita ma non doveva!");

		$this->assertEqual($result['data'],"Hello! :)","Il risultato della chiamata non corrisponde a quello atteso!");

	}

	//ok
	function testWriteFileContent() {
		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->writeFileContent("","my_dir/file_with_content_from_write.txt","Hello again! :)");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR."/deployer/tmp/my_dir/file_with_content_from_write.txt");

		$this->assertTrue($f->exists(),"Il file non è stato creato!");

		$file_content = $f->getContent();

		$this->assertEqual($file_content,"Hello again! :)","Il contenuto del file non corrisponde a quello previsto!");
	}

	//ok
	function testDeployerHello() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->hello("123prova123");

		$this->assertFalse($this->isSuccess($result),"La chiamata non è fallita ma doveva!");
	
	}
	
	//ok
	function testDeployerChangePassword() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->setEnv("","PWD","123prova123");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->hello("123prova123");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->setEnv("123prova123","PWD","");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");


	}

	//ok
	function testDeployerCopyFile() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/a.txt');

		$this->assertFalse($f_dest->exists(),"Il file è già dove non dovrebbe essere!");

		$this->pushFile($f_source);

		$result = $deployer_controller->copyFile("","/a.txt");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/a.txt');

		$this->assertTrue($f_dest->exists(),"Il file non è stato copiato!");

	}

	//ok
	function testDeployerMakeDir() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$dest_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova1/');

		$this->assertFalse($dest_dir->exists(),"La directory da creare esiste già!");

		$result = $deployer_controller->makeDir("","prova1/");

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertTrue($dest_dir->exists(),"La directory da creare non è stata creata!");

		$result = $deployer_controller->makeDir("","prova2");

		$this->assertFalse($this->isSuccess($result),"La directory che non termina con il separator è stata creata!");

		$result = $deployer_controller->makeDir("","nested_dir/prova/");

		$this->assertTrue($this->isSuccess($result),"La directory con sottodirectory non è stata creata!");

		$dest_dir2 = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/nested_dir/prova/');

		$this->assertTrue($dest_dir2->exists(),"La directory con sottodirectory non è stata creata!");

	}

	//ok
	function testDeployerDeleteFile() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/a.txt');

		$r = $f_source->copy($f_dest);

		$this->assertTrue($r,"Il file non è stato copiato con successo!");

		$this->assertTrue($f_dest->exists(),"Il file da cancellare non è stato copiato!");

		$result = $deployer_controller->deleteFile("","a.txt");

		$this->assertTrue($this->isSuccess($result),"Il file non è stato eliminato correttamente!");

		$f_source->copy($f_dest);

		$this->assertTrue($f_dest->exists(),"Il file da cancellare non è stato copiato!");

		$result = $deployer_controller->deleteFile("","/a.txt");

		$this->assertTrue($this->isSuccess($result),"Il file non è stato eliminato correttamente!");

		$dir_dest = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/');

		$dir_dest->touch();

		$f_dest2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/a.txt');

		$f_source->copy($f_dest2);

		$result = $deployer_controller->deleteFile("","/prova/a.txt");

		$this->assertTrue($this->isSuccess($result),"Il file non è stato eliminato correttamente!");

		$this->assertFalse($f_dest2->exists(),"Il file non è stato veramente cancellato!");

	}

	//ok
	function testDeployerDeleteDir() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$d_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/');

		$d_dir->touch();

		$this->assertTrue($d_dir->exists(),"La directory da cancellare non è stata creata!");

		$result = $deployer_controller->deleteDir("","prova",false);

		$this->assertFalse($this->isSuccess($result),"La directory senza slash finale è stata cancellata!");

		$result = $deployer_controller->deleteDir("","prova/",false);

		$this->assertTrue($this->isSuccess($result),"La directory non è stata eliminata con successo!");

	}

	//ok
	function testDeployerListElements() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$d_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/');

		$d_dir->touch();

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/a.txt');

		$r = $f_source->copy($f_dest);

		$result = $deployer_controller->listElements("","/");

		$this->assertTrue($this->isSuccess($result),"L'elenco non viene restituito correttamente!");

		$data = $result['data'];

		$this->assertEqual(count($data),3,"Il numero di elementi ritornati non corrisponde!");
		$this->assertEqual($data[0],"prova/","La cartella non è stata ritornata correttamente!");
		$this->assertEqual($data[1],"a.txt","Il file non è stato ritornato correttamente!");
		$this->assertEqual($data[2],"deployer.php","Il file non è stato ritornato correttamente!");
	}

	function testInArray() {

		$element = '/prova/';

		$data = ['/deployer.php','/prova/','/a.txt'];

		$this->assertTrue(in_array($element,$data),"L'elemento non è stato trovato nell'array!");


	}
	
	//ok
	function testDeployerListHashes() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$d_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/');

		$d_dir->touch();

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/a.txt');

		$f_dest2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/a.txt');

		$f_source->copy($f_dest);

		$f_source->copy($f_dest2);

		$result = $deployer_controller->listHashes("",['@'],[]);

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertEqual(count($result['data']),3,"Il numero di elementi ritornati non corrisponde!");

		$result = $deployer_controller->listHashes("",['@'],[]);

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertEqual(count($result['data']),3,"Il numero di elementi ritornati non corrisponde!");

		$result = $deployer_controller->listHashes("",['@','prova/'],[]);

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertEqual(count($result['data']),1,"Il numero di elementi ritornati non corrisponde!");

	}
	
	//ok
	function testDeployerDownloadDir() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$d_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/');

		$d_dir->touch();

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/a.txt');

		$f_dest2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/prova/a.txt');

		$f_source->copy($f_dest);

		$f_source->copy($f_dest2);

		$result = $deployer_controller->downloadDir("","/prova");

		$this->assertFalse($this->isSuccess($result),"La chiamata è andata a buon fine senza lo slash finale!");

		$result = $deployer_controller->downloadDir("","/prova/");

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertTrue($result['data'] instanceof DFile,"L'elemento restituito non è un file!");
	}
	
}