<?php



class RemoteHttpsDeployerClientTest extends LTestCase {
	
	const TEST_DIR = 'tests';	
	
	//ok
	function testProjectCheck() {

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('remote_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('remote_key','deployer.php');

		$this->assertTrue($r,"Il set del percorso dalla root non è andato a buon fine!");

		$r = $dc->project_check('remote_key');

		$this->assertTrue($r,"Il check non è stato eseguito con successo!");

		$r = $dc->detach('remote_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

	}
	
	//ok
	function testProjectUpdate() {


		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('remote_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('remote_key','deployer.php');

		$this->assertTrue($r,"Il set del percorso del deployer non è valido");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->project_update('remote_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$r = $dc->temp_clean('remote_key');

		$this->assertTrue($r,"Il clean non è andato a buon fine!");

		$r = $dc->reset('remote_key');

		$this->assertTrue($r,"Il reset non è andato a buon fine!");

		$r = $dc->detach('remote_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

	}
	
	//ok
	function testFrameworkCheck() {

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('remote_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('remote_key','deployer.php');

		$r = $dc->framework_check('remote_key');

		$r = $dc->detach('remote_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

	}
	
	//ok
	function testFrameworkUpdate() {

		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('remote_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('remote_key','deployer.php');

		$r = $dc->framework_update('remote_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$r = $dc->reset('remote_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$r = $dc->detach('remote_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");
	}

	//ok
	function testTempClean() {
		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('remote_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('remote_key','deployer.php');

		$r = $dc->reset('remote_key');

		$this->assertTrue($r,"Il reset non è andato a buon fine!");

		$r = $dc->temp_clean('remote_key');

		$this->assertTrue($r,"Il clean non è andato a buon fine!");

		$r = $dc->detach('remote_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");
	}
	
	//ok
	function testDeployerUpdate() {
		$dc = new LDeployerClient();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_key.key');

		if ($key_file->exists()) $key_file->delete();

		$r = $dc->attach('remote_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->set_deployer_path_from_root('remote_key','deployer.php');

		$r = $dc->deployer_update('remote_key');

		$this->assertTrue($r,"Il deployer_update non è andato a buon fine!");

		$r = $dc->detach('remote_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");
	}
	
}