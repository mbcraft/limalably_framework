<?php

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
            Lymlym::finish(0);
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
        }
        
        if ($this->hasExecutedCommand()) Lymlym::finish ();
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}