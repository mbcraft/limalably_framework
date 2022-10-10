<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LFrameworkCommandExecutor implements LICommandExecutor {
    
    private $command_executed = false;
    
    private function setCommandAsExecuted() {
        $this->command_executed = true;
    }
    
    private function handleRunFrameworkTests() {
        $this->setCommandAsExecuted();
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], 'tests/');
        LTestRunner::run();
        
    }

    private function handleRunFrameworkTestsFast() {
        $this->setCommandAsExecuted();
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], 'tests_fast/');
        LTestRunner::run();
        
    }
    
    public function tryExecuteCommand() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/run_framework_tests' : $this->handleRunFrameworkTests();break;
            case 'internal/run_framework_tests_fast' : $this->handleRunFrameworkTestsFast();break;
        }
        
        if ($this->hasExecutedCommand()) Lymz::finish ();
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}
