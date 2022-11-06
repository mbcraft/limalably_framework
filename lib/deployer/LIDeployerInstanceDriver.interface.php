<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LIDeployerInstanceDriver {
	
	const SUCCESS_RESULT = ':)';
	const FAILURE_RESULT = ':(';
	
	public function version($password);

	public function listElements($password,$folder);

	public function listHashes($password,$excluded_paths,$included_paths);

	public function deleteFile($password,$path);

	public function makeDir($password,$path);

	public function deleteDir($password,$path,$recursive);

	public function copyFile($password,$path,$source_file);

	public function downloadDir($password,$path,$save_file);

	public function listEnv($password);

	public function getEnv($password,$env_var_name);

	public function setEnv($password,$env_var_name,$env_var_value);

	public function hello($password=null);

}