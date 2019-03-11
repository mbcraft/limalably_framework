<?php

//core
require_once ('lib/core/LOutput.php');
require_once ('lib/core/LConfig.class.php');
require_once ('lib/core/LExecutionMode.class.php');

//... misc others

//unit test
require_once ('test/LAssert.class.php');
require_once ('test/LTestCase.class.php');
require_once ('test/LTestRunner.class.php');

//alla fine la classe Lym
require_once ('lib/Lym.class.php');

Lym::boot();