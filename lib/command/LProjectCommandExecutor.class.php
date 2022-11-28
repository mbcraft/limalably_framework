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
        switch ($route) {
            case 'project/set_execution_mode' : new LProjectSetExecutionModeCommand();break;
            case 'project/get_execution_mode' : new LProjectGetExecutionModeCommand();break;
            case 'project/run_tests' : new LProjectRunTestsCommand();break;
            case 'project/run_tests_fast' : new LProjectRunTestsFastCommand();break;
            case 'project/generate_data_objects' : new LProjectGenerateDataObjectsCommand();break;
            case 'project/url_alias_db_list' : new LProjectUrlAliasDbListCommand();break;
            case 'project/url_alias_db_add' : new LProjectUrlAliasDbAddCommand();break;
            case 'project/url_alias_db_remove' : new LProjectUrlAliasDbRemoveCommand();break;
            case 'project/deployer' : $cmd = new LProjectDeployerCommand();break;
            case 'project/migrate' : $cmd = new LProjectMigrateCommand();break;
        }

        $this->setCommandAsExecuted();
        $cmd->execute();
        
        if ($this->hasExecutedCommand()) Lymz::finish ();
    }

}