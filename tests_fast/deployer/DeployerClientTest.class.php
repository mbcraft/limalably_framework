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

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/fake_project/';

	}

	private function initAll() {
		$this->initEmptyFakeProject();
		$this->initEmptyServer();
	}

	private function disposeAll() {
		unset($_SERVER['PROJECT_DIR']);

		$deployer_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/');
		$deployer_dir->delete(true);
		$deployer_dir->touch();
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

		$this->assertTrue($r,"Il check non è stato eseguito con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}


	function testProjectUpdate() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->project_update('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$f1 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/project_file.txt');

		$this->assertTrue($f1->exists(),"Il file di progetto non è stato copiato con successo!");

		$f2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/project_dir/my_file.txt');

		$this->assertTrue($f2->exists(),"Il file di progetto nella sottodirectory non è stato copiato con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}


	function testFrameworkCheck() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->framework_check('default_key');

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}

	function testFrameworkUpdate() {
		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->framework_update('default_key');

		$this->assertTrue($r,"L'update non è andato a buon fine!");

		$f1 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/lymz_framework/SampleClass.class.php');

		$this->assertTrue($f1->exists(),"Il file del framework non è stato copiato con successo!");

		$f2 = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/lymz_framework/bin/sample_command.sh');

		$this->assertTrue($f2->exists(),"Il file nella sottodirectory del framework non è stato copiato con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();
	}

	function testDisappear() {
		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$fd = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($fd->exists(),"Il deployer non è al suo posto!");

		$r = $dc->disappear('default_key');

		$this->assertTrue($r,"Il comando di disappear non è andato a buon fine!");

		$this->assertFalse($fd->exists(),"Il deployer non è stato cancellato!");
	}

	function testAutoConfig() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->auto_config('default_key');

		$this->assertFalse($r,"La procedura di auto_config ha dato esito positivo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();

	}

	function testManualConfig() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$host_config = new LFile($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/config/hostnames/my_host/config.json');

		$this->assertFalse($host_config->exists(),"Il file di configurazione esiste già nella destinazione e non dovrebbe!");

		$r = $dc->manual_config('default_key','my_host');

		$this->assertTrue($r,"La procedura di manual_config ha dato esito negativo!");

		$this->assertTrue($host_config->exists(),"Il file di configurazione non è stato copiato con successo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();

	}

	function testDeployerVersion() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$r = $dc->deployer_version('default_key');

		$this->assertTrue($r,"La procedura di version ha dato esito negativo!");

		$r = $dc->detach('default_key');

		$this->assertTrue($r,"Il detach non è avvenuto con successo!");

		$this->disposeAll();

	}

	function testBackup() {

		$this->initAll();

		$dc = new LDeployerClient();

		$r = $dc->attach('default_key',$_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/tmp/deployer.php');

		$this->assertTrue($r,"L'attach non è avvenuto con successo!");

		$backup_save_dir = new LDir($_SERVER['FRAMEWORK_DIR'].self::TEST_DIR.'/deployer/backup_save/');

		$backup_save_dir->touch();

		$backup_save_dir->delete(true);

		$backup_save_dir->touch();

		$r = $dc->backup('default_key',$backup_save_dir->getFullPath());

		$this->assertTrue($r,"La procedura di backup ha dato esito negativo!");

		$file_list = $backup_save_dir->listFiles();

		$this->assertTrue(count($file_list)==1,"The backup save dir does not contain any saved file");

		$this->assertTrue(LStringUtils::endsWith($file_list[0]->getFilename(),'zip'),"The backup saved file is not a zip file");

		$this->assertTrue($file_list[0]->getSize()>0,"The backup file is empty!");

		$r = $dc->detach('default_key');

		$this->disposeAll();
	}


}