<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LRemoteDeployerInstanceDriver implements LIDeployerInstanceDriver {

	function __construct($full_deployer_url) {

		$this->full_deployer_url = $full_deployer_url;

	}

	private function asResult($data) {
		
		return json_decode($data,true);

	}

	public function version($password) {

		$params = [];
		$params['METHOD'] = 'VERSION';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function listElements($password,$folder) {

		$params = [];
		$params['METHOD'] = 'LIST_ELEMENTS';
		$params['PASSWORD'] = $password;
		$params['FOLDER'] = $folder;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function listHashes($password,$excluded_paths,$included_paths) {

		$params = [];
		$params['METHOD'] = 'LIST_HASHES';
		$params['PASSWORD'] = $password;
		$params['EXCLUDED_PATHS'] = implode(',',$excluded_paths);
		$params['INCLUDED_PATHS'] = implode(',',$included_paths);

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function deleteFile($password,$path) {

		$params = [];
		$params['METHOD'] = 'DELETE_FILE';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function makeDir($password,$path) {

		$params = [];
		$params['METHOD'] = 'MAKE_DIR';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function deleteDir($password,$path,$recursive) {

		$params = [];
		$params['METHOD'] = 'DELETE_DIR';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;
		$params['RECURSIVE'] = $recursive ? 'true' : 'false';

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);

	}

	public function copyFile($password,$path,$source_file) {

		if (!$source_file instanceof LFile) throw new \Exception("source_file is actually not an LFile instance.");

		$params = [];
		$params['METHOD'] = 'COPY_FILE';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;
		$params['f'] = $source_file;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}

	public function downloadDir($password,$path,$save_file) {

		$params = [];
		$params['METHOD'] = 'DOWNLOAD_DIR';
		$params['PASSWORD'] = $password;
		$params['PATH'] = $path;

		LHttp::post_to_file($this->full_deployer_url,$params,$save_file);

	}

	public function changePassword($old_password,$new_password) {

		$params = [];
		$params['METHOD'] = 'CHANGE_PASSWORD';
		$params['PASSWORD'] = $old_password;
		$params['NEW_PASSWORD'] = $new_password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}
	
	public function hello($password=null) {

		$params = [];
		$params['METHOD'] = 'HELLO';
		$params['PASSWORD'] = $password;

		$result = LHttp::post($this->full_deployer_url,$params);

		return $this->asResult($result);
	}


}