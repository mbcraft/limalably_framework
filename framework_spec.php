<?php

if (!defined('FRAMEWORK_NAME')) define ('FRAMEWORK_NAME','limalably');
if (!defined('FRAMEWORK_DIR_NAME')) define ('FRAMEWORK_DIR_NAME','limalably_framework');

if (!isset($_SERVER['FRAMEWORK_DIR'])) {
	$uniform_file_path = str_replace('\\','/',__FILE__);
	$path_parts = explode('/',$uniform_file_path);
	array_pop($path_parts); //rimuovo l'ultimo elemento

	$framework_dir = implode('/',$path_parts).'/';
	$_SERVER['FRAMEWORK_DIR'] = $framework_dir;
}