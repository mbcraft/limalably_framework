<?php


class DeployerServerTest extends LTestCase {
	
	
	function reinit() {

		$d = new LDir($_SERVER['FRAMEWORK_DIR']."tests_fast/deployer/tmp/");

		if ($d->exists()) $d->delete(true);

		$d->touch();

		$deployer = new LFile($_SERVER['FRAMEWORK_DIR']."tools/deployer.php");

		$deployer->copy($d);

		$f_deployer = new LFile($_SERVER['FRAMEWORK_DIR']."tests_fast/deployer/tmp/deployer.php");

		return $f_deployer->includeFileOnce();

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

	function testDeployerHello() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->hello("123prova123");

		$this->assertFalse($this->isSuccess($result),"La chiamata non è fallita ma doveva!");
	
	}

	function testDeployerChangePassword() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->changePassword("","123prova123");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->hello("123prova123");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->changePassword("123prova123","");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");


	}

	function testDeployerCopyFile() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/a.txt');

		$this->assertFalse($f_dest->exists(),"Il file è già dove non dovrebbe essere!");

		$this->pushFile($f_source);

		$result = $deployer_controller->copyFile("","/a.txt");

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/a.txt');

		$this->assertTrue($f_dest->exists(),"Il file non è stato copiato!");

	}

	function testDeployerMakeDir() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$dest_dir = new LDir($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/prova1/');

		$this->assertFalse($dest_dir->exists(),"La directory da creare esiste già!");

		$result = $deployer_controller->makeDir("","prova1/");

		$this->assertTrue($this->isSuccess($result),"La chiamata non è andata a buon fine!");

		$this->assertTrue($dest_dir->exists(),"La directory da creare non è stata creata!");

		$result = $deployer_controller->makeDir("","prova2");

		$this->assertFalse($this->isSuccess($result),"La directory che non termina con il separator è stata creata!");

		$result = $deployer_controller->makeDir("","nested_dir/prova/");

		$this->assertTrue($this->isSuccess($result),"La directory con sottodirectory non è stata creata!");

		$dest_dir2 = new LDir($_SERVER['FRAMEWORK_DIR'],'tests_fast/deployer/tmp/nested_dir/prova/');

		$this->assertTrue($dest_dir2->exists(),"La directory con sottodirectory non è stata creata!");

	}

	function testDeployerDeleteFile() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$f_source = new LFile($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/data/a.txt');

		$f_dest = new LFile($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/a.txt');

		$r = $f_source->copy($f_dest);

		$this->assertTrue($r,"Il file non è stato copiato con successo!");

		$this->assertTrue($f_dest->exists(),"Il file da cancellare non è stato copiato!");

		$result = $deployer_controller->deleteFile("","a.txt");

		$this->assertTrue($this->isSuccess($result),"Il file non è stato eliminato correttamente!");

		$f_source->copy($f_dest);

		$this->assertTrue($f_dest->exists(),"Il file da cancellare non è stato copiato!");

		$result = $deployer_controller->deleteFile("","/a.txt");

		$this->assertTrue($this->isSuccess($result),"Il file non è stato eliminato correttamente!");

		$dir_dest = new LDir($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/prova/');

		$dir_dest->touch();

		$f_dest2 = new LFile($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/prova/a.txt');

		$f_source->copy($f_dest2);

		$result = $deployer_controller->deleteFile("","/prova/a.txt");

		$this->assertTrue($this->isSuccess($result),"Il file non è stato eliminato correttamente!");

		$this->assertFalse($f_dest2->exists(),"Il file non è stato veramente cancellato!");

	}

	function testDeployerDeleteDir() {

		$this->reinit();

		$deployer_controller = new DeployerController();

		$result = $deployer_controller->hello();

		$this->assertTrue($this->isSuccess($result),"La chiamata non ha dato esito positivo!");

		$d_dir = new LDir($_SERVER['FRAMEWORK_DIR'].'tests_fast/deployer/tmp/prova/');

		$d_dir->touch();

		$this->assertTrue($d_dir->exists(),"La directory da cancellare non è stata creata!");

		$result = $deployer_controller->deleteDir("","prova",false);

		$this->assertFalse($this->isSuccess($result),"La directory senza slash finale è stata cancellata!");

		$result = $deployer_controller->deleteDir("","prova/",false);

		$this->assertTrue($this->isSuccess($result),"La directory non è stata eliminata con successo!");

	}



}