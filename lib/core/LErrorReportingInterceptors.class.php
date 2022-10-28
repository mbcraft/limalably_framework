<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

function lymz_fatal_handler() {

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

            lymz_report($errno, $errstr, $errfile, $errline, []);
        }
    }
}

function lymz_report(int $errno, string $errstr, string $errfile, int $errline, array $errcontext=[]) {

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

    echo "Error type: " . $type . "\n<br>";
    echo "Error : " . $errstr . "\n<br>";
    echo "File : " . $errfile . "\n<br>";
    echo "Line number : " . $errline . "\n<br>";

    return false;
}

function shutdown_function() {
    
}

class LErrorReportingInterceptors {

    function register() {

        $report_mask = 0;
        if (LConfigReader::simple('/misc/errors/reporting/error', false))
            $report_mask |= E_ERROR;
        if (LConfigReader::simple('/misc/errors/reporting/warning', false))
            $report_mask |= E_WARNING;
        if (LConfigReader::simple('/misc/errors/reporting/parse', false))
            $report_mask |= E_PARSE;
        if (LConfigReader::simple('/misc/errors/reporting/notice', false))
            $report_mask |= E_NOTICE;
        if (LConfigReader::simple('/misc/errors/reporting/core_error', false))
            $report_mask |= E_CORE_ERROR;
        if (LConfigReader::simple('/misc/errors/reporting/core_warning', false))
            $report_mask |= E_CORE_WARNING;
        if (LConfigReader::simple('/misc/errors/reporting/compile_error', false))
            $report_mask |= E_COMPILE_ERROR;
        if (LConfigReader::simple('/misc/errors/reporting/compile_warning', false))
            $report_mask |= E_COMPILE_WARNING;
        if (LConfigReader::simple('/misc/errors/reporting/recoverable_error', false))
            $report_mask |= E_RECOVERABLE_ERROR;
        if (LConfigReader::simple('/misc/errors/reporting/user_error', false))
            $report_mask |= E_USER_ERROR;
        if (LConfigReader::simple('/misc/errors/reporting/user_warning', false))
            $report_mask |= E_USER_WARNING;
        if (LConfigReader::simple('/misc/errors/reporting/user_notice', false))
            $report_mask |= E_USER_NOTICE;

        set_error_handler('lymz_report', $report_mask);

        register_shutdown_function('lymz_fatal_handler');

        error_reporting($report_mask);
    }

}
