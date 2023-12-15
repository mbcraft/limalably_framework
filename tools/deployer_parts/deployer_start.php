<?php

//--launch

if (isset($_POST['METHOD'])) {

    try {
       $controller = new DeployerController();

	   $controller->processRequest();

    } catch (\Exception $ex) {
        echo $controller->preparePostResponse($controller->failure("Server got an exception : ".$ex->getMessage()." - ".$ex->getTraceAsString()));
    }
} else echo "Hello :)";