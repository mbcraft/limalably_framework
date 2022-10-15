<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LDeployerClient {
	
	const STANDARD_DEPLOYER_FILENAME = 'deployer.php';
	const DEPLOYER_KEY_EXTENSION = '.key';

	private $current_driver = null;
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

	private function loadKey(string $name) {

		$this->initDeployerClientKeysFolder();

		$deployer_key = $this->deployer_keys_folder->newFile($name.self::DEPLOYER_KEY_EXTENSION);

		if ($deployer_key->exists() && $deployer_key->isReadable()) {
			$lr = $deployer_key->openReader();

			$deployer_uri = $lr->readLine();
			$deployer_token = $lr->readLine();

			$lr->close();

			if (LStringUtils::startsWith($deployer_uri,'http')) {
				$this->current_driver = new LRemoteDeployerInstanceDriver($deployer_uri);
			} else {

				$deployer_file = new LFile($deployer_uri);
				if (!$deployer_file->exists()) throw new \Exception("Unable to locate deployer file!");

				$this->current_driver = new LLocalDeployerInstanceDriver($deployer_file);
			}

			$this->current_password = $deployer_token;
		}

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


}