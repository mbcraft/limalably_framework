<?php


class MigrationHandlerTest extends LTestCase {
	

	public function testFakeMigration() {

		$f = new LFile('tests/db2/migrations/data/AnotherFakeMigrationClass.class.php');

		$mh = new LMigrationHandler($f,'fw');

		$this->assertFalse($mh->isMigrationFile(),"Il file sbagliato viene riconosciuto come migrazione!");

	}

	public function testRealMigration() {

		$f = new LFile('tests/db2/migrations/data/TestMigration123.migration.php');

		$mh = new LMigrationHandler($f,'fw');

		$this->assertTrue($mh->isMigrationFile(),"Il file della migrazione non viene riconosciuto come tale!");

	}

	public function testMigrationExecuteAndRevert() {

		$f = new LFile('tests/db2/migrations/data/TestMigration123.migration.php');

		$mh = new LMigrationHandler($f,'fw');

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project/';
		
		$this->assertEqual($mh->getMigrationLogFile()->getPath(),"config/migrations/ut/fw/TestMigration123.log","Il percorso del file di log della migrazione non coincide!");

		$this->assertTrue($mh->isAlreadyExecuted(),"La migrazione nonostante il log non viene riconosciuta come già eseguita!");

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_no_mig/';
		
		$this->assertFalse($mh->isAlreadyExecuted(),"La migrazione viene riconosciuta come già eseguita!");

		unset($_SERVER['PROJECT_DIR']);
	}

}