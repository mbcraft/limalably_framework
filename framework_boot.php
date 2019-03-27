<?php

//hashmap
require_once ('lib/hashmap/LHashMap.class.php');
require_once ('lib/hashmap/LHashMapView.class.php');
require_once ('lib/hashmap/LStaticHashMapBase.trait.php');
require_once ('lib/hashmap/LStaticReadHashMap.trait.php');
require_once ('lib/hashmap/LStaticWriteHashMap.trait.php');

//config
require_once ('lib/config/LConfig.class.php');
require_once ('lib/config/LConfigReader.class.php');
require_once ('lib/config/LExecutionMode.class.php');
require_once ('lib/config/LEnvironmentUtils.class.php');

//core
require_once ('lib/core/LInvalidParameterException.class.php');
require_once ('lib/core/LOutput.class.php');
require_once ('lib/core/LClassLoader.class.php');

//utils
require_once ('lib/utils/LStringUtils.class.php');


LConfig::init();

LClassLoader::init();