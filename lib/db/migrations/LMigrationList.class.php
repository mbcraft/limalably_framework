<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMigrationList {
	
	private $migration_list = [];
	private $context = null;

	public function __construct($dir,$context='default') {

		if (!is_string($context)) throw new \Exception("Context for migrations is not valid!");

		if (!LStringUtils::endsWith($context,'/')) $context.='/';

		$this->context = $context;

		$my_dir = null;

		if (is_string($dir)) {
			$my_dir = new LDir($dir);
		}
		if ($dir instanceof LDir) {
			$my_dir = $dir;
		}

		if ($my_dir==null) throw new \Exception("Unable to recognize dir as a folder!");

		if (!$my_dir->exists()) return;

		$file_list = $my_dir->listFiles();

		foreach ($file_list as $f) {

			$mh = new LMigrationHandler($f,$this->context);

			if ($mh->isMigrationFile()) {
				$this->migration_list[$f->getName()] = $f->getFullPath();
			}

		}

		ksort($this->migration_list);
	}

	public function findNextMissingMigration() {

		foreach ($this->migration_list as $name => $path) {
			$mh = new LMigrationHandler($path,$this->context);

			if ($mh->isMigrationFile() && !$mh->isAlreadyExecuted()) return $mh;
		}

		return null;

	}

	public function findAllExecutedMigrations() {
		$result_list = [];

		foreach ($this->migration_list as $name => $path) {
			$mh = new LMigrationHandler($path,$this->context);

			if ($mh->isMigrationFile() && $mh->isAlreadyExecuted()) $result_list[] = $mh;
		}

		return $result_list;
	}

	public function findLastExecutedMigration() {

		$result = null;

		foreach ($this->migration_list as $name => $path) {
			$mh = new LMigrationHandler($path,$this->context);

			if ($mh->isMigrationFile() && $mh->isAlreadyExecuted()) $result = $mh;
		}

		return $result;

	}

	public function findAllMissingMigrations() {
		$result_list = [];

		foreach ($this->migration_list as $name => $path) {
			$mh = new LMigrationHandler($path,$this->context);

			if ($mh->isMigrationFile() && !$mh->isAlreadyExecuted()) $result_list[] = $mh;
		}

		return $result_list;
	}
	

}