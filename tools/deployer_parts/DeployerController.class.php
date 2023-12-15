<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

$current_dir = __DIR__;

if (!DStringUtils::endsWith($current_dir,'/')) $current_dir.='/';

$_SERVER['DEPLOYER_PROJECT_DIR'] = $current_dir;

//starting deployer controller ---

if (!class_exists('DeployerController')) {
    class DeployerController {

        const BUILD_NUMBER = 70;

        const DEPLOYER_VERSION = "1.5";

        const DEPLOYER_FEATURES = ['version','listElements','listHashes','deleteFile','makeDir','deleteDir','copyFile','downloadDir','setEnv','getEnv','listEnv','hello','fileExists','writeFileContent','readFileContent','listDb','backupDbStructure','backupDbData','migrateAll','migrateReset','migrateListDone','migrateListMissing','fixPermissions'];

    	private $deployer_file;
    	private $root_dir;

        //password
    	private static $PWD = /*!P_W_D!*/""/*!P_W_D!*/; 

        //deployer path from root
        private static $DPFR = /*!D_P_F_R!*/"deployer.php"/*!D_P_F_R!*/; 

    	const SUCCESS_RESULT = ":)";
    	const FAILURE_RESULT = ":(";

    	function __construct() {

    		$this->deployer_file = new DFile(__FILE__);

            $path_parts = explode('/',self::$DPFR);

            $current_dir = new DDir(__DIR__);

            for ($i=0;$i<count($path_parts)-1;$i++) $current_dir = $current_dir->getParentDir();

    		$this->root_dir = $current_dir;

            $_SERVER['DEPLOYER_PROJECT_DIR'] = $this->root_dir->getFullPath();

    	}

        private function logWithFile(string $file_name,string $content) {
            $f = new DFile($file_name);
            $f->setContent($content);
        }

        private function hasPostParameter($param_name) {

            return isset($_POST[$param_name]);

        }

        private function isPostParameterDangerous($param_name) {
            $filtered_param_value = $this->getPostParameter($param_name);

            $raw_param_value = $_POST[$param_name];

            if ($filtered_param_value!=$raw_param_value) return true;
            else return false;
        }

        private function getPostParameter($param_name) {
            $value = $_POST[$param_name];

            $final_value = filter_var($value,FILTER_DEFAULT);

            return $final_value;
        }

        public function version($password) {
            if ($this->accessGranted($password)) {
                return ['result' => self::SUCCESS_RESULT,'version' => self::DEPLOYER_VERSION,'features' => self::DEPLOYER_FEATURES,'build' => self::BUILD_NUMBER];
            } else return $this->failure("Wrong password");
        }

        private function getFinalPathList($path_list) {

            $result = [];

            foreach ($path_list as $p) {
                if ($p==='@') $result[] = self::$DPFR;
                    else $result[] = $p;
            }

            return $result;

        }

        private function containsDeployerPath($path_list) {
            return in_array('@',$path_list);
        }

        private function loadFrameworkBasicClasses() {

            $path_prefix = FRAMEWORK_DIR_NAME.'/';

            $f = new DFile($path_prefix.'framework_spec.php');
            if (!$f->exists()) return $path_prefix.'framework_spec.php not found.';
            $f->requireFileOnce();

            $f = new DFile($path_prefix.'lib/treemap/LTreeMap.class.php');
            if (!$f->exists()) return $path_prefix.'lib/treemap/LTreeMap.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/treemap/LTreeMapView.class.php');
            if (!$f->exists()) return $path_prefix.'lib/treemap/LTreeMapView.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/treemap/LStaticTreeMapBase.trait.php');
            if (!$f->exists()) return $path_prefix.'lib/treemap/LStaticTreeMapBase.trait.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/treemap/LStaticTreeMapRead.trait.php');
            if (!$f->exists()) return $path_prefix.'lib/treemap/LStaticTreeMapRead.trait.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/treemap/LStaticTreeMapWrite.trait.php');
            if (!$f->exists()) return $path_prefix.'lib/treemap/LStaticTreeMapWrite.trait.php not found.';
            $f->requireFileOnce();

            //config
            $f = new DFile($path_prefix.'lib/config/LConfig.class.php');
            if (!$f->exists()) return $path_prefix.'lib/config/LConfig.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/config/LConfigReader.class.php');
            if (!$f->exists()) return $path_prefix.'lib/config/LConfigReader.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/config/LExecutionMode.class.php');
            if (!$f->exists()) return $path_prefix.'lib/config/LExecutionMode.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/config/LEnvironmentUtils.class.php');
            if (!$f->exists()) return $path_prefix.'lib/config/LEnvironmentUtils.class.php not found.';
            $f->requireFileOnce();

            //core
            $f = new DFile($path_prefix.'lib/core/LErrorReportingInterceptors.class.php');
            if (!$f->exists()) return $path_prefix.'lib/core/LErrorReportingInterceptors.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/core/LInvalidParameterException.class.php');
            if (!$f->exists()) return $path_prefix.'lib/core/LInvalidParameterException.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/core/LResult.class.php');
            if (!$f->exists()) return $path_prefix.'lib/core/LResult.class.php not found.';
            $f->requireFileOnce();

            $f = new DFile($path_prefix.'lib/core/LClassLoader.class.php');
            if (!$f->exists()) return $path_prefix.'lib/core/LClassLoader.class.php not found.';
            $f->requireFileOnce();

            //utils
            $f = new DFile($path_prefix.'lib/utils/LStringUtils.class.php');
            if (!$f->exists()) return $path_prefix.'lib/utils/LStringUtils.class.php not found.';
            $f->requireFileOnce();
            $f = new DFile($path_prefix.'lib/utils/LJsonUtils.class.php');
            if (!$f->exists()) return $path_prefix.'lib/utils/LJsonUtils.class.php not found.';
            $f->requireFileOnce();

            $f = new DFile($path_prefix.'lib/db/functions.php');
            if (!$f->exists()) return $path_prefix.'lib/db/functions.php not found.';
            $f->requireFileOnce();

            if (!LConfig::initCalled()) LConfig::init();

            if (!LClassLoader::initCalled()) LClassLoader::init();

            return true;

        }

        public function listDb($password) {

            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) {

                        return $this->failure($result);
                    }
                } catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                $db_list = LConfigReader::simple('/database');

                $result_data = [];

                foreach ($db_list as $db_name => $db_data) {
                    $result_data[] = $db_name;
                }

                return ["result" => self::SUCCESS_RESULT,"data" => $result_data];

            } else return $this->failure("Wrong password.");
        }

        public function backupDbStructure($password,$connection_name) {
            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) return $this->failure($result);
                }
                catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                if (!LDbConnectionManager::has($connection_name)) return $this->failure("Unable to find db connection with name : ".$connection_name);

                $connection = LDbConnectionManager::get($connection_name);

                $zip_dir = new DDir('temp/backup/db/structure/'.$connection_name.'/');
                if ($zip_dir->exists()) $zip_dir->delete(true);
                sleep(3);
                $zip_dir->touch();

                if (!$zip_dir->exists()) return $this->failure("Unable to create temporary dir necessary for storing and compressing files.");

                try {
                    $db = db($connection_name);

                    $table_list = table_list()->go($db);

                    foreach ($table_list as $tb) {
                        $query = create_table($tb)->show()->go($db);
                        
                        $qf = $zip_dir->newFile($tb.'__structure.sql');
                        $qf->setContent($query."\n\n");
                    }
                } catch (\Exception $ex) {

                    return $this->failure("Exception during query phase : ".$ex->getMessage());
                } 

                $zip_file = new DFile('temp/backup/db/structure/'.$connection_name.'_structure_bkp.zip');

                DZipUtils::createArchive($zip_file,'temp/backup/db/structure/'.$connection_name.'/','');

                return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

            } else return $this->failure("Wrong password.");
        }

        public function backupDbData($password,$connection_name) {
            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) return $this->failure($result);
                }
                catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                if (!LDbConnectionManager::has($connection_name)) return $this->failure("Unable to find db connection with name : ".$connection_name);

                $connection = LDbConnectionManager::get($connection_name);

                $zip_dir = new DDir('temp/backup/db/data/'.$connection_name.'/');
                if ($zip_dir->exists()) $zip_dir->delete(true);
                $zip_dir->touch();

                if (!$zip_dir->exists()) return $this->failure("Unable to create temporary dir necessary for storing and compressing files.");

                try {
                    $db = db($connection_name);

                    $table_list = table_list()->go($db);

                    foreach ($table_list as $tb) {
                        $iterator = select('*',$tb)->iterator($db);
                        
                        $qf = $zip_dir->newFile($tb.'__data.sql');
                        $wr = $qf->openWriter();

                        while ($iterator->hasNext()) {
                            $data = $iterator->next();

                            $query = insert($tb,array_keys($data),array_values($data)).";";

                            $wr->writeln($query);
                            $wr->writeln("");

                        }

                        $wr->close();
                    }
                } catch (\Exception $ex) {

                    return $this->failure("Exception during query phase : ".$ex->getMessage());
                } 

                $zip_file = new DFile('temp/backup/db/data/'.$connection_name.'_data_bkp.zip');

                DZipUtils::createArchive($zip_file,'temp/backup/db/data/'.$connection_name.'/','');

                return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

            } else return $this->failure("Wrong password.");
        }

        public function migrateAll($password) {
            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) return $this->failure($result);
                }
                catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                LResult::disableOutput();

                LMigrationSupport::executeAllMigrations();

                return ['result' => self::SUCCESS_RESULT];

            } else return $this->failure("Wrong password.");
        }

        public function migrateReset($password) {
            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) return $this->failure($result);
                }
                catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                LResult::disableOutput();

                LMigrationSupport::resetAllMigrations();

                return ['result' => self::SUCCESS_RESULT];

            } else return $this->failure("Wrong password.");
        }

        public function migrateListDone($password) {
            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) return $this->failure($result);
                }
                catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                LResult::disableOutput();

                $mh_list = LMigrationSupport::printAllExecutedMigrations();

                $result = [];

                foreach ($mh_list as $mh) $result[] = ">> ".$mh;

                return ['result' => self::SUCCESS_RESULT,'data' => $result];

            } else return $this->failure("Wrong password.");
        }

        public function migrateListMissing($password) {
            if ($this->accessGranted($password)) {

                try {
                    $result = $this->loadFrameworkBasicClasses();

                    if ($result!==true) return $this->failure($result);
                }
                catch (\Exception $ex) {
                    return $this->failure("Some framework classes are missing, use framework_update to upload framework classes ...");
                }

                if (!LConfigReader::has('/database')) return $this->failure("No config files are present in order to look for database connections.");

                LResult::disableOutput();

                $mh_list = LMigrationSupport::printAllMissingMigrations();

                $result = [];

                foreach ($mh_list as $mh) $result[] = ">> ".$mh;

                return ['result' => self::SUCCESS_RESULT,'data' => $result];

            } else return $this->failure("Wrong password.");
        }

        public function fileExists($password,$path) {
            if ($this->accessGranted($password)) {

                $final_path = $this->root_dir->getFullPath().$path;

                if (file_exists($final_path)) return ["result" => self::SUCCESS_RESULT,"data" => 'true'];
                else return ["result" => self::SUCCESS_RESULT,"data" => 'false'];

            } else return $this->failure("Wrong password.");
        }

        public function readFileContent($password,$path) {
            if ($this->accessGranted($password)) {

                $f = new DFile($path);

                if (!$f->exists()) return $this->failure("File at path '".$path."' do not exists on this deployer instance.");

                if (!$f->isReadable()) return $this->failure("File at path '".$path."' is not readable on this deployer instance.");

                $content = $f->getContent();

                return ["result" => self::SUCCESS_RESULT,"data" => $content];

            } else return $this->failure("Wrong password.");
        }

        public function writeFileContent($password,$path,$content) {
            if ($this->accessGranted($password)) {

                $f = new DFile($path);

                $parent_dir = $f->getDirectory();

                if (!$parent_dir->exists()) $parent_dir->touch();

                if ($f->exists() && !$f->isWritable()) return $this->failure("File at path '".$path."' already exists but is not writable in this deployer instance.");

                $f->setContent($content);

                return ["result" => self::SUCCESS_RESULT];

            } else return $this->failure("Wrong password.");
        }

    	public function listElements($password,$folder) {
    		if ($this->accessGranted($password)) {

    			if (!DStringUtils::endsWith($folder,'/')) return $this->failure("Folder name to list does not end with /.");

    			$dir = new DDir($this->root_dir->getFullPath().$folder);

    			if ($dir->exists() && $dir->isReadable()) {

    				$folder_list = $dir->listFolders();
    				$file_list = $dir->listFiles();

    				$data = [];
    				foreach ($folder_list as $f) $data[] = $f->getName().'/';
    				foreach ($file_list as $f) $data[] = $f->getFilename();

    				return ["result" => self::SUCCESS_RESULT,"data" => $data];

    			} else return ["result" => self::SUCCESS_RESULT,"data" => []];

    		} else return $this->failure("Wrong password.");
    	}

    	public function listHashes($password,$excluded_paths,$included_paths) {
    		if ($this->accessGranted($password)) {

                if ($this->containsDeployerPath($excluded_paths)) {
                    $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                    if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
                }

                if ($this->containsDeployerPath($included_paths)) {
                    $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                    if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
                }

                $inspector = new DContentHashInspector();

                $inspector->setExcludedPaths($this->getFinalPathList($excluded_paths));
                $inspector->setIncludedPaths($this->getFinalPathList($included_paths));

                $pre_include_result = [];

    			if (count($inspector->getIncludedPaths())>0) {
    				foreach ($inspector->getIncludedPaths() as $path) {
    					$my_dir = new DDir($this->root_dir->getFullPath().$path);

    					$pre_include_result = array_merge($my_dir->explore($inspector),$pre_include_result);
    				}
    			} else {
    				$pre_include_result = $this->root_dir->explore($inspector);
    			}

                $pre_result = array_remove_key_or_value($pre_include_result,'');

                $final_result = [];

                if (empty($inspector->getExcludedPaths()))
                    $final_result = $pre_result;
                else {
                    foreach ($pre_result as $path => $hash) {
                        $skip = false;
                        foreach ($inspector->getExcludedPaths() as $excluded) {
                            if (DStringUtils::startsWith($path,$excluded)) 
                                $skip = true;
                            if (DStringUtils::startsWith($path,'config/deployer/'))
                                $skip = true;
                        }

                        if (!$skip) $final_result[$path] = $hash;
                    }
                }
                $final_result_2 = array_remove_key_or_value($final_result,'');

                return ["result" => self::SUCCESS_RESULT,"data" => $final_result_2];

    		} else return $this->failure("Wrong password.");
    	}

        public function fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths) {
            if ($this->accessGranted($password)) {

                if (!DFileSystemUtils::isPermissionsFlagsValid($permissions_to_set)) return $this->failure("Permissions flags are not in a valid form!");

                if ($this->containsDeployerPath($excluded_paths)) {
                    $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                    if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
                }

                if ($this->containsDeployerPath($included_paths)) {
                    $calc_deployer_file = new DFile($this->root_dir->getFullPath().self::$DPFR);

                    if ($calc_deployer_file->getFullPath()!=$this->deployer_file->getFullPath()) return $this->failure("Deployer path from root dir is not correctly set!");
                }

                $inspector = new DPermissionsFixerInspector($permissions_to_set);

                $inspector->setExcludedPaths($this->getFinalPathList($excluded_paths));
                $inspector->setIncludedPaths($this->getFinalPathList($included_paths));

                $pre_include_result = [];

                if (count($inspector->getIncludedPaths())>0) {
                    foreach ($inspector->getIncludedPaths() as $path) {
                        $my_dir = new DDir($this->root_dir->getFullPath().$path);

                        $my_dir->explore($inspector);
                    }
                } else {
                    $this->root_dir->explore($inspector);
                }

                return ["result" => self::SUCCESS_RESULT];

            } else return $this->failure("Wrong password.");
        }

    	public function deleteFile($password,$path) {
    		if ($this->accessGranted($password)) {

                if (DStringUtils::endsWith($path,'/')) return $this->failure("Use deleteDir to delete directories. Actual path found is : ".$path);

                $full_path = $this->root_dir->getFullPath().$path;

                if (DFileSystemUtils::isDir($full_path)) return $this->failure("Actual path is a directory and should be deleted using deleteDir. Path is : ".$path);

    			$f = new DFile($this->root_dir->getFullPath().$path);

    			if ($f->exists()) {

    				$result = $f->delete();

                    if ($f->getPath()==$this->deployer_file->getPath() && $result) {
                        self::$PWD = "";
                    }

    				return ["result" => self::SUCCESS_RESULT];
    			} else return $this->failure("File to delete does not exist.");


    		} else return $this->failure("Wrong password.");
    	}

    	public function makeDir($password,$path) {
    		if ($this->accessGranted($password)) {

    			if (!DStringUtils::endsWith($path,'/')) return $this->failure("Directory name to create does not end with /.");

    			$dest = new DDir($this->root_dir->getFullPath().$path);

    			$dest->touch();

    			if ($dest->exists()) return ["result" => self::SUCCESS_RESULT];
    			else return $this->failure("Unable to create directory.");

    		} else return $this->failure("Wrong password.");
    	}

    	public function deleteDir($password,$path,$recursive) {
    		if ($this->accessGranted($password)) {

    			if (!DStringUtils::endsWith($path,'/')) return $this->failure("The directory name does not ends with /.");

    			$dest = new DDir($this->root_dir->getFullPath().$path);

    			if (!$dest->exists()) return $this->failure("Directory to delete does not exist : ".$dest->getFullPath());

    			$dest->delete($recursive);

    			if (!$dest->exists()) return ["result" => self::SUCCESS_RESULT];
    			else return $this->failure("Unable to delete directory.");

    		} else return $this->failure("Wrong password.");
    	}

    	public function copyFile($password,$path) {

    		if ($this->accessGranted($password)) {
    			if (isset($_FILES['f']) && $_FILES['f']['error'] == UPLOAD_ERR_OK) {

    				if (DStringUtils::endsWith($path,'/')) return $this->failure("File name should not end with a directory separator.");

    				$content = file_get_contents($_FILES['f']['tmp_name']);

                    if ($content===null) return $this->failure("Unable to read content of uploaded file!");

    				$dest = new DFile($this->root_dir->getFullPath().$path);

    				$dir = $dest->getDirectory();

    				if (!$dir->exists()) return $this->failure("Parent directory does not exist.");

    				$dest->setContent($content);

                    $dest->setPermissions('-rw-r--r--');

    				if ($dest->getSize()!=$_FILES['f']['size']) {
    					$dest->delete();
    					return $this->failure("Size of file is wrong after write.");
    				}

    				return ["result" => self::SUCCESS_RESULT];

    			} else return $this->failure("File 'f' is not present or upload failure.");
    		} 
    		else return $this->failure("Wrong password.");
    	}

    	public function downloadDir($password,$path) {
    		if ($this->accessGranted($password)) {

    			if (!DStringUtils::endsWith($path,'/')) return $this->failure("Folder to zip does not ends with /.");

    			$source = new DDir($this->root_dir->getFullPath().$path);

    			if (!$source->exists()) return $this->failure("Directory to zip and get does not exist.");

    			$zip_file = $this->root_dir->newFile("my_dir.zip");

    			DZipUtils::createArchive($zip_file,$source);

    			if (!$zip_file->exists()) return $this->failure("Unable to create zip file.");

    			return ["result" => self::SUCCESS_RESULT,"data" => $zip_file];

    		} else $this->failure("Wrong password.");
    	}

        //using a trick to avoid replacement

        const ENV_VAR_NAME_MAP = ['PWD' => 'Deployer Password','DPFR' => 'Deployer path from root'];

        public function listEnv($password) {
            if ($this->accessGranted($password)) {
                return ['result' => self::SUCCESS_RESULT,'data' => self::ENV_VAR_NAME_MAP];
            } return $this->failure("Wrong password.");
        }

        public function getEnv($password,$env_var_name) {
            if ($this->accessGranted($password)) {
                if (!isset(self::ENV_VAR_NAME_MAP[$env_var_name])) return $this->failure("Unavailable environment variable to get : ".$env_var_name);

                $data = null;
                if ($env_var_name=='PWD') $data = self::$PWD;
                if ($env_var_name=='DPFR') $data = self::$DPFR;

                return ["result" => self::SUCCESS_RESULT,'data' => $data];
            } else return $this->failure("Wrong password.");
        }

    	public function setEnv($password,$env_var_name,$env_var_value) {
    		if ($this->accessGranted($password)) {
    			$deployer_content = $this->deployer_file->getContent();

                if (!isset(self::ENV_VAR_NAME_MAP[$env_var_name])) return $this->failure("Unavailable environment variable to set : ".$env_var_name);

                if (strpos($env_var_value,',')!==false) {

                    $var_value_array = explode(',',$env_var_value);

                    $final_string = var_export($var_value_array,true);

                    $final_string = str_replace("'",'"',$final_string);

                } else {
                    $final_string = var_export($env_var_value,true);

                    $final_string = str_replace("'",'"',$final_string);
                }

                $env_var_delimiter = DStringUtils::getCommentDelimitedReplacementsStringSeparator($env_var_name);

    			$tokens = explode("/*!".$env_var_delimiter."!*/",$deployer_content);

    			$tokens[1] = $final_string;

    			$deployer_content = implode("/*!".$env_var_delimiter."!*/",$tokens);

    			$this->deployer_file->setContent($deployer_content);

    			//using a variable instead of a constant is necessary to be able to make unit test on it ... it's still ok :)
    			self::$$env_var_name = $env_var_value;

    			return ["result" => self::SUCCESS_RESULT];
    		} else return $this->failure("Wrong password.");
    	}

    	public function hello($password=null) {
    		if ($this->accessGranted($password)) {
    			return ["result" => self::SUCCESS_RESULT];
    		} else return $this->failure("Wrong password.");
    	}



    	private function accessGranted($password) {
    	   if (($this->hasPassword() && self::$PWD==$password) || (!$this->hasPassword() && !$password)) 
                return true;
    	   else {

                return false;
           }
            
    	}

    	public function failure(string $message) {
    		return ["result" => self::FAILURE_RESULT,"message" => $message];
    	}

    	private function hasPassword() {
    		return self::$PWD!=null;
    	}

        private function getRequestMethod() {
            if (isset($_SERVER['REQUEST_METHOD'])) return $_SERVER['REQUEST_METHOD'];
            else return 'CLI';
        }

        public function preparePostResponse($data) {
            if (is_array($data)) {
                try {
                    $final_result = json_encode($data);
                    return $final_result;
                } catch (\Exception $ex) {
                    return var_export($data);
                }
            }

            return "".$data;
        }

        private function sendFileFromResult($result) {
            if ($result['result']==self::SUCCESS_RESULT) {
                $f = $result['data'];

                if ($f->exists() && $f->getSize()>0) {

                    header('Content-Description: File Transfer');
                    header('Content-Type: '.mime_content_type ($f->getFullPath()));
                    
                    $content_disposition = 'inline';
                    
                    header('Content-Disposition: '.$content_disposition.'; filename="my_dir.zip"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . $f->getSize());
                    header('Connection: close');
                    flush(); // Flush system output buffer
                    readfile($f->getFullPath());
                    
                    exit();
                } else {

                    echo $this->preparePostResponse($this->failure("Unable to find zip file to send to client."));
                    exit();
                }
            }
        }

        private function getResultMessage($result) {
            if (is_array($result) && isset($result['message'])) return $result['message'];
            else return "Unknown error";
        }

        private function isSuccess($result) {
            return is_array($result) && $result['result']==self::SUCCESS_RESULT;
        }

        public function processRequest() {
        	if ($this->hasPostParameter('METHOD')) {
        		$method = $this->getPostParameter('METHOD');

        		switch ($method) {
                    case 'VERSION' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in VERSION request."));

                        echo $this->preparePostResponse($this->version($password));
                        break;
                    }
        			case 'HELLO' : {
    					if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in HELLO request."));

    					echo $this->preparePostResponse($this->hello($password));
        				break;
        			}
                    case 'FILE_EXISTS' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in FILE_EXISTS request."));

                        if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
                        else echo $this->preparePostResponse($this->failure("PATH field missing in FILE_EXISTS request."));

                        echo $this->preparePostResponse($this->fileExists($password,$path));

                        break;
                    }
                    case 'LIST_DB' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_DB request."));

                        echo $this->preparePostResponse($this->listDb($password));

                        break;
                    }
                    case 'BACKUP_DB_STRUCTURE' : {

                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in BACKUP_DB_STRUCTURE request."));

                        if ($this->hasPostParameter('CONNECTION_NAME')) $connection_name = $this->getPostParameter('CONNECTION_NAME');
                        else echo $this->preparePostResponse($this->failure("CONNECTION_NAME field missing in BACKUP_DB_STRUCTURE request."));

                        $result = $this->backupDbStructure($password,$connection_name);

                        if ($this->isSuccess($result)) {

                            $this->sendFileFromResult($result);
                        }
                        else {

                            echo $this->preparePostResponse($this->failure("Unable to correctly prepare file to send for backup db structure : ".$this->getResultMessage($result)));
                        }

                        break;
                    }
                    case 'BACKUP_DB_DATA' : {

                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in BACKUP_DB_DATA request."));

                        if ($this->hasPostParameter('CONNECTION_NAME')) $connection_name = $this->getPostParameter('CONNECTION_NAME');
                        else echo $this->preparePostResponse($this->failure("CONNECTION_NAME field missing in BACKUP_DB_DATA request."));

                        $result = $this->backupDbData($password,$connection_name);

                        if ($this->isSuccess($result)) {
                            $this->sendFileFromResult($result);
                        }
                        else {
                            echo $this->preparePostResponse($this->failure("Unable to correctly prepare file to send for backup db structure : ".$this->getResultMessage($result)));
                        }

                        break;
                    }
                    case 'MIGRATE_ALL' : {

                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_ALL request."));

                        echo $this->preparePostResponse($this->migrateAll($password));

                        break;
                    }
                    case 'MIGRATE_RESET' : {

                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_ALL request."));

                        echo $this->preparePostResponse($this->migrateReset($password));

                        break;
                    }
                    case 'MIGRATE_LIST_DONE' : {

                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_LIST_DONE request."));

                        echo $this->preparePostResponse($this->migrateListDone($password));

                        break;
                    }
                    case 'MIGRATE_LIST_MISSING' : {

                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MIGRATE_LIST_MISSING request."));

                        echo $this->preparePostResponse($this->migrateListMissing($password));

                        break;
                    }
                    case 'READ_FILE_CONTENT' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in READ_FILE_CONTENT request."));

                        if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
                        else echo $this->preparePostResponse($this->failure("PATH field missing in READ_FILE_CONTENT request."));

                        echo $this->preparePostResponse($this->readFileContent($password,$path));

                        break;
                    }
                    case 'WRITE_FILE_CONTENT' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in WRITE_FILE_CONTENT request."));

                        if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
                        else echo $this->preparePostResponse($this->failure("PATH field missing in WRITE_FILE_CONTENT request."));
                        
                        if ($this->hasPostParameter('CONTENT')) {
                            if ($this->accessGranted($password)) {
                                //content is unfiltered
                                $content = $_POST['CONTENT'];
                            } else {
                                if ($this->isPostParameterDangerous('CONTENT')) {
                                echo $this->preparePostResponse($this->failure('Warning!! CONTENT field with potential security issues and wrong password detected!!'));
                                return;
                                } else {
                                    echo $this->preparePostResponse($this->failure("Wrong password."));
                                    return;
                                }
                            }
                        }    
                        else {
                            echo $this->preparePostResponse($this->failure("CONTENT field missing in WRITE_FILE_CONTENT request."));
                            return;
                        }        

                        echo $this->preparePostResponse($this->writeFileContent($password,$path,$content));

                        break;
                    }
                    case 'SET_ENV' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in SET_ENV request."));

                        if ($this->hasPostParameter('ENV_VAR_NAME')) $env_var_name = $this->getPostParameter('ENV_VAR_NAME');
                        else echo $this->preparePostResponse($this->failure("ENV_VAR_NAME field missing in SET_ENV request."));

                        if ($this->hasPostParameter('ENV_VAR_VALUE')) $env_var_value = $this->getPostParameter('ENV_VAR_VALUE');
                        else echo $this->preparePostResponse($this->failure("ENV_VAR_VALUE field missing in SET_ENV request."));

                        echo $this->preparePostResponse($this->setEnv($password,$env_var_name,$env_var_value));

                        break;
                    }
        			case 'GET_ENV' : {
        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in GET_ENV request."));

    					if ($this->hasPostParameter('ENV_VAR_NAME')) $env_var_name = $this->getPostParameter('ENV_VAR_NAME');
    					else echo $this->preparePostResponse($this->failure("ENV_VAR_NAME field missing in GET_ENV request."));

    					echo $this->preparePostResponse($this->getEnv($password,$env_var_name));

    					break;
        			}
                    case 'LIST_ENV' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_ENV request."));

                        echo $this->preparePostResponse($this->listEnv($password));

                        break;
                    }
        			case 'LIST_ELEMENTS' : {

        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_ELEMENTS request."));

    					if (isset($_POST['FOLDER'])) $folder = $this->getPostParameter('FOLDER');
    					else echo $this->preparePostResponse($this->failure("FOLDER field missing in LIST_ELEMENTS request."));

    					echo $this->preparePostResponse($this->listElements($password,$folder));

        				break;
        			}
        			case 'LIST_HASHES' : {
        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in LIST_HASHES request."));

    					if ($this->hasPostParameter('EXCLUDED_PATHS')) $excluded_paths = explode(',',$this->getPostParameter('EXCLUDED_PATHS'));
    					else echo $this->preparePostResponse($this->failure("EXCLUDED_PATHS field missing in LIST_HASHES request."));

    					if ($this->hasPostParameter('INCLUDED_PATHS')) $included_paths = explode(',',$this->getPostParameter('INCLUDED_PATHS'));
    					else echo $this->preparePostResponse($this->failure("INCLUDED_PATHS field missing in LIST_HASHES request."));					

    					echo $this->preparePostResponse($this->listHashes($password,$excluded_paths,$included_paths));

    					break;
        			}
                    case 'FIX_PERMISSIONS' : {
                        if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
                        else echo $this->preparePostResponse($this->failure("PASSWORD field missing in FIX_PERMISSIONS request."));

                        if (isset($_POST['PERMISSIONS_TO_SET'])) $permissions_to_set = $this->getPostParameter('PERMISSIONS_TO_SET');
                        else echo $this->preparePostResponse($this->failure("PERMISSIONS_TO_SET field missing in FIX_PERMISSIONS request."));

                        if ($this->hasPostParameter('EXCLUDED_PATHS')) $excluded_paths = explode(',',$this->getPostParameter('EXCLUDED_PATHS'));
                        else echo $this->preparePostResponse($this->failure("EXCLUDED_PATHS field missing in FIX_PERMISSIONS request."));

                        if ($this->hasPostParameter('INCLUDED_PATHS')) $included_paths = explode(',',$this->getPostParameter('INCLUDED_PATHS'));
                        else echo $this->preparePostResponse($this->failure("INCLUDED_PATHS field missing in FIX_PERMISSIONS request."));                   

                        echo $this->preparePostResponse($this->fixPermissions($password,$permissions_to_set,$excluded_paths,$included_paths));

                        break;
                    }
        			case 'COPY_FILE' : {

        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in COPY_FILE request."));

    					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
    					else echo $this->preparePostResponse($this->failure("PATH field missing in COPY_FILE request."));

    					echo $this->preparePostResponse($this->copyFile($password,$path));

    					break;
        			}
        			case 'MAKE_DIR' : {
        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in MAKE_DIR request."));

    					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
    					else echo $this->preparePostResponse($this->failure("PATH field missing in MAKE_DIR request."));

    					echo $this->preparePostResponse($this->makeDir($password,$path));

    					break;
        			}
        			case 'DELETE_DIR' : {
        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in DELETE_DIR request."));

    					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
    					else echo $this->preparePostResponse($this->failure("PATH field missing in DELETE_DIR request."));

    					if ($this->hasPostParameter('RECURSIVE')) $recursive = $this->getPostParameter('RECURSIVE') == 'true' ? true : false;
    					else echo $this->preparePostResponse($this->failure("RECURSIVE field missing in DELETE_DIR request."));

    					echo $this->preparePostResponse($this->deleteDir($password,$path,$recursive));

    					break;
        			}
        			case 'DELETE_FILE' : {
        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in DELETE_FILE request."));

    					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
    					else echo $this->preparePostResponse($this->failure("PATH field missing in DELETE_FILE request."));

    					echo $this->preparePostResponse($this->deleteFile($password,$path));

    					break;
        			}
        			case 'DOWNLOAD_DIR' : {
        				if ($this->hasPostParameter('PASSWORD')) $password = $this->getPostParameter('PASSWORD');
    					else echo $this->preparePostResponse($this->failure("PASSWORD field missing in DOWNLOAD_DIR request."));

    					if ($this->hasPostParameter('PATH')) $path = $this->getPostParameter('PATH');
    					else echo $this->preparePostResponse($this->failure("PATH field missing in DOWNLOAD_DIR request."));

    					$result = $this->downloadDir($password,$path);

    					$this->sendFileFromResult($result);

    					break;
        			}
        			default : echo $this->preparePostResponse($this->failure("Unknown method : ".$method));
        		}
        	} else {
        		echo $this->preparePostResponse($this->failure("METHOD parameter not set in POST."));
        	}
        }
    }
}
//--launch

if (isset($_POST['METHOD'])) {

    try {
       $controller = new DeployerController();

	   $controller->processRequest();

    } catch (\Exception $ex) {
        echo $controller->preparePostResponse($controller->failure("Server got an exception : ".$ex->getMessage()));
    }
} else echo "Hello :)";