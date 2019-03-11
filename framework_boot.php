<?php

//core
require_once ('lib/core/LReadHashMap.trait.php');

require_once ('lib/core/LOutput.class.php');
require_once ('lib/core/LConfig.class.php');
require_once ('lib/core/LExecutionMode.class.php');

//... misc others

//unit test
require_once ('lib/test/LAssert.class.php');
require_once ('lib/test/LTestCase.class.php');
require_once ('lib/test/LTestRunner.class.php');

//alla fine la classe Lym
require_once ('lib/Lym.class.php');

Lym::boot();