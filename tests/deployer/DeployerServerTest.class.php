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

		$result = $deployer_controller->listHashes("",[],[]);

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertEqual(count($result['data']),4,"Il numero di elementi ritornati non corrisponde!");

		$result = $deployer_controller->listHashes("",[],[]);

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertEqual(count($result['data']),4,"Il numero di elementi ritornati non corrisponde!");

		$result = $deployer_controller->listHashes("",['prova/'],[]);

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertEqual(count($result['data']),2,"Il numero di elementi ritornati non corrisponde!");

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