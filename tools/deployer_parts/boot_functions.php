<?php

if (!defined('FRAMEWORK_NAME')) define ('FRAMEWORK_NAME','limalably');
if (!defined('FRAMEWORK_DIR_NAME')) define ('FRAMEWORK_DIR_NAME','limalably_framework');

if (!function_exists('array_remove_key_or_value')) {
    function array_remove_key_or_value(array $data,$to_remove) {

        if ($data===null) return null;

        $result = [];

        foreach ($data as $key => $value) {
            if ($key!==$to_remove && $value!==$to_remove) {
                $result[$key] = $value;
            } 
        }

        return $result;
    }
}

function limalably_deployer_fatal_handler() {

    if (isset($_SERVER['EXIT'])) {
        exit();
    } else {

        $errfile = "unknown file";
        $errstr = "shutdown";
        $errno = E_CORE_ERROR;
        $errline = 0;

        $error = error_get_last();

        if ($error !== NULL) {
            $errno = $error["type"];
            $errfile = $error["file"];
            $errline = $error["line"];
            $errstr = $error["message"];

            limalably_deployer_report($errno, $errstr, $errfile, $errline);
        }
    }
}

function limalably_deployer_report(int $errno, string $errstr, string $errfile, int $errline, array $errcontext=[]) {

    $available_constants = [E_COMPILE_ERROR, E_COMPILE_WARNING, E_CORE_ERROR, E_CORE_WARNING, E_ERROR, E_PARSE, E_NOTICE, E_WARNING, E_RECOVERABLE_ERROR, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE];

    foreach ($available_constants as $constant) {
        if (($errno & $constant) == $constant)
            break;
    }

    $warning = false;

    switch ($constant) {
        case E_COMPILE_ERROR : $type = 'compile';
            break;
        case E_COMPILE_WARNING : $type = 'compile';
            $warning = true;
            break;
        case E_CORE_ERROR : $type = 'core';
            break;
        case E_CORE_WARNING : $type = 'core';
            $warning = true;
            break;
        case E_ERROR : $type = 'error';
            break;
        case E_PARSE : $type = 'parse';
            break;
        case E_NOTICE: $type = 'notice';
            $warning = true;
            break;
        case E_WARNING: $type = 'warning';
            $warning = true;
            break;
        case E_RECOVERABLE_ERROR : $type = 'recoverable_error';
            break;
        case E_USER_ERROR : $type = 'user_error';
            break;
        case E_USER_WARNING : $type = 'user_warning';
            $warning = true;
            break;
        case E_USER_NOTICE : $type = 'user_notice';
            $warning = true;
            break;
        default : $type = 'unknown_error_type';
            break;
    }

    $msg = "Error type: " . $type . " - ";
    $msg .= "Error : " . $errstr . " - ";
    $msg .= "File : " . $errfile . " - ";
    $msg .= "Line number : " . $errline;

    echo json_encode(['result' => DeployerController::FAILURE_RESULT,'message' => $msg]);

    exit(0);
}

set_error_handler('limalably_deployer_report', E_ERROR | E_WARNING | E_PARSE | E_NOTICE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING | E_RECOVERABLE_ERROR | E_USER_ERROR | E_USER_WARNING | E_USER_NOTICE);
