<?php

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
    
    public function tryExecuteCommand() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/run_framework_tests' : $this->handleRunFrameworkTests();break;
        }
        
        if ($this->hasExecutedCommand()) Lymlym::finish ();
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}
