<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

if (!defined("SOFTWARE_DIR")) define("SOFTWARE_DIR","/home/marco/SoftwareProjects/MBCRAFT");

class ActualDeployerProblemsTest extends LTestCase {

const TEST_DIR = "tests";

	private function initLocalHttpWebSiteAndProject() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$deployer_file = new LFile($_SERVER['FRAMEWORK_DIR'].'tools/deployer.php');

		$local_http_deployer_test_dir = new LDir(SOFTWARE_DIR.'/DeployerTestLocalSite/');
		$deployer_file->copy($local_http_deployer_test_dir);

		$this->runFixAllPermissionsScript();
		

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');
		if ($key_file->exists()) $key_file->delete();

	}

	private function runFixAllPermissionsScript() {
		$fix_local_http_deployer_file_permissions_script = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/scripts/fix_http_deployer_local_site_permissions.sh');
		$fix_local_http_deployer_file_permissions_script->execute(LFile::EXECUTE_RESULT_FORMAT_COMMAND_LINE);
	}

	private function initAll() {
		$this->disposeAll();
		$this->initLocalHttpWebSiteAndProject();
	}

	private function disposeAll() {
		unset($_SERVER['PROJECT_DIR']);

		$backup_save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/backup_save/');
		$backup_save_dir->delete(true);
		$backup_save_dir->touch();

		$empty_local_http_deployer_dir_script = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/scripts/delete_http_deployer_local_site_instance.sh');
		$empty_local_http_deployer_dir_script->execute(LFile::EXECUTE_RESULT_FORMAT_COMMAND_LINE);

	}

	private function isSuccess($result) {
		if ($result===true) return true;
		else return false;
	}

	private function getErrorMessage($result) {
		if (is_array($result)) return $result['message'];
		else return '';
	}

	//ok-
	function testGetExecMode() {
		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php','http://deployer__local_test/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->get_exec_mode('default_key');

		$this->assertFalse($r,"Il comando è andato a buon fine senza un file di execution mode!");

		$d = new LDir(SOFTWARE_DIR.'/DeployerTestLocalSite/config/mode/');
		$d->touch();

		$f = $d->newFile('development.txt');
		$f->setContent("howla");

		$r = $dc->get_exec_mode('default_key');

		$this->assertTrue($r,"Il comando non è andato a buon fine e avrebbe dovuto! : ".$this->getErrorMessage($r));

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	//ok-
	function testSetExecMode() {
		$this->initAll();

		$dc = new LDeployerClient();

		$key_file = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/config/deployer/default_key.key');

		if ($key_file->exists()) $key_file->delete();

		$this->assertFalse($key_file->exists(),"Il file della chiave esiste già!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

		$r = $dc->attach('default_key','wwwroot/deployer.php','http://deployer__local_test/deployer.php');

		$this->assertTrue($this->isSuccess($r),"Impossibile effettuare l'attach con successo! : ".$this->getErrorMessage($r));

		$this->assertTrue($key_file->exists(),"Il file della chiave non è stato creato! : ".$key_file->getFullPath());

		$r = $dc->hello('default_key');

		$this->assertTrue($r,"Impossibile verificare correttamente l'accesso col token.");

		$r = $dc->get_exec_mode('default_key');

		$this->assertFalse($r,"Il comando è andato a buon fine senza un file di execution mode!");

		$r = $dc->set_exec_mode('default_key','fd');

		$this->assertTrue($r,"Il comando non è andato a buon fine!");

		$r = $dc->get_exec_mode('default_key');

		$this->assertTrue($r,"Il comando non è stato eseguito con successo! : ".$this->getErrorMessage($r));

		$r = $dc->set_exec_mode('default_key','testing');

		$this->assertTrue($r,"Il comando non è andato a buon fine!");

		$r = $dc->get_exec_mode('default_key');

		$this->assertTrue($r,"Il comando non è stato eseguito con successo!");

		$r = $dc->set_exec_mode('default_key','maintenance');

		$this->assertTrue($r,"Il comando non è andato a buon fine!");

		$r = $dc->get_exec_mode('default_key');

		$this->assertTrue($r,"Il comando non è stato eseguito con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->assertFalse($key_file->exists(),"Il file della chiave non è stato eliminato! : ".$key_file->getFullPath());

		$this->disposeAll();

	}

	//ok-
	function testBackup() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key','wwwroot/deployer.php','http://deployer__local_test/deployer.php');

		$this->assertTrue($this->isSuccess($r),"L'attach non è avvenuto con successo!");

		$backup_save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/backup_save/');

		$backup_save_dir->touch();

		$backup_save_dir->delete(true);

		$backup_save_dir->touch();

		$r = $dc->backup('default_key','/',$backup_save_dir->getFullPath());

		$this->assertTrue($this->isSuccess($r),"La procedura di backup ha dato esito negativo! : ".$this->getErrorMessage($r));

		$file_list = $backup_save_dir->listFiles();

		$this->assertTrue(count($file_list)==1,"The backup save dir does not contain any saved file");

		$this->assertTrue(LStringUtils::endsWith($file_list[0]->getFilename(),'zip'),"The backup saved file is not a zip file");

		$this->assertTrue($file_list[0]->getSize()>0,"The backup file is empty!");

		$r = $dc->detach('default_key');

		$this->disposeAll();
	}
}