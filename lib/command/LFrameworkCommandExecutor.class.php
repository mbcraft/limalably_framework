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
    
    public function tryExecuteCommand() {
        $route = $_SERVER['ROUTE'];
        $cmd = null;
        switch ($route) {
            case 'framework/run_tests' : $cmd = new LFrameworkRunTestsCommand();break;
            case 'framework/run_tests_fast' : $cmd = new LFrameworkRunTestsFastCommand();break;
        }

        if ($cmd) {
            $this->setCommandAsExecuted();
            $cmd->execute();
        }
        
        if ($this->hasExecutedCommand()) Limalably::finish ();
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}
