<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMigrationSupport {
	
	const PROJECT_MIGRATION_DIRECTORY = "migrations/";

	private $lmigration_list = null;
	private $context = null;

	public function loadMigrationList($folder=self::PROJECT_MIGRATION_DIRECTORY,$context='default') {

		$this->lmigration_list = new LMigrationList(new LDir($folder),$context);

		$this->context = $context;
	}

	public function printContextExecutedMigrations($child_mode=false) {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		$mh_list = $this->lmigration_list->findAllExecutedMigrations();

		LResult::messagenl("Executed migration list for context ".LMigrationHelper::getCleanContextName($this->context)." :");

		if (empty($mh_list)) {
			LResult::messagenl("No migrations found.");
		}
		else
			foreach ($mh_list as $f) {

			LResult::messagenl(">> ".$f->getName()." at ".$f->getExecutionTime());;
		}

		if (!$child_mode) LResult::messagenl("... done!");

	}

	public function printContextMissingMigrations($child_mode=false) {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		$mh_list = $this->lmigration_list->findAllMissingMigrations();

		LResult::messagenl("Missing migrations for context ".LMigrationHelper::getCleanContextName($this->context)." : ");

		if (empty($mh_list)) {

			LResult::messagenl("No migrations found.");
		}
		else
			foreach ($mh_list as $mh) {
				LResult::messagenl(">> ".$mh->getName());
		}

		if (!$child_mode) LResult::messagenl("... done!");
	}

	public static function resetAllMigrations() {

		$db = db();

		LResult::messagenl("Resetting all migrations and cleaning up the database on connection [".$db->getName()."] ...");

		if (!LResult::isOutputDisabled()) sleep(5);

		LDbUtils::deleteAllTables();

		$log_dir = LMigrationHandler::getMigrationRunningModeLogDirectory();
		$log_dir->delete(true);

		$log_dir->touch();

		LResult::messagenl("... done!");
	}

	public static function executeAllMigrations() {

		$db = db();

		LResult::messagenl("Executing all migrations on main connection [".$db->getName()."] ...");

		if (!LResult::isOutputDisabled()) sleep(5);

		$ms = new LMigrationSupport();

		//modules migrations in the future will be placed here ...

		//...

		
		$ms->loadMigrationList();
		$ms->executeAllContextMigrations(true);

		LResult::messagenl("... done!");
	}

	public static function printAllExecutedMigrations() {

		LResult::messagenl("Listing all executed migrations ...");

		$ms = new LMigrationSupport(); //default

		//modules migrations in the future will be placed here ...

		//...

		
		$ms->loadMigrationList();
		$ms->printContextExecutedMigrations(true);

		LResult::messagenl("... done!");
	}

	public static function printAllMissingMigrations() {

		LResult::messagenl("Listing all missing migrations ...");

		$ms = new LMigrationSupport(); //default

		//modules migrations in the future will be placed here ...

		//...

		
		$ms->loadMigrationList();
		$ms->printContextMissingMigrations(true);

		LResult::messagenl("... done!");
	}

	public function hasExecutedAtLeastOneContextMigration() {
		$mh = $this->lmigration_list->findLastExecutedMigration();

		if ($mh!=null) return true;
		else return false;

	}

	public function hasMoreContextMigrationsToExecute() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		$mh = $this->lmigration_list->findNextMissingMigration();

		if ($mh==null) return false;
		else return true;

	}

	public function executeNextContextMigration() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		LResult::messagenl("Executing next context ".LMigrationHelper::getCleanContextName($this->context)." migrations ...");

		$mh = $this->lmigration_list->findNextMissingMigration();

		return $mh->executeIt();

		LResult::messagenl("... done!");
	}

	public function executeAllContextMigrations($child_mode=false) {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		LResult::messagenl("Executing all context ".LMigrationHelper::getCleanContextName($this->context)." migrations ...");

		do {
			$mh = $this->lmigration_list->findNextMissingMigration();

			if ($mh) $mh->executeIt();

		} while ($mh!=null);

		if (!$child_mode) LResult::messagenl("... done!");
	}

	public function revertLastContextMigration() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		LResult::messagenl("Reverting last context ".LMigrationHelper::getCleanContextName($this->context)." migration ...");

		$mh = $this->lmigration_list->findLastExecutedMigration();

		return $mh->revertIt();

		LResult::messagenl("... done!");
	}

	public function revertAllContextMigrations($child_mode=false) {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		LResult::messagenl("Reverting all context ".LMigrationHelper::getCleanContextName($this->context)." migrations ...");

		do {
			$mh = $this->lmigration_list->findLastExecutedMigration();

			if ($mh) $mh->revertIt();

		} while($mh!=null);

		if (!$child_mode) LResult::messagenl("... done!");

	}



}