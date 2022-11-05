<?php



class RemoteRemoteDeployerClientTest extends LTestCase {
	
	const TEST_DIR = 'tests_fast';	
	
	/*
	function testProjectCheck() {


		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('default_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('default_key','mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"Il set del percorso dalla root non è andato a buon fine!");

		$r = $dc->project_check('default_key');

		$this->assertTrue($r,"Il check non è stato eseguito con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

	}
	*/
	/*
	function testProjectUpdate() {


		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('default_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('default_key','mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"Il set del percorso del deployer non è valido");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->project_update('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$r = $dc->reset('default_key');

		$this->assertTrue($r,"Il reset non è andato a buon fine!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

	}

	function testFrameworkCheck() {

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('default_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('default_key','mbcraftlab.it/deployer_test/deployer.php');

		$r = $dc->framework_check('default_key');

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

	}
	
	function testFrameworkUpdate() {

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('default_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('default_key','mbcraftlab.it/deployer_test/deployer.php');

		$r = $dc->framework_update('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$r = $dc->reset('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");
	}
	*/
}