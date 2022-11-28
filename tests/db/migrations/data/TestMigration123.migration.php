<?php



class TestMigration123 implements LIMigration {
	
	public static $execute_done = false;
	public static $revert_done = false;

	public function execute() {
		self::$execute_done = true;
	}

	public function revert() {
		self::$revert_done = true;
	}

}