<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

trait LAssetResourceManagerTrait {
	

	static $assets_to_include = [];


	private static function checkAssetPath($asset_path) {

		$wwwroot_folder = LConfigReader::simple('/misc/wwwroot');

		$wwwroot_folder = str_replace('/','',$wwwroot_folder);

		$complete_path = $wwwroot_folder.$asset_path;

		$f = new LFile($complete_path);

		if (!$f->exists()) throw new \Exception("Asset file not found : ".$complete_path);

	}

	public static function require($asset_name,$asset_path,$version='unknown') {

		self::checkAssetPath($asset_path);

		$params = [
			'path' => $asset_path,
			'version' = $version
		];

		$current_asset = isset($assets_to_include[$asset_name]) ? $assets_to_include[$asset_name] : null;

		if (!$current_asset) {
			self::$assets_to_include[$asset_name] = $params;
			return;
		} else {
			if ($current_asset['version']=='unknown') {
				self::$assets_to_include[$asset_name] = $params;
				return;
			}
			if ($current_asset['version']!='unknown' && $params['version']=='unknown') return;

			if (version_compare($current_asset['version'],$params['version'])>0) {
				self::$assets_to_include[$asset_name] = $params;
				return;
			}
		}

	}

	public static function getTagList() {

		$result = [];

		foreach (self::$assets_to_include as $name => $spec) {

			$result []= self::renderTag($spec);
		}

		return $result;

	}


}