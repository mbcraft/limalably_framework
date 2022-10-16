<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LDeployerClient {
	
	const SUCCESS_RESULT = ':)';
	const FAILURE_RESULT = ':(';

	const STANDARD_DEPLOYER_FILENAME = 'deployer.php';
	const DEPLOYER_KEY_EXTENSION = '.key';

	private $current_driver = null;
	private $current_uri = null;
	private $current_password = "";

	private $deployer_keys_folder = null;

	public function setDeployerClientKeysFolder($dir_or_path) {
		if (is_string($dir_or_path)) {
			$dir_or_path = new LDir();
		}

		if ($dir_or_path==null) throw new \Exception("dir_or_path is null!");

		if (LFileSystemUtils::isDir($dir_or_path->getFullPath()) && $dir_or_path->exists() && $dir_or_path->isReadable()) {
			$this->deployer_keys_folder = $dir_or_path;
		} else throw new \Exception("dir_or_path is not a valid key folder!");
	}

	private function initDeployerClientKeysFolder() {
		if ($this->deployer_keys_folder) return;

		if (!isset($_SERVER['PROJECT_DIR'])) $this->deployer_keys_folder = new LDir($_SERVER['FRAMEWORK_DIR'].'tests/deployer/tmp/');
		else $this->deployer_keys_folder = new LDir($_SERVER['PROJECT_DIR'].'config/deployer/');

		$this->deployer_keys_folder->touch();
	}

	private function isSuccess($result) {
		return $result['result'] == self::SUCCESS_RESULT;
	}

	private function loadKey(string $name,bool $use_password) {

		$this->initDeployerClientKeysFolder();

		$deployer_key = $this->deployer_keys_folder->newFile($name.self::DEPLOYER_KEY_EXTENSION);

		if ($deployer_key->exists() && $deployer_key->isReadable()) {
			$lr = $deployer_key->openReader();

			$this->current_uri = $lr->readLine();
			$this->current_password = $lr->readLine();

			$lr->close();

			if (LStringUtils::startsWith($this->current_uri,'http')) {
				$this->current_driver = new LRemoteDeployerInstanceDriver($this->current_uri);
			} else {

				$deployer_file = new LFile($this->current_uri);
				if (!$deployer_file->exists()) throw new \Exception("Unable to locate deployer file!");

				$this->current_driver = new LLocalDeployerInstanceDriver($deployer_file);
			}

			if ($use_password) {
				$hello_result = $this->current_driver->hello($this->current_password);
			} else {
				$hello_result = $this->current_driver->hello("");
			}

			if ($this->isSuccess($hello_result)) return true;
			else return $this->unreachableDeployerServer($deployer_uri);
		}

		return $this->loadKeyError($name);

	}

	private function unreachableDeployerServer(string $deployer_uri) {

		return false;
	}

	private function loadKeyError(string $name) {

		return false;
	}

	private function deleteKey(string $name) {
		$this->initDeployerClientKeysFolder();

		$deployer_key_file = $this->deployer_keys_folder->newFile($name.self::DEPLOYER_KEY_EXTENSION);

		$deployer_key_file->delete();
	}

	private function saveKey(string $name,string $deployer_uri) {

		if (LStringUtils::startsWith($deployer_uri,'http')) {
			if (LStringUtils::endsWith($deployer_uri,'/')) {
			$deployer_uri .= self::STANDARD_DEPLOYER_FILENAME;
		} else {
			if (LFileSystemUtils::isFile($deployer_uri));
			else {
				$dir = new LDir($deployer_uri);
				$df = $dir->newFile(self::STANDARD_DEPLOYER_FILENAME);

				if ($df->exists()) $deployer_uri = $df->getFullPath();
				else throw new \Exception("Unable to locate deployer file path on local file system.");
			}
		}		


		$this->initDeployerClientKeysFolder();

		$deployer_key_file = $this->deployer_keys_folder->newFile($name.self::DEPLOYER_KEY_EXTENSION);

		$lw = $deployer_key_file->openWriter();

		$lw->writeln($deployer_uri);

		LRandomUtils::seed();

		$random_token = LRandomUtils::letterCode(36);

		$lw->writeln($random_token);

		$lw->close();

		return $random_token;
	}

	private function failure(string $msg) {

		echo $msg;

		return false;
	}

	public function help() {

	}

	public function attach(string $key_name,string $deployer_uri) {

		$this->saveKey($key_name,$deployer_uri);
		if ($this->loadKey($key_name,false)) {

			$result = $this->current_driver->changePassword("",$this->current_password);

			if ($this->isSuccess($result)) return true;
			else return $this->failure("Unable to correctly change password on deployer installation.");

		} else {
			$this->deleteKey($key_name);
			return $this->failure("Unable to find saved key ".$key_name);
		}
	}

	public function detach(string $key_name) {

		if (!$this->loadKey($key_name)) return $this->failure("Unable to load key ".$key_name);

		$result = $this->current_driver->changePassword($this->current_password,"");

		if ($this->isSuccess($result)) return true;
			else return $this->failure("Unable to change password on deployer instance.");
	}

	public function deployer_update(string $key_name) {
		if (!$this->loadKey($key_name)) return $this->failure("Unable to load key ".$key_name);
	}

	public function framework_update(string $key_name) {

	}

	public function project_update(string $key_name) {

	}

	public function disappear(string $key_name) {
		if (!$this->loadKey($key_name)) return $this->failure("Unable to load key ".$key_name);

		$uri_parts = explode('/',$this->current_uri);
		$deployer_filename = end($uri_parts);

		$r = $this->current_driver->deleteFile($this->current_password,'/'.$deployer_filename);

		if ($this->isSuccess($r)) return true;
		else return $this->failure("Unable to make deployer installation disappear.");
	}

	public function reset(string $key_name) {
		if (!$this->loadKey($key_name)) return $this->failure("Unable to load key ".$key_name);

		$uri_parts = explode('/',$this->current_uri);
		$deployer_filename = end($uri_parts);

		$r = $this->current_driver->listElements($this->current_password,'/');

		if ($this->isSuccess($r)) {
			$elements = $r['data'];

			foreach ($elements as $el) {
				if (LStringUtils::endsWith($el,'/')) {
					$r2 = $this->current_driver->deleteDir($this->current_password,$el,true);

					if (!$this->isSuccess($r2)) return $this->failure("Unable to delete directory : ".$el);
				} else {
					if (!LStringUtils::endsWith($el,$deployer_filename)) {
						$r3 = $this->current_driver->deleteFile($this->current_password,$el);

						if (!$this->isSuccess($r2)) return $this->failure("Unable to delete file : ".$el);
					}
				}
			}
		}

		return true;
	}

	public function temp_clean(string $key_name) {
		if (!$this->loadKey($key_name)) return $this->failure("Unable to load key ".$key_name);

		$result = $this->current_driver->listElements($this->current_password,'/');

		if ($this->isSuccess($result)) {
			$elements = $result['data'];

			$temp_found = false;
			foreach ($elements as $el) {
				if ($el=='temp/') $temp_found = true;
			}

			if (!$temp_found) return $this->failure("Unable to find temp folder to clean.");

			$r1 = $this->current_driver->deleteDir($this->current_password,'temp/',true);
			$r2 = $this->current_driver->makeDir($this->current_password,'temp/');

			if ($this->isSuccess($r1) && $this->isSuccess($r2)) return true;
			else return $this->failure("Unable to delete and recreate temp folder on deployer installation.");
		} else $this->failure("Unable to list files on deployer installation.");
	}

	public function framework_check(string $key_name) {

	}

	public function project_check(string $key_name) {

	}

	public function backup(string $key_name) {

	}

	public function auto_config(string $key_name) {

	}

	public function manual_config(string $key_name,string $config_folder) {

	}

}