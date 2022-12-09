<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LProjectCommandExecutor implements LICommandExecutor {
    
    private $command_executed = false;
    
    private function setCommandAsExecuted() {
        $this->command_executed = true;
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

    public function tryExecuteCommand() {

        $route = $_SERVER['ROUTE'];
        $cmd = null;
        switch ($route) {
            case 'project/set_execution_mode' : $cmd = new LProjectSetExecutionModeCommand();break;
            case 'project/get_execution_mode' : $cmd = new LProjectGetExecutionModeCommand();break;
            case 'project/run_tests' : $cmd = new LProjectRunTestsCommand();break;
            case 'project/run_tests_fast' : $cmd = new LProjectRunTestsFastCommand();break;
            case 'project/generate_data_objects' : $cmd = new LProjectGenerateDataObjectsCommand();break;
            case 'project/url_alias_db_list' : $cmd = new LProjectUrlAliasDbListCommand();break;
            case 'project/url_alias_db_add' : $cmd = new LProjectUrlAliasDbAddCommand();break;
            case 'project/url_alias_db_remove' : $cmd = new LProjectUrlAliasDbRemoveCommand();break;
            case 'project/deployer' : $cmd = new LProjectDeployerCommand();break;
            case 'project/migrate' : $cmd = new LProjectMigrateCommand();break;
            case 'project/db_tool' : $cmd = new LProjectDbToolCommand();break;
        }

        if ($cmd) {
            $this->setCommandAsExecuted();
            $cmd->execute();
        }
        
        if ($this->hasExecutedCommand()) Lymz::finish ();
    }

}