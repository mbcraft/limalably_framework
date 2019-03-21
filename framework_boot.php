<?php

//hashmap
require_once ('lib/hashmap/LHashMap.class.php');
require_once ('lib/hashmap/LHashMapView.class.php');
require_once ('lib/hashmap/LStaticHashMapBase.trait.php');
require_once ('lib/hashmap/LStaticReadHashMap.trait.php');
require_once ('lib/hashmap/LStaticWriteHashMap.trait.php');

//core
require_once ('lib/core/LInvalidParameterException.class.php');
require_once ('lib/core/LOutput.class.php');
require_once ('lib/core/LConfig.class.php');
require_once ('lib/core/LExecutionMode.class.php');

//utils
require_once ('lib/utils/LStringUtils.class.php');
require_once ('lib/utils/LEnvironmentUtils.class.php');

//logging
require_once ('lib/logging/LILogger.interface.php');
require_once ('lib/logging/LILogWriter.interface.php');
require_once ('lib/logging/LLog.class.php');

require_once ('lib/logging/file/LFileLogWriter.class.php');
require_once ('lib/logging/file/LDistinctFileLogger.class.php');
require_once ('lib/logging/file/LTogetherFileLogger.class.php');
require_once ('lib/logging/output/LOutputLogger.class.php');
require_once ('lib/logging/db/LMysqlLogWriter.class.php');
require_once ('lib/logging/db/LDbLogger.class.php');



//... misc others
//routing
require_once ('lib/routing/manager/LIRouteManager.interface.php');
require_once ('lib/routing/manager/LDefaultRouteManager.class.php');
require_once ('lib/routing/manager/LMaintenanceRouteManager.class.php');

//database
require_once ('lib/db/connection/LIDbConnection.interface.php');
require_once ('lib/db/connection/LMysqlConnection.class.php');
require_once ('lib/db/connection/LSqliteConnection.class.php');

require_once ('lib/db/connection/LDbConnectionManager.class.php');

//unit test
require_once ('lib/test/LAssert.class.php');
require_once ('lib/test/LTestCase.class.php');
require_once ('lib/test/LTestRunner.class.php');

//commands
require_once ('lib/command/LICommandExecutor.interface.php');
require_once ('lib/command/LFrameworkCommandExecutor.class.php');
require_once ('lib/command/LProjectCommandExecutor.class.php');

//alla fine la classe Lym
require_once ('lib/Lym.class.php');

