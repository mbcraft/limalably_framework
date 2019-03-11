<?php

class LOutput {

    private static function newline() {
        if ($_SERVER['ENVIRONMENT'] == 'script')
            echo "\n";
        else
            echo '<br>';
    }

    static function framework_debug($message) {
        if (!isset($_CONFIG['execution_mode']) || $_CONFIG['execution_mode'] == 'framework_debug') {
            echo $message;
            newline();
        }
    }

    static function debug($message) {
        if (!isset($_CONFIG['execution_mode']) || $_CONFIG['execution_mode'] != 'production') {
            echo $message;
            newline();
        }
    }

    static function output($message) {
        echo $message;
        newline();
    }

}
