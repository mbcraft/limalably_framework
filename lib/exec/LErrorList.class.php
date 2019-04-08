<?php

class LErrorList {

    private static $data = [];

    public static function clear() {
        self::$data = [];
    }

    public static function saveFromException(string $type, \Exception $ex) {
        self::$data[$type][] = $ex;
        self::$data['all'][] = $ex;

        $log_errors = LConfigReader::executionMode('/error/log');

        if ($log_errors) {
            LLog::exception($ex);
        }

        $continue_execution_on_errors = LConfigReader::executionMode('/error/continue_on_errors');

        if (!$continue_execution_on_errors) {
            switch ($type) {
                case 'session' : {
                        if (empty($_SESSION))
                            throw new LHttpError(LHttpError::ERROR_UNAUTHORIZED);
                        else
                            throw new LHttpError(LHttpError::ERROR_FORBIDDEN);
                    }
                case 'input' : {
                        throw new LHttpError(LHttpError::ERROR_BAD_REQUEST);
                    }
                case 'conditions': {
                        throw new LHttpError(LHttpError::ERROR_METHOD_NOT_ALLOWED);
                    }

                default: throw new LHttpError(LHttpError::ERROR_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function saveFromErrors(string $type, $errors, $code = LSimpleError::SIMPLE_ERROR_CODE) {
        if (is_string($errors))
            $result_errors = [new LSimpleError($errors, $code)];
        else {
            $result_errors = [];
            foreach ($errors as $err)
                $result_errors[] = new LSimpleError($err, $code);
        }

        self::$data = array_merge_recursive(self::$data, array($type => $result_errors, 'all' => $result_errors));

        $log_errors = LConfigReader::executionMode('/error/log');

        if ($log_errors) {

            foreach ($result_errors as $err) {
                LLog::error($err->getMessage(), $err->getCode());
            }
        }

        $continue_execution_on_errors = LConfigReader::executionMode('/error/continue_on_errors');

        if (!$continue_execution_on_errors) {
            switch ($type) {
                case 'session' : {
                        if (empty($_SESSION))
                            throw new LHttpError(LHttpError::ERROR_UNAUTHORIZED);
                        else
                            throw new LHttpError(LHttpError::ERROR_FORBIDDEN);
                    }
                case 'input' : {
                        throw new LHttpError(LHttpError::ERROR_BAD_REQUEST);
                    }
                case 'conditions': {
                        throw new LHttpError(LHttpError::ERROR_METHOD_NOT_ALLOWED);
                    }

                default: throw new LHttpError(LHttpError::ERROR_INTERNAL_SERVER_ERROR);
            }
        }
    }

    public static function hasErrors() {
        return !empty(self::$data);
    }

    public static function mergeIntoTreeMap($treemap) {
        if (self::hasErrors()) {
            $treemap->set('/success', false);
            $treemap->set('/errors', self::$data);
        } else {
            $treemap->set('/success', true);
        }
    }

}
