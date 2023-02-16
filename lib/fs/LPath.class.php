<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

class LPath {
	

	private static $aliases = [];

	private $original_path;
	private $converted_path;

	function __construct(string $path) {
		$this->original_path = $path;

		foreach (self::$aliases as $alias => $path_part) {
			$path = str_replace($alias,$path_part,$path);
		}

		$this->converted_path = $path;
	}

	private static $defaultAliasesInitialized = false;

	public static function initDefaultAliases() {
		self::$defaultAliasesInitialized = true;

		if (isset($_SERVER['FRAMEWORK_DIR'])) {
			self::addAlias('@framework_dir',$_SERVER['FRAMEWORK_DIR']);
		}

		if (isset($_SERVER['PROJECT_DIR'])) {
			self::addAlias('@project_dir',$_SERVER['PROJECT_DIR']);
		}
	}

	public static function addAlias(string $alias,string $path_part) {
		self::$aliases[$alias] = $path_part;
	}

	public function getRelativePath() {
		return $this->original_path;
	}

	public function getConvertedPath() {
		return $this->converted_path;
	}

	public function isFile() {
		return LFileSystemUtils::isFile($this->converted_path);
	}

	public function isDir() {
		return LFileSystemUtils::isDir($this->converted_path);
	}

	public function exists() {
		return $this->isFile() || $this->isDir();
	}

	public function __toString() {
		return "Path : [ ".$this->original_path." -> ".$this->converted_path." ]";
	}

	public function get() {
		if (!$this->exists()) throw new \Exception("Unable to get path element that does not exists!");

		if ($this->isFile()) return new LFile($this->converted_path);
		if ($this->isDir()) return new LDir($this->converted_path);
	}

}