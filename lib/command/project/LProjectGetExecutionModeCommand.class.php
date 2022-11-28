<?php



class LProjectGetExecutionModeCommand implements LICommand {
	
    private function execute() {
        LResult::messagenl("Execution mode is now '".LExecutionMode::get()."'.");
    }
}