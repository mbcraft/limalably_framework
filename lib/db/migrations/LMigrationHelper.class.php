<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LMigrationHelper {
	
	const MIGRATION_LOG_DIRECTORY = "config/executed_migrations/";
	
	public static function getMigrationRunningModeLogDirectory() {
		$running_mode_folder = LExecutionMode::getShort().'/';

		$base_dir = isset($_SERVER['DEPLOYER_PROJECT_DIR']) ? $_SERVER['DEPLOYER_PROJECT_DIR'] : $_SERVER['PROJECT_DIR'];

		$result = new LDir($base_dir.self::MIGRATION_LOG_DIRECTORY.$running_mode_folder);

		return $result;
	}

	public static function getMigrationLogDirectory($context) {

		$running_mode_folder = LExecutionMode::getShort().'/';

		$context_folder = LStringUtils::endsWith($context,'/') ? $context : $context.'/';

		$base_dir = isset($_SERVER['DEPLOYER_PROJECT_DIR']) ? $_SERVER['DEPLOYER_PROJECT_DIR'] : $_SERVER['PROJECT_DIR'];

		$result = new LDir($base_dir.self::MIGRATION_LOG_DIRECTORY.$running_mode_folder.$context_folder);

		$result->touch();

		return $result;
	}

	public static function getCleanContextName($context) {
		if (LStringUtils::endsWith($context,'/')) return "[".substr($context,0,-1)."]";
		else return "[".$context."]";
	}

}