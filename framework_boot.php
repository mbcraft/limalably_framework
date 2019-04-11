<?php

//hashmap
require_once ('lib/treemap/LTreeMap.class.php');
require_once ('lib/treemap/LTreeMapView.class.php');
require_once ('lib/treemap/LStaticTreeMapBase.trait.php');
require_once ('lib/treemap/LStaticTreeMapRead.trait.php');
require_once ('lib/treemap/LStaticTreeMapWrite.trait.php');

//config
require_once ('lib/config/LConfig.class.php');
require_once ('lib/config/LConfigReader.class.php');
require_once ('lib/config/LExecutionMode.class.php');
require_once ('lib/config/LEnvironmentUtils.class.php');

//core
require_once ('lib/core/LErrorReportingInterceptors.class.php');
require_once ('lib/core/LInvalidParameterException.class.php');
require_once ('lib/core/LResult.class.php');
require_once ('lib/core/LClassLoader.class.php');

//utils
require_once ('lib/utils/LStringUtils.class.php');

LConfig::init();

LClassLoader::init();

$error_reporter = new LErrorReportingInterceptors();
$error_reporter->register();