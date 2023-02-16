<?php



class LProjectRunTestsCommand implements LICommand {
	
    public function execute() {
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests/');
        LTestRunner::run();
        
    }

}