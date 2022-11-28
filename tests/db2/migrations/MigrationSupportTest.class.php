<?php




class MigrationSupportTest extends LTestCase {
	

	public function testPrints() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_wm/';

		$ms = new LMigrationSupport();
		$ms->loadMigrationList();

		$ms->printContextExecutedMigrations();

		$ms->printContextMissingMigrations();


		LMigrationSupport::printAllExecutedMigrations();
		LMigrationSupport::printAllMissingMigrations();

		unset($_SERVER['PROJECT_DIR']);

	}

	public function testExecutionAndRevert() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_wm/';

		$ms = new LMigrationSupport();
		$ms->loadMigrationList();

		$this->assertFalse($ms->hasExecutedAtLeastOneContextMigration(),"Risultano migrazioni già eseguite!");
		$this->assertTrue($ms->hasMoreContextMigrationsToExecute(),"Non ci sono migrazioni da eseguire!");

		$result = $ms->executeNextContextMigration();

		$this->assertTrue($result,"Migration run was not successful");

		$this->assertTrue($ms->hasExecutedAtLeastOneContextMigration(),"Non risultano migrazioni già eseguite!");
		$this->assertTrue($ms->hasMoreContextMigrationsToExecute(),"Non ci sono migrazioni da eseguire!");

		$result2 = $ms->executeNextContextMigration();

		$this->assertTrue($result2,"Migration run was not successful");

		$this->assertTrue($ms->hasExecutedAtLeastOneContextMigration(),"Non risultano migrazioni già eseguite!");
		$this->assertFalse($ms->hasMoreContextMigrationsToExecute(),"Non ci sono migrazioni da eseguire!");

		$result3 = $ms->revertLastContextMigration();

		$this->assertTrue($result2,"Migration run was not successful");

		$result4 = $ms->revertLastContextMigration();

		$this->assertTrue($result2,"Migration run was not successful");

		$this->assertFalse($ms->hasExecutedAtLeastOneContextMigration(),"Risultano migrazioni già eseguite!");
		$this->assertTrue($ms->hasMoreContextMigrationsToExecute(),"Non ci sono migrazioni da eseguire!");

		unset($_SERVER['PROJECT_DIR']);

	}

	public function testExecutionAndRevert2() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_wm/';

		$ms = new LMigrationSupport();
		$ms->loadMigrationList();

		$ms->executeAllContextMigrations();

		$ms->revertAllContextMigrations();

		unset($_SERVER['PROJECT_DIR']);

	}
}