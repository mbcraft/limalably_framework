<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LLocalDeployerInstanceDriver implements LIDeployerInstanceDriver {
	
	private $controller;

	const SUCCESS_RESULT = ':)';
	const FAILURE_RESULT = ':(';

	function __construct($deployer_instance_file) {
		if (!$deployer_instance_file instanceof LFile) throw new \Exception("Deployer file not found!");

		$deployer_instance_file->requireFileOnce();

		$this->controller = new DeployerController();
	}

	private function isSuccess($result) {
		return $result['result']==self::SUCCESS_RESULT;
	}

	private function pushFile($file) {

		if (!$file instanceof LFile) throw new \Exception("Parameter is not actually an LFile instance");

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

	public function changePassword($old_password,$new_password) {

		return $this->controller->changePassword($old_password,$new_password);

	}
	
	public function hello($password=null) {

		return $this->controller->hello($password);
		
	}

}