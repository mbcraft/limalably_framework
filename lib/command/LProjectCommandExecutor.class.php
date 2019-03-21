<?php

class LProjectCommandExecutor implements LICommandExecutor {
   
    private $command_executed = false;
    
    private function setCommandAsExecuted() {
        $this->command_executed = true;
    }
    
    private function handleSetExecutionMode() {
        $this->setCommandAsExecuted();
        if (!isset($_SERVER['argv'][2])) {
            LOutput::error_message("Mode name not set. Choose between 'maintenance','framework_debug','debug','testing' or 'production'.");
            return;
        }
        $mode_name = $_SERVER['argv'][2];
        try {
            LExecutionMode::setByName($mode_name);
            LOutput::message("Execution mode set to '".$mode_name."' successfully.");
        } catch (\Exception $ex) {
            LOutput::exception($ex);
        }
        
    }
    
    private function handleGetExecutionMode() {
        $this->setCommandAsExecuted();
        LOutput::message("Execution mode is now '".LExecutionMode::get()."'.");
    }
    
    private function handleRunTests() {
        $this->setCommandAsExecuted();
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests/');
        LTestRunner::run();
        
    }
    
    private function handleRunTestsFast() {
        $this->setCommandAsExecuted();
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests_fast/');
        LTestRunner::run();
        
    }
    
    public function tryExecuteCommand() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/set_execution_mode' : $this->handleSetExecutionMode();break;
            case 'internal/get_execution_mode' : $this->handleGetExecutionMode();break;
            case 'internal/run_tests' : $this->handleRunTests();break;
            case 'internal/run_tests_fast' : $this->handleRunTestsFast();break;
        }
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}