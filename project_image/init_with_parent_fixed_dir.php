<?php

/**
* @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it 
*
*
*/

//change this path if needed, always end with slash, examples :
// same dir inside project : 'limalably_framework/'
// same level as project : '../limalably_framework/'
// absolute path : '/var/lib/limalably_framework/'

$limalably_framework_path = '../limalably_framework/';

// --------------- DO NOT CHANGE ANYTHING AFTER THIS LINE ----------------------------

// booting framework ...

$path_parts = explode('/',__FILE__);
array_pop($path_parts); //rimuovo l'ultimo elemento

$project_dir = implode('/',$path_parts).'/';
$_SERVER['PROJECT_DIR'] = $project_dir;

if (strpos($limalably_framework_path,'/')===0) {
    $framework_path = $limalably_framework_path;
} else {
    $framework_path = $_SERVER['PROJECT_DIR'].$limalably_framework_path;
}

$final_framework_path = $framework_path.'/';

if (!is_dir($final_framework_path) || !is_file($final_framework_path.'framework_boot.php')) {
    echo "Framework not found in path : ".$final_framework_path;
    exit(1);
} else {
    require_once($final_framework_path.'framework_spec.php');

    require_once($final_framework_path.'framework_boot.php');
}

try {
Limalably::project_boot();
} catch (\Exception $ex) {
    var_dump($ex);
}
