<?php

class TestMigrationP1 implements LIMigration {
	
	public static $execute_done = false;
	public static $revert_done = false;

	public function execute() {
		self::$execute_done = true;
	}

	public static function executeDone() {
		return self::$execute_done;
	}

	public function revert() {
		self::$revert_done = true;
	}

	public static function revertDone() {
		return self::$revert_done;
	}

}