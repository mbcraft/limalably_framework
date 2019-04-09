<?php

class LMaintenanceRouteExecutor implements LICommandExecutor {

    private $executed = false;

    public function hasExecutedCommand() {
        return $this->executed;
    }

    public function tryExecuteCommand() {
        LResult::framework_debug("Executing maintenance route executor ...");
        $this->executed = true;

        if (LEnvironmentUtils::getEnvironment() == 'script') {
            $search_flags = LUrlMapResolver::FLAGS_SEARCH_ALL;

            $route = $_SERVER['ROUTE'];

            $route_resolver = new LUrlMapResolver();
            $urlmap = $route_resolver->resolveUrlMap($route, $search_flags);

            if ($urlmap) {
                $executor = new LUrlMapExecutor($urlmap);

                $executor->executeRootRequest($route);
            } else {

                echo "Unable to find route : " . $route . ".\n";
                exit(1);
            }
        } else {
            
            $error_format = LConfigReader::simple("/format/default_error_format");

            $maintenance = new LHttpError(LHttpError::ERROR_SERVICE_UNAVAILABLE);

            $maintenance->execute($error_format);

        }
    }

}
