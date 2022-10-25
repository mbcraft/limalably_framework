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

		$fp_project_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/project_file.txt');
		$fp_project_file->touch();

		$fp_project_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/project_dir/');
		$fp_project_dir->touch();

		$fp_project_dir_file = $fp_project_dir->newFile('my_file.txt');
		$fp_project_dir_file->touch();

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

	}

	private function initAll() {
		$this->initEmptyFakeProject();
		$this->initEmptyServer();
	}

	private function disposeAll() {
		unset($_SERVER['PROJECT_DIR']);
	}

	
	function testAttachDetach() {

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

		$enemy_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/enemy.txt');

		$this->assertFalse($enemy_file->exists(),"Il file intruso esiste già!");

		$enemy_file->touch();

		$this->assertTrue($enemy_file->exists(),"Il file intruso non esiste ma dovrebbe!");

		$r = $dc->reset('default_key');

		$this->assertTrue($r,"La procedura di reset non funziona correttamente!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	function testTempClean() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$other_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/other/');

		$other_dir->touch();

		$temp_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/temp/');

		$temp_dir->touch();

		$enemy_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/temp/enemy.txt');

		$this->assertFalse($enemy_file->exists(),"Il file intruso esiste già!");

		$enemy_file->touch();

		$this->assertTrue($enemy_file->exists(),"Il file intruso non esiste ma dovrebbe!");

		$r = $dc->temp_clean('default_key');

		$this->assertTrue($r,"La procedura di reset non funziona correttamente!");

		$this->assertFalse($enemy_file->exists(),"Il file intruso non è stato ripulito!");

		$this->assertTrue($other_dir->exists(),"L'altra directory è stata cancellata senza motivo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	function testDeployerUpdate() {

		$this->initAll();

		$df = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($df->exists(),"Il deployer non è stato trovato al suo posto!");

		$time1 = $df->getLastModificationTime();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		sleep(1);

		$r = $dc->deployer_update('default_key');

		$time2 = $df->getLastModificationTime();

		$this->assertTrue($time2>$time1,"Il deployer non è stato aggiornato con l'ultima versione!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}

	function testProjectCheck() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->project_check('default_key');

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}
	

}