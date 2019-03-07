<?php

class Lym {
    
    static function start() {
        $route = $_SERVER['ROUTE'];
        if ($route=='internal/test') {
            LTestRunner::clear();
            LTestRunner::collect('test/');
            LTestRunner::run();
            exit(0);
        }
        if ($route=='internal/test_fast') {
            LTestRunner::clear();
            LTestRunner::collect('test_fast/');
            LTestRunner::run();
            exit(0);
        }
    }
    
}