<?php

class LDefaultRouteExecutor implements LICommandExecutor {
    
    private $executed = false;
    
    public function hasExecutedCommand() {
        return $this->executed;
    }

    public function tryExecuteCommand() {
        
        $this->executed = true;
        
        if (LEnvironmentUtils::getEnvironment()=='script') {
            $search_flags = LUrlMapResolver::FLAGS_SEARCH_ALL;
        } else {
            if (LExecutionMode::isProduction() || LExecutionMode::isTesting()) {
                $search_flags = LUrlMapResolver::FLAGS_SEARCH_PUBLIC;
            } else {
                $search_flags = LUrlMapResolver::FLAGS_SEARCH_ALL;
            }
        }
        
        $route = $_SERVER['ROUTE'];
        
        $route_resolver = new LUrlMapResolver();
        $urlmap = $route_resolver->resolveUrlMap($route, $search_flags);
        
        if ($urlmap) {
            $executor = new LUrlMapExecutor($urlmap);
            
            $executor->executeRootRequest($route);
        } else {
            if (LEnvironmentUtils::getEnvironment()=='script') {
                echo "Unable to find route : ".$route.".";
                exit(1);
            } else {
                $error_format = LConfigReader::simple("/format/default_error_format");
                $page_not_found = new LHttpError(404);
                $page_not_found->execute($error_format);
            }
        }
        
    }

}
