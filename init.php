<?php

// --------------- DO NOT CHANGE ANYTHING AFTER THIS LINE ----------------------------

// booting framework ONLY ...

$path_parts = explode('/',__FILE__);
array_pop($path_parts); //rimuovo l'ultimo elemento

$framework_dir = implode('/',$path_parts).'/';
$_SERVER['FRAMEWORK_DIR'] = $framework_dir;

require_once('framework_boot.php');

Lym::framework_boot();
