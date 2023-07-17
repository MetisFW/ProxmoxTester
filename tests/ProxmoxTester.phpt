<?php

use MetisFW\ProxmoxTester\ProxmoxMachine;
use Tester\Assert;

require __DIR__."/bootstrap.php";

Assert::exception(
  function() {
    $m = ProxmoxMachine::id("not-exists");
  },
  'MetisFW\ProxmoxTester\ProxmoxMachineException'
);

$m = ProxmoxMachine::id("proxmox-test");

Assert::contains('sbin', $m->run("ls /"));

Assert::exception(
  function() use($m) {
    $m->run("wrong");
  },
  'MetisFW\ProxmoxTester\ProxmoxMachineException'
);

Assert::true($m->fileExists("/bin/bash"));
Assert::false($m->fileExists("/not-exists"));

Assert::exception(
  function() use($m) {
    $m->file('/not-exists');
  },
  'MetisFW\ProxmoxTester\ProxmoxMachineException'
);

ProxmoxMachine::$output = function($machine, $text) {
  file_put_contents(TEMP_DIR . "/$machine.output", $text, FILE_APPEND);
};

$m->run("ls /");

Assert::contains('>>[proxmox-test]>> ls /', file_get_contents(TEMP_DIR . "/proxmox-test.output"));
