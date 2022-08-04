<?php

//change this path if needed, always end with slash, examples :
// same dir inside project : 'lymz_framework/'
// same level as project : '../lymz_framework/'
// absolute path : '/var/lib/lymz_framework/'

$lymz_framework_path = '../lymz_framework_repository/';

$version = "0.9.7";
// --------------- DO NOT CHANGE ANYTHING AFTER THIS LINE ----------------------------

// booting framework ...

$path_parts = explode('/',__FILE__);
array_pop($path_parts); //rimuovo l'ultimo elemento

$project_dir = implode('/',$path_parts).'/';
$_SERVER['PROJECT_DIR'] = $project_dir;

if (strpos($lymz_framework_path,'/')===0) {
    $framework_path = $lymz_framework_path;
} else {
    $framework_path = $_SERVER['PROJECT_DIR'].$lymz_framework_path;
}

$final_framework_path = realpath($framework_path).'/'.$version.'/';

if (!is_dir($final_framework_path) || !is_file($final_framework_path.'framework_boot.php')) {
    echo "Framework not found in path : ".$final_framework_path;
    exit(1);
} else {
    $_SERVER['FRAMEWORK_DIR'] = $final_framework_path;
    require_once($final_framework_path.'framework_boot.php');
}

try {
Lymz::project_boot();
} catch (\Exception $ex) {
    var_dump($ex);
}
