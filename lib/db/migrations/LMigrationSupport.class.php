<?php


class LMigrationSupport {
	
	const PROJECT_MIGRATION_DIRECTORY = "migrations/";

	private $lmigration_list = null;
	private $context = null;

	public function loadMigrationList($folder=self::PROJECT_MIGRATION_DIRECTORY,$context='default') {

		$this->lmigration_list = new LMigrationList(new LDir($folder),$context);

		$this->context = $context;
	}

	public function printContextExecutedMigrations() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		$mh_list = $this->lmigration_list->findAllExecutedMigrations();

		echo "Executed migration list for context [".$this->context."] :\n\n";

		if (empty($mh_list)) {
			echo "No migrations found.\n\n";
		}
		else
			foreach ($mh_list as $f) {

			echo ">> ".$f->getName()." at ".$f->getExecutionTime()."\n";
		}

		echo "\n";

	}

	public function printContextMissingMigrations() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		$mh_list = $this->lmigration_list->findAllMissingMigrations();

		echo "Missing migrations for context [".$this->context."] : \n\n";

		if (empty($mh_list)) {

			echo "No migrations found.\n\n";
		}
		else
			foreach ($mh_list as $mh) {
				echo ">> ".$mh->getName()."\n";
		}

		echo "\n";
	}

	public function resetContextMigrations() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		do {
			$mh = $this->migration_list->findLastExecutedMigration();

			if ($mh) $mh->revertIt();

		} while($mh!=null);

	}

	public static function resetAllMigrations() {

		LDbUtils::deleteAllTables();

		$log_dir = LMigrationHandler::getMigrationRunningModeLogDirectory();
		$log_dir->delete(true);

		$log_dir->touch();
	}

	public static function executeAllMigrations() {

		$ms = new LMigrationSupport();

		//modules migrations in the future will be placed here ...

		//...

		
		$ms->loadMigrationList();
		$ms->executeAllContextMigrations();
	}

	public static function printAllExecutedMigrations() {

		$ms = new LMigrationSupport(); //default

		//modules migrations in the future will be placed here ...

		//...

		
		$ms->loadMigrationList();
		$ms->printContextExecutedMigrations();
	}

	public static function printAllMissingMigrations() {

		$ms = new LMigrationSupport(); //default

		//modules migrations in the future will be placed here ...

		//...

		
		$ms->loadMigrationList();
		$ms->printContextMissingMigrations();
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

		$mh = $this->lmigration_list->findNextMissingMigration();

		return $mh->executeIt();
	}

	public function executeAllContextMigrations() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		do {
			$mh = $this->lmigration_list->findNextMissingMigration();

			if ($mh) $mh->executeIt();

		} while ($mh!=null);
	}

	public function revertLastContextMigration() {

		if (!$this->lmigration_list) throw new \Exception("Migration list is not loaded!");

		$mh = $this->migration_list->findLastExecutedMigration();

		return $mh->revertIt();
	}



}