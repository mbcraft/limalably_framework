<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LProjectGetExecutionModeCommand implements LICommand {
	
    private function execute() {
        LResult::messagenl("Execution mode is now '".LExecutionMode::get()."'.");
    }
}