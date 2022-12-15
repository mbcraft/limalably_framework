<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

class LSetupInProgressRouteExecutor implements LICommandExecutor {

    private $executed = false;

    public function hasExecutedCommand() {
        return $this->executed;
    }

    public function tryExecuteCommand() {
        LResult::trace("Executing maintenance route executor ...");
        $this->executed = true;

        if (LEnvironmentUtils::getEnvironment() == 'script') {
            
            echo "Unable to find valid running mode.\n";
            Lymz::finish(1);
            
        } else {
            
            $error_format = LConfigReader::simple("/format/default_error_format");

            $maintenance = new LHttpError(LHttpError::ERROR_IM_A_TEAPOT);

            $maintenance->execute($error_format);

        }
    }

}
