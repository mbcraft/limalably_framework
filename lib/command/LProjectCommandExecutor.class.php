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
    
    private function handleSetExecutionMode() {
        $this->setCommandAsExecuted();
        if (!isset($_SERVER['argv'][2])) {
            LResult::error_message("Mode name not set. Choose between 'maintenance','framework_development','development','testing' or 'production'.");
            return;
        }
        $mode_name = $_SERVER['argv'][2];
        try {
            LExecutionMode::setByName($mode_name);
            LResult::message("Execution mode set to '".$mode_name."' successfully.");
        } catch (\Exception $ex) {
            LResult::exception($ex);
        }
        
    }
    
    private function handleGetExecutionMode() {
        $this->setCommandAsExecuted();
        LResult::message("Execution mode is now '".LExecutionMode::get()."'.");
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
    
    private function handleGenerateDataObjects() {
        $this->setCommandAsExecuted();
        
        if (isset($_SERVER['argv'][2])) {
            $connection_name = $_SERVER['argv'][2];
        } else {
            $connection_name = 'default';
        }
        
        LAtlasSkeletonGenerator::generate($connection_name);
        
    }
    
    private function handleUrlAliasDbList() {
        $this->setCommandAsExecuted();
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $elements = $url_alias_db_utils->listRoutes();
        
        if (empty($elements)) {
            echo "No routes found in url alias db.\n";
            Lymz::finish(0);
        }
        
        echo "Routes found in url alias db : ".count($elements)."\n\n";
        
        foreach ($elements as $k => $el) {
            echo "$k : $el \n";
        }
    }
    
    private function handleUrlAliasDbAdd() {
        $this->setCommandAsExecuted();
        
        if (LParameters::count()!=2) {
            echo "Two parameters needed : the name of the public route and the name of the wanted route. \n";
            return;
        }
        
        $public_route = LParameters::getByIndex(0);
        $wanted_route = LParameters::getByIndex(1);
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $result = $url_alias_db_utils->addRoute($public_route, $wanted_route);
        
        echo $result."\n";
    }
    
    private function handleUrlAliasDbRemove() {
        $this->setCommandAsExecuted();
        
        if (LParameters::count()!=1) {
            echo "One index of the entry to remove is needed. Use list command to list available url alias db routes.\n";
            return;
        }
                
        $index = LParameters::getByIndex(0);
        
        $url_alias_db_utils = new LUrlAliasDbUtils();
        
        $result = $url_alias_db_utils->removeRouteByIndex($index);
        
        echo $result."\n";
    }

    private function handleDeployer() {
        $this->setCommandAsExecuted();

        $dc = new LDeployerClient();

        $parameter_map = [
            'help' => 1,
            'attach' => 3,
            'detach' => 2,
            'set_deployer_path_from_root' => 3,
            'get_deployer_path_from_root' => 2,
            'deployer_version' => 2, 
            'deployer_update' => 2,
            'framework_check' => 2,
            'framework_update' => 2,
            'project_check' => 2,
            'project_update' => 2,
            'auto_config' => 2,
            'manual_config' => 3,
            'backup' => 3,
            'disappear' => 2,
            'reset' => 2,
            'temp_clean' => 2,
            'get_exec_mode' => 2,
            'set_exec_mode' => 3
        ];

        if (LParameters::count()<1) {
            $dc->help();
            return;
        }

        if (LParameters::count()==1) {
            if (LParameters::getByIndex(0)=='help') {
                $dc->help();
                return;
            } else {
                echo "Unknown command '".LParameters::getByIndex(0)."'.\n";
                $dc->help();
                return;
            }
        }

        $command = LParameters::getByIndex(0);

        if (!isset($parameter_map[$command])) {
            echo "Unknown command.\n";
            $dc->help();
            return;
        }

        if (isset($parameter_map[$command]) && $parameter_map[$command]!=LParameters::count()) {
            echo "Parameter number mismatch.\n";
            $dc->help();
            return;
        }

        $deploy_key_name = LParameters::getByIndex(1);

        if (LParameters::count()>2) {
            $path_host_uri_or_exec_mode = LParameters::getByIndex(2);
        }

        switch ($command) {
            case 'help' : $dc->help();break;
            case 'attach': $dc->attach($deploy_key_name,$path_host_uri_or_exec_mode);break;
            case 'detach' : $dc->detach($deploy_key_name);break;
            case 'get_deployer_path_from_root' : $dc->get_deployer_path_from_root($deploy_key_name);break;
            case 'set_deployer_path_from_root' : $dc->set_deployer_path_from_root($deploy_key_name,$path_host_uri_or_exec_mode);break;
            case 'deployer_version' : $dc->deployer_version($deploy_key_name);break;
            case 'deployer_update' : $dc->deployer_update($deploy_key_name);break;
            case 'framework_check' : $dc->framework_check($deploy_key_name);break;
            case 'framework_update' : $dc->framework_update($deploy_key_name);break;
            case 'project_check' : $dc->project_check($deploy_key_name);break;
            case 'project_update' : $dc->project_update($deploy_key_name);break;
            case 'auto_config' : $dc->auto_config($deploy_key_name);break;
            case 'manual_config' : $dc->manual_config($deploy_key_name,$path_host_uri_or_exec_mode);break;
            case 'backup' : $dc->backup($deploy_key_name,$path_host_uri_or_exec_mode);break;
            case 'disappear' : $dc->disappear($deploy_key_name);break;
            case 'reset' : $dc->reset($deploy_key_name);break;
            case 'temp_clean': $dc->temp_clean($deploy_key_name);break;
            case 'get_exec_mode': $dc->get_exec_mode($deploy_key_name);break;
            case 'set_exec_mode': $dc->set_exec_mode($deploy_key_name,$path_host_uri_or_exec_mode);break;

            default : throw new \Exception("Command handler not implemented : ".$command);

        }

        return;
    }
    
    public function tryExecuteCommand() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/set_execution_mode' : $this->handleSetExecutionMode();break;
            case 'internal/get_execution_mode' : $this->handleGetExecutionMode();break;
            case 'internal/run_tests' : $this->handleRunTests();break;
            case 'internal/run_tests_fast' : $this->handleRunTestsFast();break;
            case 'internal/generate_data_objects' : $this->handleGenerateDataObjects();break;
            case 'internal/url_alias_db_list' : $this->handleUrlAliasDbList();break;
            case 'internal/url_alias_db_add' : $this->handleUrlAliasDbAdd();break;
            case 'internal/url_alias_db_remove' : $this->handleUrlAliasDbRemove();break;
            case 'internal/deployer' : $this->handleDeployer();break;
        }
        
        if ($this->hasExecutedCommand()) Lymz::finish ();
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}