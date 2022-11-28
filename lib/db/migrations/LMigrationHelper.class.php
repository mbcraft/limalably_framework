<?php

class LMigrationHelper {
	
	const MIGRATION_LOG_DIRECTORY = "config/migrations/";
	
	public static function getMigrationRunningModeLogDirectory() {
		$running_mode_folder = LExecutionMode::getShort().'/';

		$result = new LDir($_SERVER['PROJECT_DIR'].self::MIGRATION_LOG_DIRECTORY.$running_mode_folder);

		return $result;
	}

	public static function getMigrationLogDirectory($context) {

		$running_mode_folder = LExecutionMode::getShort().'/';

		$context_folder = LStringUtils::endsWith($context,'/') ? $context : $context.'/';

		$result = new LDir($_SERVER['PROJECT_DIR'].self::MIGRATION_LOG_DIRECTORY.$running_mode_folder.$context_folder);

		$result->touch();

		return $result;
	}

}