<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */


interface LIInspector {
	
	/**
	This method should return an array of the collected results.
	@param $dir An LDir instance to visit.

	*/
	public function visit($dir);


	/**
	Returns an array of excluded paths.
	*/
	public function getExcludedPaths();



	/**
	Returns an array of included paths.
	*/
	public function getIncludedPaths();


}