<?php


class MigrationHelperTest extends LTestCase {
	


	function testMethods() {
		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_run/';

		$dir = LMigrationHelper::getMigrationRunningModeLogDirectory();

		$this->assertNotNull($dir,"La cartella ritornata è nulla!");

		$dir2 = LMigrationHelper::getMigrationLogDirectory('ctx');

		$this->assertNotNull($dir,"La cartella ritornata è nulla!");

		$this->assertTrue(LStringUtils::startsWith($dir2->getPath(),$dir->getPath()),"La seconda cartella non è una sottocartella della prima cartella!");

		$this->assertTrue($dir2->exists(),"La cartella non è stata creata!");

		$dir3 = new LDir($_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_run/config/executed_migrations/');

		if ($dir3->exists()) $dir3->delete(true);

		unset($_SERVER['PROJECT_DIR']);
	}

}