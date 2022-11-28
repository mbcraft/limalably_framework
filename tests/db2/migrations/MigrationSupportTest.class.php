<?php




class MigrationSupportTest extends LTestCase {
	

	public function testSomething() {

		$_SERVER['PROJECT_DIR'] = $_SERVER['FRAMEWORK_DIR'].'tests/db2/migrations/fake_project_wm/';

		$ms = new LMigrationSupport();
		$ms->loadMigrationList();

		$ms->printContextExecutedMigrations();

		$ms->printContextMissingMigrations();



	}
}