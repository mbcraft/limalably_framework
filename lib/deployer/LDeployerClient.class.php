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

		echo "\nChanges list :\n\n";

		echo count($this->files_to_add)." files to add.\n";

		foreach ($this->files_to_add as $f) {
			echo ">> ".$f."\n";
		}
		echo "\n";
		echo count($this->files_to_update)." files to update.\n";

		foreach ($this->files_to_update as $f) {
			echo ">> ".$f."\n";
		}
		echo "\n";

		echo count($this->files_to_delete)." files to delete.\n";

		foreach ($this->files_to_delete as $f) {
			echo ">> ".$f."\n";
		}
		echo "\n\n";

	}

	private function executeChangesList() {
		$count_dir_to_add = 0;
		$count_dir_to_add_ok = 0;
		$count_files_to_add = 0;
		$count_files_to_add_ok = 0;

		foreach ($this->files_to_add as $path) {
			if (LStringUtils::endsWith($path,'/')) {
				$count_dir_to_add++;
				$r = $this->current_driver->makeDir($this->current_password,$path);
				if ($this->isSuccess($r)) $count_dir_to_add_ok++;
			} else {
				$count_files_to_add++;
				$source_file = new LFile($_SERVER['PROJECT_DIR'].$path);
				$r = $this->current_driver->copyFile($this->current_password,$path,$source_file);
				if ($this->isSuccess($r)) $count_files_to_add_ok++;
			}
		}

		$count_files_to_update = 0;
		$count_files_to_update_ok = 0;

		foreach ($this->files_to_update as $path) {
			if (LStringUtils::endsWith($path,'/')) {
				//nothing to do
			} else {
				$count_files_to_update++;
				$source_file = new LFile($_SERVER['PROJECT_DIR'].$path);
				$r = $this->current_driver->copyFile($this->current_password,$path,$source_file);
				if ($this->isSuccess($r)) $count_files_to_update_ok++;
			}
		}

		$count_dir_to_delete = 0;
		$count_dir_to_delete_ok = 0;
		$count_files_to_delete = 0;
		$count_files_to_delete_ok = 0;

		foreach ($this->files_to_delete as $path) {
			if (LStringUtils::endsWith($path,'/')) {
				$count_dir_to_delete++;
				$r = $this->current_driver->deleteDir($this->current_password,$path,true);
				if ($this->isSuccess($r)) $count_dir_to_delete_ok++;
			} else {
				$count_files_to_delete++;
				$r = $this->current_driver->deleteFile($this->current_password,$path);
				if ($this->isSuccess($r)) $count_files_to_delete_ok++;
			}
		}

		echo "\n\nOperations summary :\n\n";
		echo "Dir added : ".$count_dir_to_add_ok." of ".$count_dir_to_add." -> ".($count_dir_to_add_ok==$count_dir_to_add ? 'OK' : 'FAILURE')."\n";
		echo "Files added : ".$count_files_to_add_ok." of ".$count_files_to_add." -> ".($count_files_to_add_ok==$count_files_to_add ? 'OK' : 'FAILURE')."\n";
		echo "Files updated : ".$count_files_to_update_ok." of ".$count_files_to_update." -> ".($count_files_to_update_ok==$count_files_to_update ? 'OK' : 'FAILURE')."\n";
		echo "Dir deleted : ".$count_dir_to_delete_ok." of ".$count_dir_to_delete." -> ".($count_dir_to_delete_ok==$count_dir_to_delete ? 'OK' : 'FAILURE')."\n";
		echo "Files deleted : ".$count_files_to_delete_ok." of ".$count_files_to_delete." -> ".($count_files_to_delete_ok==$count_files_to_delete ? 'OK' : 'FAILURE')."\n";
		echo "\n";

	}

	public function visit($dir) {

		if (!in_array($dir->getPath(),$this->excluded_paths)) {
			$this->visit_result['/'.$dir->getPath()] = $dir->getContentHash();

			$files = $dir->listFiles();

			foreach ($files as $f) {
				if (!in_array($f->getPath(),$this->excluded_paths)) {
					$this->visit_result['/'.$f->getPath()] = $f->getContentHash();
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
				$my_dir = new LDir($_SERVER['PROJECT_DIR'].$dp);

				if (!$my_dir->exists()) continue;

				$my_dir->visit($this);
			}

			return $this->visit_result;
		} else {

			$root_dir = new LDir($_SERVER['PROJECT_DIR']);

			$root_dir->visit($this);
		}

		unset($this->visit_result['/']);

		var_dump($this->visit_result);

		foreach ($this->excluded_paths as $excluded) {
			foreach ($this->visit_result as $path => $hash)
				if (DStringUtils::startsWith($path,$excluded)) {
					unset($this->visit_result[$path]);
			}
		}


		
		return $this->visit_result;
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
		if (is_array($result))
			return $result['result'] == self::SUCCESS_RESULT;
		else return false;
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
				echo "Using remote deployer driver ...\n";
			} else {

				$deployer_file = new LFile($this->current_uri);
				if (!$deployer_file->exists()) throw new \Exception("Unable to locate deployer file!");

				$this->current_driver = new LLocalDeployerInstanceDriver($deployer_file);
				echo "Using local deployer driver ...\n";
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

		echo "Deployer server is unreachable at : ".$deployer_uri."\n\n";

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

		echo "\n\n";

		return false;
	}

	function help() {
		echo "Generic usage : ./bin/deployer.sh <command> <parameter1> <parameter2> <parameter...>";
		echo "\n\n";
		echo "Command List : \n\n";
		echo "./bin/deployer.sh help --> prints this help\n\n";
		echo "./bin/deployer.sh attach <deploy_key_name> <deployer.php full uri path> --> attaches the remote deployer and generates a local security token\n\n";
		echo "./bin/deployer.sh detach <deploy_key_name> --> detaches the deployer removing the server token and deleting the local key\n\n";
		echo "./bin/deployer.sh deployer_version <deploy_key_name> --> prints the deployer version\n\n";
		echo "./bin/deployer.sh deployer_update <deploy_key_name> --> updates the remote deployer using the local version\n\n";
		echo "./bin/deployer.sh framework_check <deploy_key_name> --> check and lists which framework files needs an update\n\n";
		echo "./bin/deployer.sh framework_update <deploy_key_name> --> updates the remote framework files and prints a report of the operations done\n\n";
		echo "./bin/deployer.sh project_check <deploy_key_name> --> check and lists which remote project files needs an update\n\n";
		echo "./bin/deployer.sh project_update <deploy_key_name> --> updates the remote project files and prints a report of the operations done\n\n";
		echo "./bin/deployer.sh auto_config <deploy_key_name> --> updates the remote project config files probing the right config to use if possible\n\n";
		echo "./bin/deployer.sh manual_config <deploy_key_name> <host_name> --> updates the remote project config files using the selected host name configs\n\n";
		echo "./bin/deployer.sh backup <deploy_key_name> <backup_dir_path> --> makes a full backup of a remote project and saves it in the backup dir specified\n\n";
		echo "./bin/deployer.sh disappear <deploy_key_name> --> deletes the remote deployer\n\n";
		echo "./bin/deployer.sh reset <deploy_key_name> --> deletes all the remote files but not the deployer one\n\n";
		echo "./bin/deployer.sh temp_clean <deploy_key_name> --> cleans up the remote temporary files folder\n\n";

		return true;
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

			if ($this->isSuccess($result)) echo "Password changed to secure token.\n";
			else $this->failure("Unable to change deployer secure token : ".$result['message']);

			echo "Waiting 5 seconds to let the server file cache update itself ...\n";
			sleep(5);

			$result2 = $this->current_driver->hello($this->current_password);

			if ($this->isSuccess($result2)) {
				echo "Hello with secure token successful.\n";
				return true;
			}
			else return $this->failure("Unable to correctly change password on deployer installation : ".$result2['message']);

		} else {
			echo "Unable to succesfully reach deployer instance, deleting generated key ...\n";
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

	public function deployer_version(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if ($this->isSuccess($r)) {
				
				$version = $r['version'];
				$features = $r['features'];

				echo "\n\nDeployer Version : ".$version;
				echo "\n\nDeployer Features : \n";

				foreach ($features as $f) {
					echo "- ".$f."\n";
				}
				echo "\n";

				return true;
			}
			else $this->failure("Unable to update deployer on server : ".$r['message']);

		} else return false;
	}

	public function deployer_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$uri_parts = explode('/',$this->current_uri);
			$deployer_filename = end($uri_parts);

			$updated_deployer = new LFile($_SERVER['FRAMEWORK_DIR'].'/tools/deployer.php');

			$r = $this->current_driver->changePassword($this->current_password,"");

			if (!$this->isSuccess($r)) return $this->failure("Unable to complete deployer update procedure.");

			$r1 = $this->current_driver->copyFile("",'/'.$deployer_filename,$updated_deployer);

			$r2 = $this->current_driver->changePassword("",$this->current_password);

			if (!$this->isSuccess($r1) || !$this->isSuccess($r2)) return $this->failure("Unable to complete deployer update procedure.");

			return true;

		} else return false;
	}

	public function disappear(string $key_name) {
		if ($this->loadKey($key_name)) {

			$uri_parts = explode('/',$this->current_uri);
			$deployer_filename = end($uri_parts);

			$r = $this->current_driver->deleteFile($this->current_password,'/'.$deployer_filename);

			$this->deleteKey($key_name);

			if ($this->isSuccess($r)) {
				
				echo "Deployer instance successfully disappeared.\n";

				return true;
			}
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

						if (!$this->isSuccess($r2)) return $this->failure("Unable to delete directory : ".$el." : ".$r2['message']);
					} else {
						if (!LStringUtils::endsWith($el,$deployer_filename)) {
							$r3 = $this->current_driver->deleteFile($this->current_password,$el);

							if (!$this->isSuccess($r3)) return $this->failure("Unable to delete file : ".$el." : ".$r3['message']);
						}
					}
				}
			}

			echo "Deployer instance reset executed successfully.\n";

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

				if ($this->isSuccess($r1) && $this->isSuccess($r2)) {
					
					echo "Deployer instance templ clean executed successfully.\n";

					return true;
				}
				else return $this->failure("Unable to delete and recreate temp folder on deployer installation.");
			} else return $this->failure("Unable to list files on deployer installation.");
		} else return false;
	}

	private function getFrameworkIncludeList() {
		return ["lymz_framework/"];
	}

	private function getFrameworkExcludeList() {
		return ["lymz_framework/project_image/","lymz_framework/bin/","lymz_framework/tools/","lymz_framework/tests/","lymz_framework/tests_fast/","lymz_framework/doc/","lymz_framework/init.php"];
	}

	public function framework_check(string $key_name) {
		if ($this->loadKey($key_name)) {

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$old_framework_dir = $_SERVER['FRAMEWORK_DIR'];
				$testing = true;
				$_SERVER['FRAMEWORK_DIR'] = $_SERVER['PROJECT_DIR'].'lymz_framework/';
			} else {
				$testing = false;
			}

			$r = $this->current_driver->listHashes($this->current_password,$this->getFrameworkExcludeList(),$this->getFrameworkIncludeList());

			if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

			$server_list = $r['data'];

			if (!$testing) {
				$temp_project_dir_path = $_SERVER['PROJECT_DIR'];
				$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
				$framework_parent_dir = $framework_dir->getParentDir();
				$_SERVER['PROJECT_DIR'] = $framework_parent_dir->getFullPath();
			}
			$client_list = $this->clientListHashes($this->getFrameworkExcludeList(),$this->getFrameworkIncludeList());

			$this->setupChangesList($client_list,$server_list);

			$this->previewChangesList();

			if ($testing) {
				$_SERVER['FRAMEWORK_DIR'] = $old_framework_dir;
			} else {
				$_SERVER['PROJECT_DIR'] = $temp_project_dir_path;
			}

			return true;

			
		} else return false;

	}

	public function framework_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$old_framework_dir = $_SERVER['FRAMEWORK_DIR'];
				$testing = true;
				$_SERVER['FRAMEWORK_DIR'] = $_SERVER['PROJECT_DIR'].'lymz_framework/';
			} else {
				$testing = false;
			}


			$r = $this->current_driver->listHashes($this->current_password,$this->getFrameworkExcludeList(),$this->getFrameworkIncludeList());

			if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

			$server_list = $r['data'];

			if (!$testing) {
				$temp_project_dir_path = $_SERVER['PROJECT_DIR'];
				$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
				$framework_parent_dir = $framework_dir->getParentDir();
				$_SERVER['PROJECT_DIR'] = $framework_parent_dir->getFullPath();
			}

			$client_list = $this->clientListHashes($this->getFrameworkExcludeList(),$this->getFrameworkIncludeList());

			$this->setupChangesList($client_list,$server_list);

			$this->executeChangesList();

			if ($testing) {
				$_SERVER['FRAMEWORK_DIR'] = $old_framework_dir;
			} else {
				$_SERVER['PROJECT_DIR'] = $temp_project_dir_path;
			}

			return true;

		} else return false;

	}

	private function getProjectExcludeList() {
		return ['deployer.php','config/','lymz_framework/'];
	}

	public function project_check(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->listHashes($this->current_password,$this->getProjectExcludeList(),[]);

			if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

			$server_list = $r['data'];

			$client_list = $this->clientListHashes($this->getProjectExcludeList(),[]);

			$this->setupChangesList($client_list,$server_list);

			$this->previewChangesList();

			return true;

		} else return false;
	}



	public function project_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->listHashes($this->current_password,$this->getProjectExcludeList(),[]);

			if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

			$server_list = $r['data'];

			$client_list = $this->clientListHashes($this->getProjectExcludeList(),[]);

			$this->setupChangesList($client_list,$server_list);

			$this->executeChangesList();

			return true;

			
		} else return false;
	}

	public function backup(string $key_name,string $save_dir_path) {
		if ($this->loadKey($key_name)) {

			$current_dir = new LDir('');

			$current_dir_name = $current_dir->getName();

			$fixed_key_name = str_replace('.','_',$key_name);

			$backup_filename = "backup_".$current_dir_name.'_'.$fixed_key_name."_".date('Y_m_d__h_i').".zip";

			$save_dir = new LDir($save_dir_path);

			$save_dir->touch();

			$backup_file = $save_dir->newFile($backup_filename);

			$r = $this->current_driver->downloadDir($this->current_password,'/',$backup_file);
		
			if ($this->isSuccess($r)) return true;
			else return $this->failure("Unable to backup remote installation into zip file.");
		} else return false;
	}

	private function executeConfigSync($config_folder) {
		$cf = new LDir($_SERVER['PROJECT_DIR'].'/config/hostnames/'.$config_folder);

		if (!$cf->exists()) return $this->failure("Speciefied hostname not found in config folder : ".$config_folder);

		$r0 = $this->current_driver->listElements($this->current_password,'/config/hostnames/');

		if (!$this->isSuccess($r0)) return $this->failure("Unable to list remote configs directories.");

		$cfgs = $r0['data'];

		if (isset($cfgs[$config_folder])) {
			$r1 = $this->current_driver->deleteDir($this->current_password,'/config/hostnames/'.$config_folder,true);
			if (!$this->isSuccess($r1)) return $this->failure("Unable to delete remote config directory.");
		}

		$r2 = $this->current_driver->makeDir($this->current_password,'/config/hostnames/'.$config_folder.'/');
		if (!$this->isSuccess($r2)) return $this->failure("Unable to recreate remote config folder.");

		$files = $cf->listFiles();

		$ok = true;
		foreach ($files as $f) {
			$r = $this->current_driver->copyFile($this->current_password,'/config/hostnames/'.$config_folder.'/'.$f->getFilename(),$f);
		
			$ok &= $this->isSuccess($r);
		}

		$cf_int = new LDir($_SERVER['PROJECT_DIR'].'/config/internal/');

		if ($cf_int->exists()) {

			$r = $this->current_driver->makeDir($this->current_password,'/config/internal/');
			$ok &= $this->isSuccess($r);

			$files = $cf_int->listFiles();
			foreach ($files as $f) {
				$r = $this->current_driver->copyFile($this->current_password,'/config/internal/'.$f->getFilename(),$f);
			
				$ok &= $this->isSuccess($r);
			}

		}

		$ok &= $this->isSuccess($r);

		if (!$ok) return $this->failure("Error during copy of config files to remote deployer instance.");

		return true;
	}

	public function auto_config(string $key_name) {
		if ($this->loadKey($key_name)) {

			if (LStringUtils::startsWith($this->current_uri,'http')) {
				$ok = false;
				if (LStringUtils::startsWith($this->current_uri,'http://')) {
					$ok = true;
					$uri_from_hostname = substr($this->current_uri,strlen('http://'));
				}
				if (LStringUtils::startsWith($this->current_uri,'https://')) {
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