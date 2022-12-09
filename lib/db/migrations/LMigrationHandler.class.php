<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMigrationHandler {

	const MIGRATION_LOG_EXTENSION = ".log";
	const MIGRATION_EXTENSION = "migration.php";

	private $migration_file = null;
	private $context = null;
	private $require_runned = false;
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

	public function isAlreadyExecuted() {

		LMigrationHelper::ensureMigrationTableExist();

		return LMigrationHelper::isMigrationExecuted($this->getName(),$this->context);
	}

	public function getExecutionTime() {

		LMigrationHelper::ensureMigrationTableExist();
		
		return LMigrationHelper::getMigrationExecutionTime($this->getName(),$this->context);

	}

	public function isMigrationFile() {

		$extension = $this->migration_file->getFullExtension();

		if ($extension==self::MIGRATION_EXTENSION) return true;
		else return false;

	}

	public function isLoaded() {
		return class_exists($this->migration_file->getName()) && $this->require_runned;
	}

	public function load() {
		
		try {
			if ($this->isLoaded()) return true;

			$this->migration_file->requireFileOnce();

			$this->require_runned = true;

			if (class_exists($this->migration_file->getName())) 
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

		LMigrationHelper::logMigration($this->getName(),$this->context);

		LResult::messagenl("Migration ".$this->migration_file->getName()." from context ".LMigrationHelper::getCleanContextName($this->context)." executed at ".date('Y-m-d H:i:s').".");

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

		LMigrationHelper::ensureMigrationTableExist();		

		if (!LMigrationHelper::isMigrationExecuted($this->getName(),$this->context)) throw new \Exception("Migration log does not exists!");

		LResult::messagenl("Migration ".$this->getName()." from context ".LMigrationHelper::getCleanContextName($this->context)." reverted at ".date('Y-m-d H:i:s').".");

		LMigrationHelper::removeMigrationLog($this->getName(),$this->context);
	
	}

	public function __toString() {
		return "Migration '".$this->getName()."'' on context ".LMigrationHelper::getCleanContextName($this->context);
	}


}