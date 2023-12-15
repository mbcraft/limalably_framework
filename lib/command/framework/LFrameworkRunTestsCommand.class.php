<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LFrameworkRunTestsCommand implements LICommand {
	


    public function execute() {
        
        LTestRunner::clear();

        $starting_dir = 'tests/';

        if (LParameters::count()==1) {

            $param = LParameters::getByIndex(0);

            if (LStringUtils::endsWith($param,'.php')) {

                echo "Running only one unit test ...\n";

                $path = $starting_dir.LParameters::getByIndex(0);

            } else {
             
                echo "Running only tests in subfolder '".$subfolder."' ...\n";

                $path = $starting_dir.LParameters::getByIndex(0).'/';    
            
            }
            
        } else {
            echo "Executing all tests subfolders unit tests ...\n";
        }

        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], $path);
        LTestRunner::run();
        
    }
}