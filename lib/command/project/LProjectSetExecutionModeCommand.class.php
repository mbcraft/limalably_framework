<?php




class LProjectSetExecutionModeCommand implements LICommand {
	

    public function execute() {
        if (!isset($_SERVER['argv'][2])) {
            LResult::error_message("Mode name not set. Choose between 'maintenance','framework_development','development','testing' or 'production'.");
            return;
        }
        $mode_name = $_SERVER['argv'][2];
        try {
            LExecutionMode::setByName($mode_name);
            LResult::messagenl("Execution mode set to '".$mode_name."' successfully.");
        } catch (\Exception $ex) {
            LResult::exception($ex);
        }
        
    }


}