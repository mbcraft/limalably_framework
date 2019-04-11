<?php

class LErrorReportingInterceptors {

    function report(int $errno, string $errstr, string $errfile, int $errline, array $errcontext): bool {
        
        $available_constants = [ E_COMPILE_ERROR,E_COMPILE_WARNING,E_CORE_ERROR,E_CORE_WARNING,E_ERROR,E_PARSE,E_NOTICE,E_WARNING,E_RECOVERABLE_ERROR,E_USER_ERROR,E_USER_WARNING,E_USER_NOTICE]; 
        
        foreach ($available_constants as $constant) {
            if (($errno & $constant)==$constant) break;
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
        try {
            if ($warning) {
                LWarningList::saveFromWarnings($type, $errstr . " on file " . $errfile . " on line " . $errline);
            } else {
                LErrorList::saveFromErrors($type, $errstr . " on file " . $errfile . " on line " . $errline);
            }
        } catch (\Exception $ex) {
            //error is not thrown ...
        }
        return false;
    }

    function register() {

        $report_mask = 0;
        if (LConfigReader::simple('/error/reporting/error', false))
            $report_mask |= E_ERROR;
        if (LConfigReader::simple('/error/reporting/warning', false))
            $report_mask |= E_WARNING;
        if (LConfigReader::simple('/error/reporting/parse', false))
            $report_mask |= E_PARSE;
        if (LConfigReader::simple('/error/reporting/notice', false))
            $report_mask |= E_NOTICE;
        if (LConfigReader::simple('/error/reporting/core_error', false))
            $report_mask |= E_CORE_ERROR;
        if (LConfigReader::simple('/error/reporting/core_warning', false))
            $report_mask |= E_CORE_WARNING;
        if (LConfigReader::simple('/error/reporting/compile_error', false))
            $report_mask |= E_COMPILE_ERROR;
        if (LConfigReader::simple('/error/reporting/compile_warning', false))
            $report_mask |= E_COMPILE_WARNING;
        if (LConfigReader::simple('/error/reporting/recoverable_error', false))
            $report_mask |= E_RECOVERABLE_ERROR;
        if (LConfigReader::simple('/error/reporting/user_error', false))
            $report_mask |= E_USER_ERROR;
        if (LConfigReader::simple('/error/reporting/user_warning', false))
            $report_mask |= E_USER_WARNING;
        if (LConfigReader::simple('/error/reporting/user_notice', false))
            $report_mask |= E_USER_NOTICE;

        set_error_handler(array($this, 'report'), $report_mask);
    }

}
