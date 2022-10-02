<?php


class DeployerServerTest extends LTestCase {
	
	
	function reinit() {

		$d = new LDir($_SERVER['FRAMEWORK_DIR']."tests/deployer/tmp/");

		if ($d->exists()) $d->delete(true);

		$d->touch();

		$deployer = new LFile($_SERVER['FRAMEWORK_DIR']."tools/deployer.php");

		$deployer->copy($d);

		$f_deployer = new LFile($_SERVER['FRAMEWORK_DIR']."tests/deployer/tmp/deployer.php");

		return $f_deployer->includeFileOnce();

	}


	function testBasicDeployer() {

		
	}



}