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
    
    private function handleHashDbList() {
        $this->setCommandAsExecuted();
        
        $hash_db_utils = new LHashDbUtils();
        
        $elements = $hash_db_utils->listRoutes();
        
        foreach ($elements as $k => $el) {
            echo "$k : $el \n";
        }
    }
    
    private function handleHashDbAdd() {
        $this->setCommandAsExecuted();
        
        if (LParameters::count()!=2) {
            echo "Two parameters needed : the name of the public route and the name of the wanted route. \n";
            return;
        }
        
        $public_route = LParameters::getByIndex(0);
        $wanted_route = LParameters::getByIndex(1);
        
        $hash_db_utils = new LHashDbUtils();
        
        $result = $hash_db_utils->addRoute($public_route, $wanted_route);
        
        echo $result;
    }
    
    private function handleHashDbRemove() {
        $this->setCommandAsExecuted();
        
        if (LParameters::count()!=1) {
            echo "One index of the entry to remove is needed. Use list command to list available hash db routes.\n";
            return;
        }
                
        $index = LParameters::getByIndex(0);
        
        $hash_db_utils = new LHashDbUtils();
        
        $result = $hash_db_utils->removeRouteByIndex($index);
        
        echo $result;
    }
    
    public function tryExecuteCommand() {
        $route = $_SERVER['ROUTE'];
        switch ($route) {
            case 'internal/set_execution_mode' : $this->handleSetExecutionMode();break;
            case 'internal/get_execution_mode' : $this->handleGetExecutionMode();break;
            case 'internal/run_tests' : $this->handleRunTests();break;
            case 'internal/run_tests_fast' : $this->handleRunTestsFast();break;
            case 'internal/generate_data_objects' : $this->handleGenerateDataObjects();break;
            case 'internal/hash_db_list' : $this->handleHashDbList();break;
            case 'internal/hash_db_add' : $this->handleHashDbAdd();break;
            case 'internal/hash_db_remove' : $this->handleHashDbRemove();break;
        }
    }

    public function hasExecutedCommand() {
        return $this->command_executed;
    }

}