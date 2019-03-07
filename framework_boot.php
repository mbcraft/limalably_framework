<?php

//unit test
require_once ('test/LAssert.class.php');
require_once ('test/LTestCase.class.php');
require_once ('test/LTestRunner.class.php');

//...

//alla fine il core
require_once ('Lym.class.php');

echo "Clear\n";
LTestRunner::clear();
echo "Collect\n";
LTestRunner::collect('test/');
echo "Run\n";
LTestRunner::run();

/*
echo "Hello world! :) \n";
echo "Hostname found : ".$_SERVER['HOSTNAME']."\n";
echo "Route found : ".$_SERVER['ROUTE']."\n"; 
*/