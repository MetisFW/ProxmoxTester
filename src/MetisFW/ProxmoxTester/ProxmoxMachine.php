<?php

namespace MetisFW\ProxmoxTester;

use Tester\Environment;

class ProxmoxMachine {

  private $machine;

  private static $locked = array();

  public static $locksDir;

  public static $output;

  public function __construct($machine) {
    $this->machine = $machine;
    $this->machineId = $this->getMachineId($machine);
    if(self::$locksDir == null) {
      self::$locksDir = sys_get_temp_dir();
    }
    $this->checkLockOrder($machine);
    Environment::lock($machine, self::$locksDir);
  }

  private function getMachineId($machine) {
    static $machineIds = null;
    $output = null;
    $status = null;
    exec("sudo pct list | awk '{print $1, \$NF}' | sed '1d'", $output, $status);
    if($status != 0) {
      throw new ProxmoxMachineException("Cannot get list of machines");
    }
    if($machineIds === null) {
      $machineIds = array();
      foreach($output as $line) {
        list($id, $name) = explode(" ", $line, 2);
        $machineIds[$name] = $id;
      }
    }
    if(!isset($machineIds[$machine])) {
      throw new ProxmoxMachineException("Machine '$machine' does not exists");
    }
    return $machineIds[$machine];
  }

  private function checkLockOrder($machine) {
    foreach(self::$locked as $id => $value) {
      if($id > $machine) {
        throw new ProxmoxMachineException(
          "Machines must be required in alphabetical order to prevent deadlocks." .
          "Machine $machine must by required before $id"
        );
      }
    }
    self::$locked[$machine] = true;
  }

  public static function id($machine) {
    return new ProxmoxMachine($machine);
  }

  protected function out($text) {
    $output = self::$output;
    $text = "$text\n";
    if($output === null) {
      echo $text;
    } else {
      $output($this->machine, $text);
    }
  }

  public function run($command, $expectedStatus = 0)
  {
    list($status, $outputStr) = $this->executeCommand($command);
    if ($status != $expectedStatus) {
      throw new ProxmoxMachineException("Unexpected exit status ($status should be $expectedStatus) for '$command'");
    }
    return $outputStr;
  }

  public function file($path) {
    return $this->run("cat " . escapeshellarg($path));
  }

  public function fileExists($path) {
    list($status, $outputStr) = $this->executeCommand("test -f " . escapeshellarg($path));
    return ($status == 0);
  }

  protected function executeCommand($command) {
    $fullCommand = "sudo pct exec {$this->machineId} -- env -i bash -c " . escapeshellarg($command) . " 2>&1";
    $this->out(">>[{$this->machine}]>> $command ($fullCommand)");
    $output = array();
    $status = null;
    $startTime = time();
    exec($fullCommand, $output, $status);
    $endTime = time();
    $outputStr = implode("\n", $output);
    $timeElapsed = $endTime - $startTime;
    $this->out("<<[{$this->machine}]({$timeElapsed}s)<< [$status] $outputStr");
    return array($status, $outputStr);
  }

}
