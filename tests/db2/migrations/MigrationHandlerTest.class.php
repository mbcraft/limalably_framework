<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

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

	public function testMigrationCheckExecution() {

		$f = new LFile('tests/db2/migrations/data/TestMigration123.migration.php');

		$mh = new LMigrationHandler($f,'fw');

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project/';
		
		LMigrationHelper::logMigration("TestMigration123",'fw');

		$this->assertTrue($mh->isAlreadyExecuted(),"La migrazione nonostante il log non viene riconosciuta come già eseguita!");

		unset($_SERVER['PROJECT_DIR']);

		LMigrationHelper::dropMigrationTable();
	}

	public function testMigrationRunAndRevert() {

		$f = new LFile('tests/db2/migrations/data/TestMigration123.migration.php');

		$mh = new LMigrationHandler($f,'fw');

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_run/';

		$this->assertFalse($mh->isAlreadyExecuted(),"La migrazione risulta già eseguita!");

		$mh->executeIt();

		$this->assertTrue(TestMigration123::executeDone(),"Execute was not done on migration!");

		$this->assertFalse(TestMigration123::revertDone(),"Revert was done on migration!");

		$this->assertTrue($mh->isAlreadyExecuted(),"La migrazione non risulta eseguita!");

		$mh->revertIt();

		$this->assertTrue(TestMigration123::revertDone(),"Revert was not done on migration!");

		$this->assertFalse($mh->isAlreadyExecuted(),"La migrazione risulta già eseguita!");

		unset($_SERVER['PROJECT_DIR']);

		LMigrationHelper::dropMigrationTable();

	}

}