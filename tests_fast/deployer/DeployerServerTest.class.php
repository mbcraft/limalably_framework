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



}