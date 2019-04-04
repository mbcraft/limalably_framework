<?php

class LDefaultRouteExecutor implements LICommandExecutor {
    
    private $executed = false;
    
    public function hasExecutedCommand() {
        return $this->executed;
    }

    public function tryExecuteCommand() {
        
        $route_resolver = new LUrlMapResolver();
        $route_resolver->
        
    }

}
