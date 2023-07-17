<?php

use MetisFW\ProxmoxTester\ProxmoxMachine;
use Tester\Assert;

require __DIR__."/../vendor/autoload.php";
require __DIR__."/../src/MetisFW/ProxmoxTester/ProxmoxMachine.php";
require __DIR__."/../src/MetisFW/ProxmoxTester/ProxmoxMachineException.php";

/**
 * @param object $val Function added by nette tester.
 * @return object only return the parameter.
 * @SuppressWarnings(ShortMethodName)
 */
function id($val) {
  return $val;
}

// create temporary directory
define('TEMP_DIR', __DIR__.'/temp/test-'.basename($argv[0]).'-'.getmypid());
@mkdir(dirname(TEMP_DIR));
Tester\Helpers::purge(TEMP_DIR);

define('LOCK_DIR', __DIR__.'/temp/locks');
@mkdir(LOCK_DIR);

// ensure Tester is avaliable
if(!class_exists('Tester\Assert')) {
  echo "Install Nette Tester using `composer update`\n";
  exit(1);
}

// setup environment
ProxmoxMachine::$locksDir = LOCK_DIR;
Tester\Environment::setup();
date_default_timezone_set('Europe/Prague');
