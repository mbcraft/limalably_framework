<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

interface LIDeployerInstanceDriver {
	

	public function listHashes($password,$excluded_paths);

	public function deleteFile($password,$path);

	public function makeDir($password,$path);

	public function deleteDir($password,$path,$recursive);

	public function copyFile($password,$path,$source_file);

	public function downloadDir($password,$path,$save_file);

	public function changePassword($old_password,$new_password);

	public function hello($password=null);

}