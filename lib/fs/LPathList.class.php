<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class LPathList {
	

	private $matches = [];

	private $path_list = [];

	public function addPath($path) {
		if ($path instanceof LPath) {
			$this->path_list[] = $path;
			return;
		} 
		if (is_string($path)) {
			$this->path_list[] = new LPath($path);
			return;
		}

		throw new \Exception("Unrecognized path format : only LPath and strings are allowed.");

	}

	public function getMatches() {
		return $this->matches;
	}

	public function hasMultipleMatches() {
		return count($this->matches)>1;
	}

	public function find() {

		foreach ($this->path_list as $path) {
			if ($path->exists()) {
				$this->matches[] = $path;
			}
		}

		if (count($this->matches)>0) {
			return $this->matches[0];
		} 

		return false;

	}

}