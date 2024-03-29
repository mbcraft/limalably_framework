<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class RemoteHttpsDeployerDbClientTest extends LTestCase {
		const TEST_DIR = "tests";

	private function initEmptyFakeProject() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

	}

	private function initAll() {
		$this->initEmptyFakeProject();
	}

	private function disposeAll() {
		unset($_SERVER['PROJECT_DIR']);

		$backup_save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/backup_save/');
		$backup_save_dir->delete(true);
		$backup_save_dir->touch();
	}

	private function isSuccess($result) {
		if ($result===true) return true;
		else return false;
	}

	private function getErrorMessage($result) {
		if (is_array($result)) return $result['message'];
		else return '';
	}
	
	
	//ok
	function testListDb() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->list_db('remote_http_key');

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}
	
	
	//ok
	function testBackupDbStructure() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$save_dir = new LDir('/home/marco/SoftwareProjects/MBCRAFT/limalably_framework/tmp/');

		$r = $dc->backup_db_structure('remote_http_key','hosting_dreamhost_tests',$save_dir->getFullPath());

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

		$files = $save_dir->listAll();

		$this->assertTrue(count($files)==1,"Il numero di files nella cartella temporanea non corrisponde!");

		$this->assertTrue(end($files)->getSize()>1000,"La dimensione del file non corrisponde a quella attesa!");

		$save_dir->makeEmpty();

	}
	
	//ok
	function testBackupDbData() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$save_dir = new LDir('/home/marco/SoftwareProjects/MBCRAFT/limalably_framework/tmp/');

		$r = $dc->backup_db_data('remote_http_key','hosting_dreamhost_tests',$save_dir->getFullPath());

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

		$files = $save_dir->listAll();

		$this->assertTrue(count($files)==1,"Il numero di files nella cartella temporanea non corrisponde!");

		$this->assertTrue(end($files)->getSize()>1000,"La dimensione del file non corrisponde a quella attesa!");

		$save_dir->makeEmpty();

	}

	
	function testMigrateAll() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->migrate_all('remote_http_key');

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	function testMigrateReset() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->migrate_reset('remote_http_key');

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	function testMigrateListDone() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->migrate_list_done('remote_http_key');

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	function testMigrateListMissing() {

		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/remote_http_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('remote_http_key','wwwroot/deployer.php','https://www.mbcraftlab.it/deployer_test_db/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('remote_http_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->migrate_list_missing('remote_http_key');

		$this->assertTrue($this->isSuccess($r),"La chiamata non è andata a buon fine!");

		$r = $dc->detach('remote_http_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}
}