<?php


class MigrationListTest extends LTestCase {
	

	public function testList() {

		$d = new LDir('tests/db2/migrations/data/');

		$ml = new LMigrationList($d,'fw');

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_run/';

		$config_migrations_dir = new LDir($_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_run/config/migrations/');

		$this->assertFalse($config_migrations_dir->exists(),"La cartella usata nella config esiste già!");

		$missing_migrations = $ml->findAllMissingMigrations();

		$this->assertEqual(count($missing_migrations),1,"Il numero di migrazioni mancanti non è corretto!");

		$executed_migrations = $ml->findAllExecutedMigrations();

		$this->assertEqual(count($executed_migrations),0,"Il numero di migrazioni eseguite non è corretto!");

		$mh = $ml->findNextMissingMigration();

		$mh->executeIt();

		$missing_migrations = $ml->findAllMissingMigrations();

		$this->assertEqual(count($missing_migrations),0,"Il numero di migrazioni mancanti non è corretto!");

		$executed_migrations = $ml->findAllExecutedMigrations();

		$this->assertEqual(count($executed_migrations),1,"Il numero di migrazioni eseguite non è corretto!");

		$mh2 = $ml->findLastExecutedMigration();

		$mh2->revertIt();

		$missing_migrations = $ml->findAllMissingMigrations();

		$this->assertEqual(count($missing_migrations),1,"Il numero di migrazioni mancanti non è corretto!");

		$executed_migrations = $ml->findAllExecutedMigrations();

		$this->assertEqual(count($executed_migrations),0,"Il numero di migrazioni eseguite non è corretto!");

		$config_migrations_dir->delete(true);

		$this->assertFalse($config_migrations_dir->exists(),"La cartella usata nella config esiste ancora!");


	}
	
}