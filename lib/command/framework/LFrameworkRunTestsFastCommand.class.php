<?php



class LFrameworkRunTestsFastCommand implements LICommand {
	

	public function execute() {
        
        LTestRunner::clear();

        $starting_dir = 'tests/';

        if (LParameters::count()==1) {

            $subfolder = LParameters::getByIndex(0);

            echo "Running only tests in subfolder '".$subfolder."' ...\n";

            $starting_dir .= LParameters::getByIndex(0).'/';
        } else {
            echo "Executing all tests_fast subfolders unit tests ...\n";
        }

        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], $starting_dir);

        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], 'tests_fast/');
        LTestRunner::run();
        
    }


}