<?php


class LMigrationSupport {
	
	const PROJECT_MIGRATION_DIRECTORY = "/migrations/";

	private $lmigration_list = null;
	private $context = null;

	public function loadMigrationList($folder=self::PROJECT_MIGRATION_DIRECTORY,$context='default') {

		$this->lmigration_list = new LMigrationList(new LDir($folder),$context);

		$this->context = $context;
	}

	public function printContextExecutedMigrations() {

		$mh_list = $this->lmigration_list->findAllExecutedMigrations();

		echo "Executed migration list for context [".$this->context."] :\n\n";

		foreach ($files as $f) {

			echo ">> ".$f->getName()." at ".$f->getExecutionTime()."\n";
		}

		echo "\n";

	}

	public function printContextMissingMigrations() {
		$mh_list = $this->lmigration_list->findAllMissingMigrations();

		echo "Missing migrations for context [".$this->context."] : \n\n";

		foreach ($mh_list as $mh) {
			echo ">> ".$mh->getName()."\n";
		}

		echo "\n";
	}

	public function resetContextMigrations() {

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

		//modules migrations in the future will be placed here ...

		//...

		$default_context_ms = new LMigrationSupport(); //default

		$default_context_ms->executeAllContextMigrations();
	}

	public static function printAllExecutedMigrations() {
		//modules migrations in the future will be placed here ...

		//...

		$default_context_ms = new LMigrationSupport(); //default

		$default_context_ms->printContextExecutedMigrations();
	}

	public static function printAllMissingMigrations() {
		//modules migrations in the future will be placed here ...

		//...

		$default_context_ms = new LMigrationSupport(); //default

		$default_context_ms->printContextMissingMigrations();
	}

	public function hasExecutedAtLeastOneContextMigration() {
		$mh = $this->lmigration_list->findLastExecutedMigration();

		if ($mh!=null) return true;
		else return false;

	}

	public function hasMoreContextMigrationsToExecute() {

		$mh = $this->lmigration_list->findNextMissingMigration();

		if ($mh==null) return false;
		else return true;

	}

	public function executeNextContextMigration() {
		$mh = $this->lmigration_list->findNextMissingMigration();

		return $mh->executeIt();
	}

	public function executeAllContextMigrations() {

		do {
			$mh = $this->lmigration_list->findNextMissingMigration();

			if ($mh) $mh->executeIt();

		} while ($mh!=null);
	}

	public function revertLastContextMigration() {

		$mh = $this->migration_list->findLastExecutedMigration();

		return $mh->revertIt();
	}



}