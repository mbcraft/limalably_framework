<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

// booting framework ONLY ...

$uniform_file_path = str_replace('\\','/',__FILE__);
$path_parts = explode('/',$uniform_file_path);
array_pop($path_parts); //rimuovo l'ultimo elemento

$framework_dir = implode('/',$path_parts).'/';
$_SERVER['FRAMEWORK_DIR'] = $framework_dir;

require_once('framework_boot.php');

Lymz::framework_boot();
