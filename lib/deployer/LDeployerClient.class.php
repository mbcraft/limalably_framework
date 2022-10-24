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

	private $visit_result = [];

	private $excluded_paths = [];
	private $included_paths = [];

	private $files_to_add = [];
	private $files_to_update = [];
	private $files_to_delete = [];

	private function setupChangesList($client_hash_list,$server_hash_list) {

		$this->files_to_add = [];
		$this->files_to_update = [];
		$this->files_to_delete = [];

		foreach ($client_hash_list as $path => $hash) {
			if (!isset($server_hash_list[$path])) {
				$this->files_to_add[] = $path;
			}
			if (isset($server_hash_list[$path]) && $server_hash_list[$path]!=$hash) {
				$this->files_to_update[] = $path;
			}
		}

		foreach ($server_hash_list as $path => $hash) {
			if (!isset($client_hash_list[$path])) {
				$this->files_to_delete[] = $path;
			}
		}

	}

	private function previewChangesList() {

		echo "Changes list :\n\n";

		echo count($this->files_to_add)." files to add.\n";
		echo count($this->files_to_update)." files to update.\n";
		echo count($this->files_to_delete)." files to delete.\n";

	}

	private function executeChangesList() {

		foreach ($this->files_to_add as $path => $hash) {
			if (LStringUtils::endsWith($path,'/')) {
				$this->current_driver->makeDir($this->current_password,$path);
			} else {
				$source_file = new LFile($path);
				$this->current_driver->copyFile($this->current_password,$path,$source_file);
			}
		}

		foreach ($this->files_to_update as $path => $hash) {
			if (LStringUtils::endsWith($path,'/')) {
				//nothing to do
			} else {
				$source_file = new LFile($path);
				$this->current_driver->copyFile($this->current_password,$path,$source_file);
			}
		}

		foreach ($this->files_to_delete as $path => $hash) {
			if (LStringUtils::endsWith($path,'/')) {
				$this->current_driver->deleteDir($this->current_password,$path,true);
			} else {
				$this->current_driver->deleteFile($this->current_password,$path);
			}
		}

	}

	public function visit($dir) {

		if (!in_array($dir->getPath(),$this->excluded_paths)) {
			$this->visit_result[$dir->getPath()] = $dir->getContentHash();

			$files = $dir->listFiles();

			foreach ($files as $f) {
				if (!in_array($f->getPath(),$this->excluded_paths)) {
					$this->visit_result[$f->getPath()] = $f->getContentHash();
				}
			}
		}

	}

	private function clientListHashes($excluded_paths,$included_paths) {
			
			$this->visit_result = [];

			$this->excluded_paths = $excluded_paths;
			$this->included_paths = $included_paths;

			if (count($this->included_paths)>0) {
				foreach ($this->included_paths as $dp) {
					$my_dir = new LDir($dp);

					$my_dir->visit($this);
				}

				return $this->visit_result;
			} else {
				return [];
			}
		
	}

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

	private function loadKey(string $name,bool $use_password=true) {

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
				//echo "Using hello with password ...\n";
				$hello_result = $this->current_driver->hello($this->current_password);
			} else {
				//echo "Using hello without password ...\n";
				$hello_result = $this->current_driver->hello();
			}

			if ($this->isSuccess($hello_result)) return true;
			else {
				return $this->unreachableDeployerServer($this->current_uri);
			}
		}

		return $this->loadKeyError($name);

	}

	private function unreachableDeployerServer(string $deployer_uri) {

		echo "Deployer server is unreachable at : ".$deployer_uri;

		return false;
	}

	private function loadKeyError(string $name) {

		echo "Unable to load key with name : ".$name;

		return false;
	}

	private function deleteKey(string $name) {
		$this->initDeployerClientKeysFolder();

		$deployer_key_file = $this->deployer_keys_folder->newFile($name.self::DEPLOYER_KEY_EXTENSION);

		$deployer_key_file->delete();
	}

	private function saveKey(string $name,string $deployer_uri) {

		$this->initDeployerClientKeysFolder();

		if (LStringUtils::startsWith($deployer_uri,'http')) {
			if (LStringUtils::endsWith($deployer_uri,'/')) {
			$deployer_uri .= self::STANDARD_DEPLOYER_FILENAME;
			}
		} else {
			if (LFileSystemUtils::isFile($deployer_uri));
			else {
				$dir = new LDir($deployer_uri);
				$df = $dir->newFile(self::STANDARD_DEPLOYER_FILENAME);

				if ($df->exists()) $deployer_uri = $df->getFullPath();
				else throw new \Exception("Unable to locate deployer file path on local file system.");
			}
		}		

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

	function help() {

	}

	public function hello(string $key_name) {
		if ($this->loadKey($key_name)) {

			$result = $this->current_driver->hello($this->current_password);

			if ($this->isSuccess($result)) return true;
			else return $this->failure("Unable to verify token with deployer instance.");
		} else return $this->failure("Unable to load key ".$key_name);
	}

	public function attach(string $key_name,string $deployer_uri) {

		$random_token = $this->saveKey($key_name,$deployer_uri);
		if ($this->loadKey($key_name,false)) {

			$this->current_password = $random_token;

			$result = $this->current_driver->changePassword("",$this->current_password);

			$result2 = $this->current_driver->hello($this->current_password);

			if ($this->isSuccess($result) && $this->isSuccess($result2)) {
				return true;
			}
			else return $this->failure("Unable to correctly change password on deployer installation.");

		} else {
			$this->deleteKey($key_name);
			return $this->failure("Unable to find saved key ".$key_name);
		}
	}

	public function detach(string $key_name) {

		if ($this->loadKey($key_name)) {

			$result = $this->current_driver->changePassword($this->current_password,"");

			$this->deleteKey($key_name);

			if ($this->isSuccess($result)) return true;
				else return $this->failure("Unable to change password on deployer instance.");

		} else return $this->failure("Unable to find key to load for detach : ".$key_name);
	}

	public function deployer_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$uri_parts = explode('/',$this->current_uri);
			$deployer_filename = end($uri_parts);

			$updated_deployer = new LFile($_SERVER['FRAMEWORK_DIR'].'/tools/deployer.php');

			$r = $this->current_driver->copyFile($this->current_password,'/'.$deployer_filename,$updated_deployer);

			if ($this->isSuccess($r)) return true;
			else $this->failure("Unable to update deployer on server.");

		} else return false;
	}

	public function framework_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$dir_name = $framework_dir->getName();

				$r = $this->current_driver->listHashes($this->current_password,[],[$dir_name.'/']);

				if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

				$this->clientListHashes([],[$dir_name.'/']);

				$this->setupChangesList();

				$this->executeChangesList();

				return true;

			} else return $this->failure("Unable to determine framework dir.");
		} else return false;

	}

	public function project_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$dir_name = $framework_dir->getName();

				$r = $this->current_driver->listHashes($this->current_password,[$dir_name.'/'],[]);

				if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

				$this->clientListHashes([$dir_name.'/'],[]);

				$this->setupChangesList();

				$this->executeChangesList();

				return true;

			} else return $this->failure("Unable to determine framework dir.");
		} else return false;
	}

	public function disappear(string $key_name) {
		if ($this->loadKey($key_name)) {

			$uri_parts = explode('/',$this->current_uri);
			$deployer_filename = end($uri_parts);

			$r = $this->current_driver->deleteFile($this->current_password,'/'.$deployer_filename);

			if ($this->isSuccess($r)) return true;
			else return $this->failure("Unable to make deployer installation disappear.");
		} else return false;	
	}

	public function reset(string $key_name) {
		if ($this->loadKey($key_name)) {

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

		} else return false;
	}

	public function temp_clean(string $key_name) {
		if ($this->loadKey($key_name)) {

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
			} else return $this->failure("Unable to list files on deployer installation.");
		} else return false;
	}

	public function framework_check(string $key_name) {
		if ($this->loadKey($key_name)) {

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$dir_name = $framework_dir->getName();

				$r = $this->current_driver->listHashes($this->current_password,[],[$dir_name.'/']);

				if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

				$this->clientListHashes([],[$dir_name.'/']);

				$this->setupChangesList();

				$this->previewChangesList();

				return true;

			} else return $this->failure("Unable to determine framework dir.");
		} else return false;

	}

	public function project_check(string $key_name) {
		if ($this->loadKey($key_name)) {

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$dir_name = $framework_dir->getName();

				$r = $this->current_driver->listHashes($this->current_password,[$dir_name.'/'],[]);

				if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

				$this->clientListHashes([$dir_name.'/'],[]);

				$this->setupChangesList();

				$this->previewChangesList();

				return true;

			} else return $this->failure("Unable to determine framework dir.");
		} else return false;
	}

	public function backup(string $key_name) {
		if ($this->loadKey($key_name)) {

			$backup_filename = "backup_".$key_name."_".date('Y_M_D_h_i').".zip";

			$backup_file = new LFile('/'.$backup_filename);

			$r = $this->current_driver->downloadDir($this->current_password,'/',$backup_file);
		
			if ($this->isSuccess($r)) return true;
			else return $this->failure("Unable to backup remote installation into zip file.");
		} else return false;
	}

	function executeConfigSync($config_folder) {
		$cf = new LDir('/config/hostnames/'.$config_folder);

		if (!$cf->exists()) return $this->failure("Speciefied hostname not found in config folder : ".$config_folder);

		$r1 = $this->current_driver->deleteDir($this->current_password,'/config/hostnames/'.$config_folder,true);
		if (!$this->isSuccess($r1)) return $this->failure("Unable to delete remote config directory.");
		$r2 = $this->current_driver->makeDir($this->current_password,'/config/hostnames/'.$config_folder);
		if (!$this->isSuccess($r2)) return $this->failure("Unable to recreate remote config folder.");

		$files = $cf->listFiles();

		$ok = true;
		foreach ($files as $f) {
			$r = $this->current_driver->copyFile($this->current_password,'/config/hostnames/'.$config_folder.'/'.$f->getName(),$f);
		
			$ok &= $this->isSuccess($r);
		}

		if (!$ok) return $this->failure("Error during copy of config files to remote deployer instance.");

		return true;
	}

	public function auto_config(string $key_name) {
		if ($this->loadKey($key_name)) {

			if (LStringUtils::beginWith($this->current_uri,'http')) {
				$ok = false;
				if (LStringUtils::beginWith($this->current_uri,'http://')) {
					$ok = true;
					$uri_from_hostname = substr($this->current_uri,strlen('http://'));
				}
				if (LStringUtils::beginWith($this->current_uri,'https://')) {
					$ok = true;
					$uri_from_hostname = substr($this->current_uri,strlen('https://'));
				}
				if (!$ok) return $this->failure("Unable to probe correct hostname from deployer key config.");
				$parts = explode('/',$uri_from_hostname);
				$hostname = $parts[0];

				$this->executeConfigSync($hostname);
			} else return $this->failure("Unable to probe the host name from deployer configuration. Use manual_config.");

		} else return false;
	}

	public function manual_config(string $key_name,string $config_folder) {
		if ($this->loadKey($key_name)) {
			return $this->executeConfigSync($config_folder);
		} else return false;
	}

}