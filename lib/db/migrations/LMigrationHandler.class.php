<?php


class LMigrationHandler {

	const MIGRATION_LOG_EXTENSION = ".log";
	const MIGRATION_EXTENSION = "migration.php";

	private $migration_file = null;
	private $context = null;
	private $load_confirmed = false;

	public function __construct($migration_file,$context) {
		$my_file = null;

		if (is_string($migration_file))
			$my_file = new LFile($migration_file);
		if ($migration_file instanceof LFile)
			$my_file = $migration_file;

		if ($my_file == null) throw new \Exception("Unable to find suitable type for migration file");

		$this->migration_file = $my_file;

		if (!is_string($context)) throw new \Exception("Context is not a valid path part as string!");

		$this->context = $context;
	}

	public function getName() {
		return $this->migration_file->getName();
	}

	public function getMigrationLogFile() {

		$migration_dir = LMigrationHelper::getMigrationLogDirectory($this->context);

		$migration_log = $migration_dir->newFile($this->getName().self::MIGRATION_LOG_EXTENSION);
		
		return $migration_log;
	}

	public function isAlreadyExecuted() {

		$migration_log = $this->getMigrationLogFile();

		return $migration_log->exists();
	}

	public function getExecutionTime() {
		$migration_log = $this->getMigrationLogFile();

		if (!$migration_log->exists()) return false;
		else return $migration_log->getContent();
	}

	public function isMigrationFile() {

		$extension = $this->migration_file->getFullExtension();

		if ($extension==self::MIGRATION_EXTENSION) return true;
		else return false;

	}

	public function isLoaded() {
		return class_exists($this->migration_file->getName()) && $this->load_confirmed;
	}

	public function load() {
		
		try {
			if ($this->isLoaded()) throw new \Exception("The migration class is already loaded!!");

			$this->migration_file->requireFileOnce();

			if ($this->isLoaded()) 
				{
					$this->load_confirmed = true;
					return true;
				}
			else return false;
		}
		catch (\Exception $ex) {
			return false;
		}
	}

	public function executeIt() {
		
		$result = $this->load();

		if (!$result) throw new \Exception("Class name and file name of migration mismatches! Check ".$this->migration_file->getFullPath());

		$class_name = $this->migration_file->getName();

		try {
			$instance = new $class_name();
		} catch (\Exception $ex) {
			throw new \Exception("Unable to make instance of migration ".$class_name." : ".$ex->getMessage());
		}

		try {
			$instance->execute();
			$this->logExecuted();
		} catch (\Exception $ex) {
			throw new \Exception("Unable to execute migration ".$class_name." : ".$ex->getMessage());
		}

		return true;
	}

	private function logExecuted() {

		$migration_log_dir = LMigrationHelper::getMigrationLogDirectory($this->context);

		$migration_log_file = $migration_log_dir->newFile($this->migration_file->getName().self::MIGRATION_LOG_EXTENSION);
		$migration_log_file->setContent(date('Y-m-d H:i:s'));

	}

	public function revertIt() {
		
		$result = $this->load();

		if (!$result) throw new \Exception("Class name and file name of migration mismatches! Check ".$this->migration_file->getFullPath());

		$class_name = $this->migration_file->getName();

		try {
			$instance = new $class_name();
		} catch (\Exception $ex) {
			throw new \Exception("Unable to make instance of migration ".$class_name." : ".$ex->getMessage());
		}

		try {
			$instance->revert();
			$this->removeLogExecuted();
		} catch (\Exception $ex) {
			throw new \Exception("Unable to revert migration ".$class_name." : ".$ex->getMessage());
		}

		return true;
	}

	private function removeLogExecuted() {
		$migration_log_dir = LMigrationHelper::getMigrationLogDirectory($this->context);

		if (!$migration_log_dir->exists()) throw new \Exception("Migration log dir does not exists!");

		$migration_log_file = $migration_log_dir->newFile($this->migration_file->getName().self::MIGRATION_LOG_EXTENSION);

		if (!$migration_log_file->exists()) throw new \Exception("Migration log file does not exists!");
		
		return $migration_log_file->delete();
	}
}