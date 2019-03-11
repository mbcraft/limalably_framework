<?php

class LOutput {

    private static function newline() {
        if ($_SERVER['ENVIRONMENT'] == 'script')
            echo "\n";
        else
            echo '<br>';
    }

    static function framework_debug($message) {
        if (LExecutionMode::isFrameworkDebug()) {
            echo $message;
            self::newline();
        }
    }

    static function debug($message) {
        if (LExecutionMode::isDebug()) {
            echo $message;
            self::newline();
        }
    }

    static function output($message) {
        echo $message;
        self::newline();
    }

}
