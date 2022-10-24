<?php


class DeployerClientTest extends LTestCase {
	
	const TEST_DIR = "tests_fast";

	private function initEmptyServer() {
		$deployer_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/');
		$deployer_dir->delete(true);
		$deployer_dir->touch();

		$source_deployer_file = new LFile($_SERVER['FRAMEWORK_DIR'].'tools/deployer.php');

		$target_deployer_file = $deployer_dir->newFile('deployer.php');

		$source_deployer_file->copy($target_deployer_file);
	}

	private function initEmptyFakeProject() {

		$fake_project_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/');
		$fake_project_dir->delete(true);
		$fake_project_dir->touch();

		$fp_config = new LDir($fake_project_dir->getFullPath().'config/');
		$fp_config->touch();

		$fp_config_hostnames = new LDir($fp_config->getFullPath().'hostnames/');
		$fp_config_hostnames->touch();

		$fp_config_mode = new LDir($fp_config->getFullPath().'mode/');
		$fp_config_mode->touch();

		$fp_config_hostnames_my_host = new LDir($fp_config_hostnames->getFullPath().'my_host/');
		$fp_config_hostnames_my_host->touch();

		$cfg = $fp_config_hostnames_my_host->newFile('config.json');
		$cfg->setContent('{}');

		$fp_config_deployer = new LDir($fp_config->getFullPath().'deployer/');
		$fp_config_deployer->touch();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

	}

	private function initAll() {
		$this->initEmptyFakeProject();
		$this->initEmptyServer();
	}

	private function disposeAll() {
		unset($_SERVER['PROJECT_DIR']);
	}

	
	function testAttach() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"Impossibile effettuare l'attach con successo!");

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();


	}
	
	
	function testReset() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}
	

}