<?php

/**
 * @author MBCRAFT di Marco Bagnaresi - mail : info@mbcraft.it
 * 
 *  
 */

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
                echo "Unable to find route : ".$route.".\n";
                Limalably::finish(1);
            } else {
                $error_format = LConfigReader::simple("/format/default_error_format");
                $page_not_found = new LHttpError(LHttpError::ERROR_PAGE_NOT_FOUND);
                $page_not_found->execute($error_format);
            }
        }
        
    }

}
