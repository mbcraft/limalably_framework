<?php


class LProjectRunTestsFastCommand implements LICommand {
	

    public function execute() {
        LTestRunner::clear();
        LTestRunner::collect($_SERVER['PROJECT_DIR'], 'tests_fast/');
        LTestRunner::run();
        
    }

}