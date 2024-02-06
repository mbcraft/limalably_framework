<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LLocalDeployerInstanceDriver implements LIDeployerInstanceDriver {
	
	private $controller;

	function __construct($deployer_instance_file) {
		if (!$deployer_instance_file instanceof LFile) throw new \Exception("Deployer file not found!");

		echo "Loading file : ".$deployer_instance_file->getFullPath()." ...\n";

		$deployer_instance_file->requireFileOnce();

		$this->controller = new LDeployerController();
	}

	private function isSuccess($result) {
		return $result['result']==self::SUCCESS_RESULT;
	}

	private function pushFile($file) {

		if (isset($_FILES['f'])) unset($_FILES['f']);

		$_FILES['f'] = array();
		$_FILES['f']['error'] = UPLOAD_ERR_OK;
		$_FILES['f']['size'] = $file->getSize();
		$_FILES['f']['tmp_name'] = $file->getFullPath();
	}

	public function version($password) {

		return $this->controller->version($password);
		
	}

	public function listElements($password,$folder) {

		return $this->controller->listElements($password,$folder);
		
	}

	public function listHashes($password,$excluded_paths,$included_paths) {

		return $this->controller->listHashes($password,$excluded_paths,$included_paths);

	}

	public function deleteFile($password,$path) {

		return $this->controller->deleteFile($password,$path);

	}

	public function makeDir($password,$path) {

		return $this->controller->makeDir($password,$path);

	}

	public function deleteDir($password,$path,$recursive) {

		return $this->controller->deleteDir($password,$path,$recursive);

	}

	public function copyFile($password,$path,$source_file) {

		if (!$source_file instanceof LFile) throw new \Exception("source_file is actually not an LFile instance.");

		$this->pushFile($source_file);

		return $this->controller->copyFile($password,$path);

	}

	public function downloadDir($password,$path,$save_file) {

		$result = $this->controller->downloadDir($password,$path);

		if ($this->isSuccess($result)) {

			$downloaded_file_orig = $result['data'];

			$downloaded_file_ok = new LFile($downloaded_file_orig->getFullPath());

			$r = $downloaded_file_ok->move_to($save_file);
			if (!$r) return ['result' => self::FAILURE_RESULT,'message' => "Unable to copy backup file to final destination ..."];

			return ['result' => self::SUCCESS_RESULT];
		} else $this->failure("Unable to download directory from server : ".$r['message']);

	}

	public function listEnv($password) {

		return $this->controller->listEnv($password);

	}

	public function getEnv($password,$env_var_name) {

		return $this->controller->getEnv($password,$env_var_name);

	}

	public function setEnv($password,$env_var_name,$env_var_value) {

		$result = $this->controller->setEnv($password,$env_var_name,$env_var_value);

		sleep(5);

		return $result;

	}
	
	public function hello($password=null) {

		return $this->controller->hello($password);
		
	}

	public function fileExists($password,$path) {

		return $this->controller->fileExists($password,$path);

	}

	public function readFileContent($password,$path) {

		return $this->controller->readFileContent($password,$path);

	}

	public function writeFileContent($password,$path,$content) {

		return $this->controller->writeFileContent($password,$path,$content);

	}

	public function listDb($password) {

		return $this->controller->listDb($password);

	}

	public function backupDbStructure($password,$connection_name,$save_file) {

		$result = $this->controller->backupDbStructure($password,$connection_name);

		if ($this->isSuccess($result)) {

			$downloaded_file_orig = $result['data'];

			$downloaded_file_ok = new LFile($downloaded_file_orig->getFullPath());

			$r = $downloaded_file_ok->move_to($save_file);
			if (!$r) return ['result' => self::FAILURE_RESULT,'message' => "Unable to copy backup file to final destination ..."];

			return ['result' => self::SUCCESS_RESULT];
		} else $this->failure("Unable to download backup of db structure from server : ".$r['message']);

	}

	public function backupDbData($password,$connection_name,$save_file) {

		$result = $this->controller->backupDbData($password,$connection_name);

		if ($this->isSuccess($result)) {

			$downloaded_file_orig = $result['data'];

			$downloaded_file_ok = new LFile($downloaded_file_orig->getFullPath());

			$r = $downloaded_file_ok->move_to($save_file);
			if (!$r) return ['result' => self::FAILURE_RESULT,'message' => "Unable to copy backup file to final destination ..."];

			return ['result' => self::SUCCESS_RESULT];
		} else $this->failure("Unable to download backup of db data from server : ".$r['message']);

	}

	public function migrateAll($password) {

		return $this->controller->migrateAll($password);

	}

	public function migrateReset($password) {

		return $this->controller->migrateReset($password);

	}

	public function migrateListDone($password) {

		return $this->controller->migrateListDone($password);

	}

	public function migrateListMissing($password) {

		return $this->controller->migrateListMissing($password);

	}

	public function fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths) {
		return $this->controller->fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths);
	}

}