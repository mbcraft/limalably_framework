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

                $path = $starting_dir.LParameters::getByIndex(0);

                echo "Running only one unit test : ".$path;

            } else {
             
                $path = $starting_dir.LParameters::getByIndex(0).'/';

                echo "Running only tests in subfolder '".$path."' ...\n";    
            
            }
            
        } else {
            echo "Executing all unit tests ...\n";

            $path = $starting_dir;
        }

        LTestRunner::collect($_SERVER['FRAMEWORK_DIR'], $path);
        LTestRunner::run();
        
    }
}