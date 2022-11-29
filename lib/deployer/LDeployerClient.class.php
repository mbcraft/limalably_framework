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

	const DEPLOYER_PATH_FROM_ROOT = 'DPFR';

	const EXEC_MODE_MAP = [
			LExecutionMode::MODE_MAINTENANCE_SHORT => LExecutionMode::MODE_MAINTENANCE,
			LExecutionMode::MODE_FRAMEWORK_DEVELOPMENT_SHORT => LExecutionMode::MODE_FRAMEWORK_DEVELOPMENT,
			LExecutionMode::MODE_DEVELOPMENT_SHORT => LExecutionMode::MODE_DEVELOPMENT,
			LExecutionMode::MODE_TESTING_SHORT => LExecutionMode::MODE_TESTING,
			LExecutionMode::MODE_PRODUCTION_SHORT => LExecutionMode::MODE_PRODUCTION
		];

	private $DPFR;

	private $current_driver = null;
	private $current_uri = null;
	private $current_password = "";

	private $deployer_keys_folder = null;

	private $explore_result = [];

	private $excluded_paths = [];
	private $included_paths = [];

	private $dirs_to_add = [];
	private $files_to_add = [];
	private $dirs_to_update = [];
	private $files_to_update = [];
	private $dirs_to_delete = [];
	private $files_to_delete = [];

	private $root_dir = null;

	function __construct() {
		if (isset($_SERVER['PROJECT_DIR']))
			$this->root_dir = new LDir($_SERVER['PROJECT_DIR']);
		else if (isset($_SERVER['FRAMEWORK_DIR']))
			$this->root_dir = new LDir($_SERVER['FRAMEWORK_DIR']);

		if ($this->root_dir==null) throw new \Exception("Unable to set root dir correctly in deployer client!");
	}

	private function setupChangesList($client_hash_list,$server_hash_list) {

		$this->dirs_to_add = [];
		$this->files_to_add = [];
		$this->dirs_to_update = [];
		$this->files_to_update = [];
		$this->dirs_to_delete = [];
		$this->files_to_delete = [];

		foreach ($client_hash_list as $path => $hash) {
			if (!isset($server_hash_list[$path])) {
				if (LStringUtils::endsWith($path,'/'))
					$this->dirs_to_add []= $path;
				else
					$this->files_to_add []= $path;
			}
			if (isset($server_hash_list[$path]) && $server_hash_list[$path]!=$hash) {
				if (LStringUtils::endsWith($path,'/'))
					$this->dirs_to_update []= $path;
				else
					$this->files_to_update []= $path;
			}
		}

		foreach ($server_hash_list as $path => $hash) {
			if (!isset($client_hash_list[$path])) {
				if (LStringUtils::endsWith($path,'/'))
					$this->dirs_to_delete []= $path;
				else
					$this->files_to_delete []= $path;
			}
		}

	}

	private function changesListSummary(bool $wait) {

		echo "\nChanges list summary :\n\n";

		echo count($this->dirs_to_add)." dir to add.\n";
		echo "\n";
		echo count($this->files_to_add)." files to add.\n";
		echo "\n";
		echo count($this->dirs_to_update)." dir to update.\n";
		echo "\n";
		echo count($this->files_to_update)." files to update.\n";
		echo "\n";
		echo count($this->dirs_to_delete)." dir to delete.\n";
		echo "\n";
		echo count($this->files_to_delete)." files to delete.\n";
		echo "\n\n";

		if ($wait) {
			echo "Waiting 5 seconds before actual execution ...\n";
			sleep(5);
		}
	}

	private function previewChangesList() {

		echo "\nChanges list :\n\n";

		echo count($this->dirs_to_add)." dir to add.\n";

		foreach ($this->dirs_to_add as $k => $d) {
			echo ">> ".$d."\n";
		}
		echo "\n";
		echo count($this->files_to_add)." files to add.\n";

		foreach ($this->files_to_add as $k => $f) {
			echo ">> ".$f."\n";
		}
		echo "\n";
		echo count($this->dirs_to_update)." dir to update.\n";

		foreach ($this->dirs_to_update as $k => $d) {
			echo ">> ".$d."\n";
		}
		echo "\n";
		echo count($this->files_to_update)." files to update.\n";

		foreach ($this->files_to_update as $k => $f) {
			echo ">> ".$f."\n";
		}
		echo "\n";
		echo count($this->dirs_to_delete)." dir to delete.\n";

		foreach ($this->dirs_to_delete as $k => $d) {
			echo ">> ".$d."\n";
		}
		echo "\n";
		echo count($this->files_to_delete)." files to delete.\n";

		foreach ($this->files_to_delete as $k => $f) {
			echo ">> ".$f."\n";
		}
		echo "\n\n";

	}

	private function getResultMessage($result) {
		if ($result && is_array($result) && isset($result['message']))
			return $result['message'];
		else {
			if ($result===null)
				return "Hard server failure!!! Probably the server host firewall or setup needs to be fixed or you need to place a .htaccess file with 'require all granted' inside server!!!";
			else 
				return $result;
		}
	}

	private function executeChangesList() {
		$count_dirs_to_add = 0;
		$count_dirs_to_add_ok = 0;
		$count_files_to_add = 0;
		$count_files_to_add_ok = 0;

		foreach ($this->dirs_to_add as $path) {
			$count_dirs_to_add++;
			$r = $this->current_driver->makeDir($this->current_password,$path);
			if ($this->isSuccess($r)) 
				{
					echo "(a)";
					$count_dirs_to_add_ok++;
				}
			else echo "\nUnable to make dir : ".$path."\n";
		}
		foreach ($this->files_to_add as $path) {
			$count_files_to_add++;
			$source_file = new LFile($_SERVER['PROJECT_DIR'].$path);
			$r = $this->current_driver->copyFile($this->current_password,$path,$source_file);
			if ($this->isSuccess($r)) 
				{
					echo "(a)";
					$count_files_to_add_ok++;
				}
			else echo "\nUnable to copy file : '".$path." - ".$this->getResultMessage($r)."'\n";
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
				if ($this->isSuccess($r)) 
					{
						echo "(u)";
						$count_files_to_update_ok++;
					}
				else echo "\nUnable to copy file : '".$path."'\n";
			}
		}

		$count_dirs_to_delete = 0;
		$count_dirs_to_delete_ok = 0;
		$count_files_to_delete = 0;
		$count_files_to_delete_ok = 0;

		foreach ($this->dirs_to_delete as $path) {
			$count_dirs_to_delete++;
			$r = $this->current_driver->deleteDir($this->current_password,$path,true);
			if ($this->isSuccess($r)) {
				echo "(d)";
				$count_dirs_to_delete_ok++;
			}
			else echo "\nUnable to delete dir : '".$path."'\n";
		} 
		foreach ($this->files_to_delete as $path) {
			$count_files_to_delete++;
			$r = $this->current_driver->deleteFile($this->current_password,$path);
			if ($this->isSuccess($r)) 
				{
					echo "(d)";
					$count_files_to_delete_ok++;
				}
			else echo "\nUnable to delete file : '".$path."'\n";
		}
		

		echo "\n\nOperations summary :\n\n";
		echo "Dir added : ".$count_dirs_to_add_ok." of ".$count_dirs_to_add." -> ".($count_dirs_to_add_ok==$count_dirs_to_add ? 'OK' : 'FAILURE')."\n";
		echo "Files added : ".$count_files_to_add_ok." of ".$count_files_to_add." -> ".($count_files_to_add_ok==$count_files_to_add ? 'OK' : 'FAILURE')."\n";
		echo "Files updated : ".$count_files_to_update_ok." of ".$count_files_to_update." -> ".($count_files_to_update_ok==$count_files_to_update ? 'OK' : 'FAILURE')."\n";
		echo "Dir deleted : ".$count_dirs_to_delete_ok." of ".$count_dirs_to_delete." -> ".($count_dirs_to_delete_ok==$count_dirs_to_delete ? 'OK' : 'FAILURE')."\n";
		echo "Files deleted : ".$count_files_to_delete_ok." of ".$count_files_to_delete." -> ".($count_files_to_delete_ok==$count_files_to_delete ? 'OK' : 'FAILURE')."\n";
		echo "\n";

	}

	public function visit($dir) {

        $result = [];

		if ($dir->exists() && !in_array($dir->getPath(),$this->excluded_paths)) {
			$result[$dir->getPath()] = $dir->getContentHash();

			$files = $dir->listFiles();

			foreach ($files as $f) {
				if (!in_array($f->getPath(),$this->excluded_paths)) {
					$result[$f->getPath()] = $f->getContentHash();
				}
			}
		}

        return $result;

	}

    private function getFinalPathList($path_list) {

        $result = [];

        foreach ($path_list as $p) {
            if ($p==='@') $result[] = $this->DPFR;
                else $result[] = $p;
        }

        return $result;

    }

    private function containsDeployerPath($path_list) {
        return in_array('@',$path_list);
    }

	private function clientListHashes($excluded_paths,$included_paths) {
			
		$this->excluded_paths = $this->getFinalPathList($excluded_paths);
		$this->included_paths = $this->getFinalPathList($included_paths);

		$pre_include_result = [];

		if (count($this->included_paths)>0) {
			foreach ($this->included_paths as $path) {
				$my_dir = new LDir($this->root_dir->getFullPath().$path);

				$pre_include_result = array_merge($my_dir->explore($this),$pre_include_result);
			}
		} else {
			$pre_include_result = $this->root_dir->explore($this);
		}

        $pre_result = array_remove_key_or_value($pre_include_result,'');

        $final_result = [];

        if (empty($this->excluded_paths))
            $final_result = $pre_result;
        else {
	        foreach ($pre_result as $path => $hash) {
	            $skip = false;
	            foreach ($this->excluded_paths as $excluded) {
	                if (LStringUtils::startsWith($path,$excluded)) 
	                    $skip = true;
	                if (LStringUtils::startsWith($path,'config/deployer/'))
	                    $skip = true;
	            }

	            if (!$skip) $final_result [$path] = $hash;
	        }
		}
        $final_result_2 = array_remove_key_or_value($final_result,'');

        return $final_result_2;
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

			$this->DPFR = $lr->readLine();
			$this->current_uri = $lr->readLine();
			$this->current_password = $lr->readLine();

			$lr->close();

			echo "Local deployer path is project : ".$this->DPFR."\n";
			echo "Deployer uri found in key : ".$this->current_uri."\n";
			echo "Waiting 3 seconds to let you block if the uri is wrong ...\n";
			sleep(3);

			if (LStringUtils::startsWith($this->current_uri,'http')) {
				$this->current_driver = new LRemoteDeployerInstanceDriver($this->current_uri);
				echo "Using remote deployer driver ...\n";
			} else {

				$deployer_file = new LFile($this->current_uri);
				if (!$deployer_file->exists()) throw new \Exception("Unable to locate deployer file!");

				$this->current_driver = new LLocalDeployerInstanceDriver($deployer_file);
				echo "Using local deployer driver ...\n";
			}

			echo "Trying hello with server ...\n";

			if ($use_password) {
				echo "Using hello with password ...\n";
				$hello_result = $this->current_driver->hello($this->current_password);
			} else {
				echo "Using hello without password ...\n";
				$hello_result = $this->current_driver->hello();
			}

			if ($this->isSuccess($hello_result)) return true;
			else {
				return $this->unreachableDeployerServer($hello_result);
			}
		}

		return $this->loadKeyError($name);

	}

	private function unreachableDeployerServer($result) {

		$msg = "Deployer server is unreachable at : ".$this->current_uri." - ".$this->getResultMessage($result);
		
		$msg.= "\n\n";

		echo $msg;

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

	private function saveKey(string $name,string $local_deployer_path,string $deployer_uri) {

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

		$lw->writeln($local_deployer_path);

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
		echo "Generic usage : ./bin/deployer.sh <deploy_key_name> <command> <parameter1> <parameter2> <parameter...>";
		echo "\n\n";
		echo "Command List : \n\n";
		echo "./bin/deployer.sh --> prints this help\n\n";
		echo "./bin/deployer.sh <deploy_key_name> attach <local deployer.php path> <remote deployer.php full uri path> --> attaches the remote deployer and generates a local security token\n\n";
		echo "./bin/deployer.sh <deploy_key_name> detach --> detaches the deployer removing the server token and deleting the local key\n\n";
		echo "./bin/deployer.sh <deploy_key_name> get_exec_mode --> gets the execution mode on the deployer instance\n\n";
		echo "./bin/deployer.sh <deploy_key_name> set_exec_mode <running_mode> --> sets the execution mode on the deployer instance\n\n";
		echo "./bin/deployer.sh <deploy_key_name> add_ignore <path> --> adds a path to the custom ignore list\n\n";
		echo "./bin/deployer.sh <deploy_key_name> rm_ignore <path> --> removes a path from the custom ignore list\n\n";
		echo "./bin/deployer.sh <deploy_key_name> print_ignore --> prints the full ignore list\n\n";
		echo "./bin/deployer.sh <deploy_key_name> get_deployer_path_from_root --> gets the deployer path from root dir\n\n";
		echo "./bin/deployer.sh <deploy_key_name> set_deployer_path_from_root <deployer_path> --> set the deployer path from root dir\n\n";
		echo "./bin/deployer.sh <deploy_key_name> deployer_version --> prints the deployer version\n\n";
		echo "./bin/deployer.sh <deploy_key_name> deployer_update --> updates the remote deployer using the local version\n\n";
		echo "./bin/deployer.sh <deploy_key_name> framework_check --> check and lists which framework files needs an update\n\n";
		echo "./bin/deployer.sh <deploy_key_name> framework_update --> updates the remote framework files and prints a report of the operations done\n\n";
		echo "./bin/deployer.sh <deploy_key_name> project_check --> check and lists which remote project files needs an update\n\n";
		echo "./bin/deployer.sh <deploy_key_name> project_update --> updates the remote project files and prints a report of the operations done\n\n";
		echo "./bin/deployer.sh <deploy_key_name> auto_config --> updates the remote project config files probing the right config to use if possible\n\n";
		echo "./bin/deployer.sh <deploy_key_name> manual_config <host_name> --> updates the remote project config files using the selected host name configs\n\n";
		echo "./bin/deployer.sh <deploy_key_name> backup <dir_to_backup> <backup_dir_save_path> --> makes a full backup of a remote project dir folder and saves it in the backup dir specified\n\n";
		echo "./bin/deployer.sh <deploy_key_name> list_db --> lists all the db connections available on the server\n\n";
		echo "./bin/deployer.sh <deploy_key_name> backup_db_structure <connection_name> <save_dir> --> makes a full backup of a remote database structures and saves the zipped sql in the specified folder\n\n";
		echo "./bin/deployer.sh <deploy_key_name> backup_db_data <connection_name> <save_dir> --> makes a full backup of a remote database data and saves it in the specified folder\n\n";
		echo "./bin/deployer.sh <deploy_key_name> migrate_all --> executes all the missing migrations on the main database of the deployer instance\n\n";
		echo "./bin/deployer.sh <deploy_key_name> migrate_reset --> cleans up all the migrations and the main database on the deployer instance\n\n";
		echo "./bin/deployer.sh <deploy_key_name> migrate_list_done --> lists all executed migrations on the deployer instance\n\n";
		echo "./bin/deployer.sh <deploy_key_name> migrate_list_missing --> lists all the missing migrations on the deployer instance\n\n";
		echo "./bin/deployer.sh <deploy_key_name> disappear --> deletes the remote deployer\n\n";
		echo "./bin/deployer.sh <deploy_key_name> reset --> deletes all the remote files but not the deployer one\n\n";
		echo "./bin/deployer.sh <deploy_key_name> temp_clean --> cleans up the remote temporary files folder\n\n";

		echo "\n\n";

		echo "Available execution modes for command 'set_exec_mode' are :\n\n";

		foreach (self::EXEC_MODE_MAP as $short_name => $classic_name) {
			echo "- '".$short_name."' OR '".$classic_name."'\n";
		}

		echo "\n";

		return true;
	}

	private function load_ignore_list($key_name) {
		$ignore_file = new LFile('config/deployer/'.$key_name.'.ignore_list');

		if (!$ignore_file->exists()) {
			return [];
		}

		if (!$ignore_file->isReadable()) throw new \Exception("ignore file for deployment ".$key_name." is not readable!");

		$lr = $ignore_file->openReader();

		$result = [];

		while (!$lr->isEndOfStream()) {
			$result[] = $lr->readLine();
		}

		$lr->close();

		$result = array_remove_key_or_value($result,'');

		return $result;
	}

	private function save_ignore_list($key_name,array $entries) {
		$ignore_file = new LFile('config/deployer/'.$key_name.'.ignore_list');

		$config_deployer_dir = $ignore_file->getDirectory();

		if (!$config_deployer_dir->isWritable()) throw new \Exception("ignore list not writable in config directory.");

		if ($ignore_file->exists() && !$ignore_file->isWritable()) throw new \Exception("ignore list is not writable for deployment ".$key_name.".");

		if ($ignore_file->exists()) $ignore_file->delete();

		$wr = $ignore_file->openWriter();

		foreach ($entries as $entry) {
			$wr->writeln($entry);
		}

		$wr->close();

		return true;
	}

	public function add_to_ignore_list(string $key_name,string $path) {
		if ($this->loadKey($key_name)) {

			try {
				$entries = $this->load_ignore_list($key_name);
			}
			catch (\Exception $ex) {
				return $this->failure("Unable to read entry list for deployment ".$key_name.", operation canceled.");
			}

			if (in_array($path,$entries)) {
				echo "Path '".$path."' already in ignore list. Skipping...\n\n";

				return true;
			} else {

				$entries [] = $path;

				try {
					$this->save_ignore_list($key_name,$entries);
				}
				catch (\Exception $ex) {
					return $this->failure("Unable to save ignore list for deployment ".$key_name.", operation canceled.");
				}

				echo "Path '".$path."' added to ignore list.\n\n";

				return true;

			}
			
		} return $this->failure("Unable to load key ".$key_name);
	}

	public function rm_from_ignore_list(string $key_name,string $path) {
		if ($this->loadKey($key_name)) {

			try {
				$entries = $this->load_ignore_list($key_name);
			}
			catch (\Exception $ex) {
				return $this->failure("Unable to read entry list for deployment ".$key_name.", operation canceled.");
			}

			if (in_array($path,$entries)) {
				echo "Path '".$path."' found in list. Removing ...\n\n";

				$result = array_remove_key_or_value($entries,$path);

				try {
					$this->save_ignore_list($key_name,$result);
				}
				catch (\Exception $ex) {
					return $this->failure("Unable to save ignore list for deployment ".$key_name.", operation canceled.");
				}

				return true;
			} else {

				echo "Path '".$path."' not found in custom ignore list. Skipping ...\n\n";

				return true;

			}
			
		} return $this->failure("Unable to load key ".$key_name);
	}

	public function print_ignore_list(string $key_name) {
		if ($this->loadKey($key_name)) {

			try {
				$entries = $this->load_ignore_list($key_name);
			}
			catch (\Exception $ex) {
				return $this->failure("Unable to read entry list for deployment ".$key_name.", operation canceled.");
			}

			$default_ignore_list = $this->getProjectDefaultExcludeList();

			echo "Default standard project ignore list :\n\n";

			foreach ($default_ignore_list as $entry) {

				if ($entry == '@') $entry .= " (dynamic deployer path)";

				echo " - ".$entry."\n";
			}

			echo "\n";

			if (empty($entries)) {
				echo "Custom project ignore list is empty.\n\n";
			} else {
				echo "Custom project ignore list :\n\n";

				foreach ($entries as $entry) {
					echo " - ".$entry."\n";
				}

				echo "\n";
			}

			echo "\n\n";

			return $entries;

		} return $this->failure("Unable to load key ".$key_name);

	}

	public function hello(string $key_name) {
		if ($this->loadKey($key_name)) {

			$result = $this->current_driver->hello($this->current_password);

			if ($this->isSuccess($result)) return true;
			else return $this->failure("Unable to verify token with deployer instance.");
		} else return $this->failure("Unable to load key ".$key_name);
	}

	public function attach(string $key_name,string $local_deployer_path,string $deployer_uri) {

		$random_token = $this->saveKey($key_name,$local_deployer_path,$deployer_uri);
		echo "Key generated, starts with : [".substr($random_token,0,5)."...]\n";
		if ($this->loadKey($key_name,false)) {

			$local_deployer_file = new LFile($local_deployer_path);

			if (!$local_deployer_file->exists()) return $this->failure("Local deployer file is not found! : ".$local_deployer_file->getFullPath());

			$this->DPFR = $local_deployer_path;

			$this->current_password = $random_token;

			$result = $this->current_driver->setEnv("","PWD",$this->current_password);

			if ($this->isSuccess($result)) echo "Password changed to secure token.\n";
			else return $this->failure("Unable to change deployer secure token : ".$this->getResultMessage($result));

			echo "Waiting 5 seconds to let the server file cache update itself ...\n";
			sleep(5);

			$result2 = $this->current_driver->hello($this->current_password);

			if ($this->isSuccess($result2)) {
				echo "Hello with secure token successful.\n";
				return true;
			}
			else return $this->failure("Unable to correctly change password on deployer installation : ".$this->getResultMessage($result2));

		} else {
			echo "Unable to succesfully reach deployer instance, deleting generated key ...\n";
			$this->deleteKey($key_name);
			return $this->failure("Unable to find saved key ".$key_name);
		}
	}

	public function detach(string $key_name) {

		if ($this->loadKey($key_name)) {

			$result = $this->current_driver->setEnv($this->current_password,"PWD","");

			$this->deleteKey($key_name);

			if ($this->isSuccess($result)) return true;
				else return $this->failure("Unable to change password on deployer instance.");

		} else return $this->failure("Unable to find key to load for detach : ".$key_name);
	}

	private function getConfigModeUser($config_filename) {
		$r = $this->current_driver->readFileContent($this->current_password,"config/mode/".$config_filename);

		if ($this->isSuccess($r)) {
			return $r['data'];
		} else return false;
	}

	public function get_exec_mode(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->fileExists($this->current_password,"config/mode/");

			if ($this->isSuccess($r)) {

				if ($r['data']=='false') return $this->failure("Config directory does not exist in deployer instance.");

			}

			$r = $this->current_driver->listElements($this->current_password,"config/mode/");

			if (!$this->isSuccess($r)) return $this->failure("Unable to list config/mode/ directory in deployer instance.");

			$elements = $r['data'];

			if (in_array(LExecutionMode::FILENAME_FRAMEWORK_DEVELOPMENT,$elements)) {

				echo "Deployer instance execution mode : framework_development.\n";

				$user = $this->getConfigModeUser(LExecutionMode::FILENAME_FRAMEWORK_DEVELOPMENT);

				if ($user) echo "User that setted this execution mode : ".$user."\n";
				else return $this->failure("Unable to get user that setted this execution mode.");
				
				return 'framework_development';
			}
			if (in_array(LExecutionMode::FILENAME_DEVELOPMENT,$elements)) {

				echo "Deployer instance execution mode : development.\n";

				$user = $this->getConfigModeUser(LExecutionMode::FILENAME_DEVELOPMENT);

				if ($user) echo "User that setted this execution mode : ".$user."\n";
				else return $this->failure("Unable to get user that setted this execution mode.");

				return 'development';
			}
			if (in_array(LExecutionMode::FILENAME_TESTING,$elements)) {

				echo "Deployer instance execution mode : testing.\n";

				$user = $this->getConfigModeUser(LExecutionMode::FILENAME_TESTING);

				if ($user) echo "User that setted this execution mode : ".$user."\n";
				else return $this->failure("Unable to get user that setted this execution mode.");

				return 'testing';
			}
			if (in_array(LExecutionMode::FILENAME_PRODUCTION,$elements)) {

				echo "Deployer instance execution mode : production.\n";

				$user = $this->getConfigModeUser(LExecutionMode::FILENAME_PRODUCTION);

				if ($user) echo "User that setted this execution mode : ".$user."\n";
				else return $this->failure("Unable to get user that setted this execution mode.");

				return 'production';
			}
			if (in_array(LExecutionMode::FILENAME_MAINTENANCE,$elements)) {

				echo "Deployer instance execution mode : maintenance.\n";

				$user = $this->getConfigModeUser(LExecutionMode::FILENAME_MAINTENANCE);

				if ($user) echo "User that setted this execution mode : ".$user."\n";
				else return $this->failure("Unable to get user that setted this execution mode.");

				return 'maintenance';
			}

			return $this->failure("Unable to recognize execution mode from deployer instance result.");

		} else return false;
	}

	public function set_exec_mode(string $key_name,string $exec_mode) {

		$exec_mode_short = [
			LExecutionMode::MODE_MAINTENANCE_SHORT => LExecutionMode::FILENAME_MAINTENANCE,
			LExecutionMode::MODE_FRAMEWORK_DEVELOPMENT_SHORT => LExecutionMode::FILENAME_FRAMEWORK_DEVELOPMENT,
			LExecutionMode::MODE_DEVELOPMENT_SHORT => LExecutionMode::FILENAME_DEVELOPMENT,
			LExecutionMode::MODE_TESTING_SHORT => LExecutionMode::FILENAME_TESTING,
			LExecutionMode::MODE_PRODUCTION_SHORT => LExecutionMode::FILENAME_PRODUCTION
		];

		$exec_mode_classic = [
			LExecutionMode::MODE_MAINTENANCE => LExecutionMode::FILENAME_MAINTENANCE,
			LExecutionMode::MODE_FRAMEWORK_DEVELOPMENT => LExecutionMode::FILENAME_FRAMEWORK_DEVELOPMENT,
			LExecutionMode::MODE_DEVELOPMENT => LExecutionMode::FILENAME_DEVELOPMENT,
			LExecutionMode::MODE_TESTING => LExecutionMode::FILENAME_TESTING,
			LExecutionMode::MODE_PRODUCTION => LExecutionMode::FILENAME_PRODUCTION
		];

		if (!isset($exec_mode_short[$exec_mode]) && !isset($exec_mode_classic[$exec_mode])) {
			return $this->failure("Unable to recognize running mode '".$exec_mode."'. See help for more instructions.");
		}

		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->fileExists($this->current_password,"config/mode/");

			if ($this->isSuccess($r)) {

				if ($r['data']=='false') {
					echo "Config mode directory do not exists, creating it ...\n";

					$r = $this->current_driver->makeDir($this->current_password,"config/mode/");

					if (!$this->isSuccess($r)) { 
						return $this->failure("Unable to create config/mode/ directory on deployer instance : ".$r['message']);
					}
				}

			}

			$r = $this->current_driver->listElements($this->current_password,"config/mode/");

			if ($this->isSuccess($r)) {

				$elements = $r['data'];

				foreach ($elements as $el) {
					$r = $this->current_driver->deleteFile($this->current_password,"config/mode/".$el);

					if (!$this->isSuccess($r)) return $this->failure("Unable to successfully cleanup config/mode/ folder in deployer instance.");
				}

				//ok config mode cleaned, now need to create new mode file

				$filename = null;

				if (isset($exec_mode_short[$exec_mode])) $filename = $exec_mode_short[$exec_mode];
				if (isset($exec_mode_classic[$exec_mode])) $filename = $exec_mode_classic[$exec_mode];

				$content = LEnvironmentUtils::getServerUser();

				$r = $this->current_driver->writeFileContent($this->current_password,"config/mode/".$filename,$content);

				if (!$this->isSuccess($r)) return $this->failure("Error during write of running mode file on deployer instance.");

				return true;

			} else return $this->failure("Unable to list config/mode/ directory on deployer instance : ".$r['message']);

		} else return false;
	}

	public function list_db(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->listDb($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to succesfully get connection name list from server : ".$this->getResultMessage($r));

			$connection_name_list = $r['data'];

			echo "Connection name list available on deployer instance :\n\n";

			foreach ($connection_name_list as $connection_name) {
				echo "- ".$connection_name."\n";
			}
			echo "\n";

			return true;

		} else return false;
	}

	public function backup_db_structure(string $key_name,string $connection_name,string $save_dir) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$backup_dir = new LDir($save_dir);

			$backup_filename = "backup_db_".$connection_name."_structure__".date('Y_m_d__h_i').".zip";

			$backup_file = $backup_dir->newFile($backup_filename);

			$r = $this->current_driver->backupDbStructure($this->current_password,$connection_name,$backup_file);

			if ($this->isSuccess($r)) {
				echo "Backup db structure file download successfully\n";
				echo "[".$backup_file->getFilename()." - size ".$backup_file->getSize()." bytes] ...\n";
			}
			return true;

		} else return false;
	}

	public function backup_db_data(string $key_name,string $connection_name,string $save_dir) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$backup_dir = new LDir($save_dir);

			$backup_filename = "backup_db_".$connection_name."_data__".date('Y_m_d__h_i').".zip";

			$backup_file = $backup_dir->newFile($backup_filename);

			$r = $this->current_driver->backupDbData($this->current_password,$connection_name,$backup_file);

			if ($this->isSuccess($r)) {
				echo "Backup db data file download successfully\n";
				echo "[".$backup_file->getFilename()." - size ".$backup_file->getSize()." bytes] ...\n";
			}
			return true;

		} else return false;
	}

	public function migrate_all(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$result = $this->current_driver->migrateAll($this->current_password);

			if ($this->isSuccess($result)) {
				echo "Missing migrations executed successfully.\n";

				return true;
			} 
			else 
				return $this->failure("Unable to execute missing migrations on deployer installation : ".$result['message']);
		} else return false;
	}


	public function migrate_reset(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$result = $this->current_driver->migrateReset($this->current_password);

			if ($this->isSuccess($result)) {
				echo "Remote database and migrations resetted successfully.\n";

				return true;
			} 
			else 
				return $this->failure("Unable to clean up main database and reset migrations on deployer installation : ".$result['message']);
		} else return false;
	}


	public function migrate_list_done(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->migrateListDone($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to succesfully get migration list from server : ".$this->getResultMessage($r));

			$migration_list = $r['data'];

			echo "Executed migration list found on deployer instance :\n\n";

			if (empty($migration_list)) {
				echo "No migrations found.\n";
			}
			else foreach ($migration_list as $migration_name) {
				echo "- ".$migration_name."\n";
			}
			echo "\n";

			return true;

		} else return false;
	}

	public function migrate_list_missing(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->migrateListMissing($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to succesfully get migration list from server : ".$this->getResultMessage($r));

			$migration_list = $r['data'];

			echo "Missing migration list found on deployer instance :\n\n";

			if (empty($migration_list)) {
				echo "No migrations found.\n";
			}
			else foreach ($migration_list as $migration_name) {
				echo "- ".$migration_name."\n";
			}
			echo "\n";

			return true;

		} else return false;
	}
 
	public function get_deployer_path_from_root(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->getEnv($this->current_password,self::DEPLOYER_PATH_FROM_ROOT);

			if (!$this->isSuccess($r)) return $this->failure("Unable to get environment variable from deployer instance : ".$this->getResultMessage($r));

			echo "Deployer path from root is : ".$r['data']."\n\n";

			return $r['data'];

		} else return false;
	}

	public function set_deployer_path_from_root(string $key_name,string $deployer_path) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$r = $this->current_driver->setEnv($this->current_password,self::DEPLOYER_PATH_FROM_ROOT,$deployer_path);

			if (!$this->isSuccess($r)) return $this->failure("Unable to set environment variable from deployer instance : ".$this->getResultMessage($r));

			echo "Deployer path from root now is : ".$deployer_path."\n\n";

			return true;

		} else return false;
	}

	public function deployer_version(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if ($this->isSuccess($r)) {
				
				$version = $r['version'];
				$build = $r['build'];
				$features = $r['features'];

				echo "\n\nDeployer Version : ".$version;
				echo "\n\nBuild number : ".$build;
				echo "\n\nDeployer Features : \n";

				foreach ($features as $f) {
					echo "- ".$f."\n";
				}
				echo "\n";

				return $r['version'];
			}
			else $this->failure("Unable to update deployer on server : ".$this->getResultMessage($r));

		} else return false;
	}

	public function deployer_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			echo "Preparing for deployer update ...\n";

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			sleep(5);

			$uri_parts = explode('/',$this->current_uri);
			$deployer_filename = end($uri_parts);

			$updated_deployer = new LFile($_SERVER['FRAMEWORK_DIR'].'/tools/deployer.php');

			$r = $this->current_driver->setEnv($this->current_password,"PWD","");

			echo "Waiting for file cache to update correctly ...\n";

			sleep(5);

			if (!$this->isSuccess($r)) return $this->failure("Unable to complete deployer update procedure.");

			$result = $this->current_driver->getEnv("","DPFR");

			if (!$this->isSuccess($result)) return $this->failure("Unable to get deployer path from root from deployer instance");

			$deployer_path_from_root = $result['data'];

			$r1 = $this->current_driver->copyFile("",$deployer_path_from_root,$updated_deployer);

			echo "Waiting for file cache to update correctly again ...\n";

			sleep(5);

			$r2 = $this->current_driver->setEnv("","DPFR",$deployer_path_from_root);

			echo "Waiting for file cache to update correctly after setup of deployer path from root ...\n";

			sleep(5);

			$r3 = $this->current_driver->setEnv("","PWD",$this->current_password);

			echo "Again you need to wait until file cache updates after password setup ...\n";

			sleep(5);

			$r4 = $this->current_driver->hello($this->current_password);

			if (!$this->isSuccess($r1) || !$this->isSuccess($r2) || !$this->isSuccess($r3) || !$this->isSuccess($r4)) return $this->failure("Unable to complete deployer update procedure. Use set_deployer_path_from_root to fix deployer path env var on server.");

			echo "Deployer update completed successfully.\n";

			return true;

		} else return false;
	}

	public function disappear(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

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

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$uri_parts = explode('/',$this->current_uri);
			$deployer_filename = end($uri_parts);

			$r = $this->current_driver->listElements($this->current_password,'/');

			if ($this->isSuccess($r)) {
				$elements = $r['data'];

				foreach ($elements as $el) {
					if (LStringUtils::endsWith($el,'/')) {
						$r2 = $this->current_driver->deleteDir($this->current_password,$el,true);

						if (!$this->isSuccess($r2)) return $this->failure("Unable to delete directory : ".$el." : ".$this->getResultMessage($r2));
					} else {
						if (!LStringUtils::endsWith($el,$deployer_filename)) {
							$r3 = $this->current_driver->deleteFile($this->current_password,$el);

							if (!$this->isSuccess($r3)) return $this->failure("Unable to delete file : ".$el." : ".$this->getResultMessage($r3));
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

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$result = $this->current_driver->listElements($this->current_password,'/');

			if ($this->isSuccess($result)) {
				$elements = $result['data'];

				$temp_found = false;
				foreach ($elements as $el) {
					if ($el=='temp/') $temp_found = true;
				}

				if (!$temp_found) 
				{
					echo "temp directory not found, creating it.\n";

					$this->current_driver->makeDir($this->current_password,'temp/');

					return true;
				}

				$r1 = $this->current_driver->deleteDir($this->current_password,'temp/',true);
				$r2 = $this->current_driver->makeDir($this->current_password,'temp/');

				if ($this->isSuccess($r1) && $this->isSuccess($r2)) {
					
					echo "Deployer instance 'temp' folder cleanup executed successfully.\n";

					return true;
				}
				else return $this->failure("Unable to delete and recreate temp folder on deployer installation.");
			} else return $this->failure("Unable to list files on deployer installation.");
		} else return false;
	}

	private function getFrameworkIncludeList() {
		return [FRAMEWORK_DIR_NAME."/"];
	}

	private function getFrameworkExcludeList() {
		return [FRAMEWORK_DIR_NAME."/project_image/",FRAMEWORK_DIR_NAME."/bin/",FRAMEWORK_DIR_NAME."/tools/",FRAMEWORK_DIR_NAME."/tests/",FRAMEWORK_DIR_NAME."/tests_fast/",FRAMEWORK_DIR_NAME."/docs/",FRAMEWORK_DIR_NAME."/init.php",FRAMEWORK_DIR_NAME."/lib/deployer/"];
	}

	public function framework_check(string $key_name) {

		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$old_framework_dir = $_SERVER['FRAMEWORK_DIR'];
				$testing = true;
				$_SERVER['FRAMEWORK_DIR'] = $_SERVER['PROJECT_DIR'].FRAMEWORK_DIR_NAME.'/';
			} else {
				$testing = false;
			}

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			echo "PROJECT_DIR : ".$_SERVER['PROJECT_DIR']."\n";
			echo "FRAMEWORK_DIR : ".$_SERVER['FRAMEWORK_DIR']."\n";

			sleep(3);

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

			$this->changesListSummary(false);

			if ($testing) {
				$_SERVER['FRAMEWORK_DIR'] = $old_framework_dir;
			} else {
				$_SERVER['PROJECT_DIR'] = $temp_project_dir_path;
			}

			return ["dirs_to_add" => $this->dirs_to_add,'files_to_add' => $this->files_to_add,
			'dirs_to_update' => $this->dirs_to_update,'files_to_update' => $this->files_to_update,
			'files_to_delete' => $this->files_to_delete,'dirs_to_delete' => $this->dirs_to_delete];

			
		} else return false;

	}

	public function framework_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			$framework_dir = new LDir($_SERVER['FRAMEWORK_DIR']);
			$project_dir = new LDir($_SERVER['PROJECT_DIR']);

			if ($framework_dir->isParentOf($project_dir)) {
				$old_framework_dir = $_SERVER['FRAMEWORK_DIR'];
				$testing = true;
				$_SERVER['FRAMEWORK_DIR'] = $_SERVER['PROJECT_DIR'].FRAMEWORK_DIR_NAME.'/';
			} else {
				$testing = false;
			}

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";
			
			echo "PROJECT_DIR : ".$_SERVER['PROJECT_DIR']."\n";
			echo "FRAMEWORK_DIR : ".$_SERVER['FRAMEWORK_DIR']."\n";

			sleep(3);

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

			$this->changesListSummary(true);

			$this->executeChangesList();

			if ($testing) {
				$_SERVER['FRAMEWORK_DIR'] = $old_framework_dir;
			} else {
				$_SERVER['PROJECT_DIR'] = $temp_project_dir_path;
			}

			return true;

		} else return false;

	}

	private function getProjectDefaultExcludeList() {
		return ['.alias','.bash_history','.bash_profile','.bashrc','.cshrc','.cache/','.config/','.gnupg/','Maildir/','.local/','.php/','composer.json','composer.lock','@','config/',FRAMEWORK_DIR_NAME.'/','bin/','logs/','temp/','composer.json'];
	}

	private function getProjectExcludeList($key_name) {

		$default_excludes = $this->getProjectDefaultExcludeList();

		$custom_ignores = $this->load_ignore_list($key_name);

		return array_merge($default_excludes,$custom_ignores);
	}

	public function project_check(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			echo "PROJECT_DIR : ".$_SERVER['PROJECT_DIR']."\n";
			echo "FRAMEWORK_DIR : ".$_SERVER['FRAMEWORK_DIR']."\n";

			sleep(3);

			$r = $this->current_driver->listHashes($this->current_password,$this->getProjectExcludeList($key_name),[]);

			if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance : ".$this->getResultMessage($r));

			$server_list = $r['data'];

			$client_list = $this->clientListHashes($this->getProjectExcludeList($key_name),[]);

			$this->setupChangesList($client_list,$server_list);

			$this->previewChangesList();

			$this->changesListSummary(false);

			return ["dirs_to_add" => $this->dirs_to_add,'files_to_add' => $this->files_to_add,
			'dirs_to_update' => $this->dirs_to_update,'files_to_update' => $this->files_to_update,
			'files_to_delete' => $this->files_to_delete,'dirs_to_delete' => $this->dirs_to_delete];

		} else 
			return false;
		
	}

	public function project_update(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			echo "PROJECT_DIR : ".$_SERVER['PROJECT_DIR']."\n";
			echo "FRAMEWORK_DIR : ".$_SERVER['FRAMEWORK_DIR']."\n";

			sleep(3);

			$r = $this->current_driver->listHashes($this->current_password,$this->getProjectExcludeList($key_name),[]);

			if (!$this->isSuccess($r)) return $this->failure("Unable to get hashes from deployer instance.");

			$server_list = $r['data'];

			$client_list = $this->clientListHashes($this->getProjectExcludeList($key_name),[]);

			$this->setupChangesList($client_list,$server_list);

			$this->previewChangesList();

			$this->changesListSummary(true);

			$this->executeChangesList();

			return true;

			
		} else return false;
	}

	public function backup(string $key_name,string $remote_folder,string $save_dir_path) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			$current_dir = new LDir('');

			$current_dir_name = $current_dir->getName();

			$fixed_key_name = str_replace('.','_',$key_name);

			$fixed_remote_folder = str_replace('/','_',$remote_folder);

			$backup_filename = "backup_".$current_dir_name.'_'.$fixed_remote_folder."_".date('Y_m_d__h_i').".zip";

			$save_dir = new LDir($save_dir_path);

			$save_dir->touch();

			$backup_file = $save_dir->newFile($backup_filename);

			$r = $this->current_driver->downloadDir($this->current_password,$remote_folder,$backup_file);
		
			if ($this->isSuccess($r)) {
				
				echo "Backup file ".$backup_filename." saved successfully.\n";

				return true;
			}
			else return $this->failure("Unable to backup remote installation into zip file : ".$r['message']);
		} else return false;
	}

	private function executeConfigSync($config_folder) {
		$cf = new LDir($_SERVER['PROJECT_DIR'].'/config/hostnames/'.$config_folder);

		if (!$cf->exists()) return $this->failure("Specified hostname not found in config folder : ".$config_folder);

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

		echo "Config directory ".$config_folder." and internal copied successfully.\n";

		return true;
	}

	public function auto_config(string $key_name) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

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

				return $this->executeConfigSync($hostname);

			} else return $this->failure("Unable to probe the host name from deployer configuration. Use manual_config.");

		} else return false;
	}

	public function manual_config(string $key_name,string $config_folder) {
		if ($this->loadKey($key_name)) {

			$r = $this->current_driver->version($this->current_password);

			if (!$this->isSuccess($r)) return $this->failure("Unable to successfully get version from deployer.");

			echo "Deployer version : ".$r['version']."\n";
			echo "Build number : ".$r['build']."\n";

			return $this->executeConfigSync($config_folder);
		} else return false;
	}

}