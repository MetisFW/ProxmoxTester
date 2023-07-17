# MetisFW/ProxmoxTester

## Setup

This must be done beafore connectiong to any Proxmox machine

```php
ProxmoxMachine::$locksDir = '/path/to/locks';
ProxmoxMachine::$tempDir = '/path/to/temp';
```

By default Proxmox machine prints log messages using echo. You can use custom output function:

```php
ProxmoxMachine::$output = function($machine, $text) {
  file_put_contents("$machine.output", $text, FILE_APPEND);
}
```

##Usage

##### Connect to Proxmox machine

```php
$machine = ProxmoxMachine::id("proxmox-tester");
```

If you connect to multiple machines connection must be done in alphabetical order to prevent deadlocks.

##### Execute command inside Proxmox machine

```php
$output = $machine->run("ls /")
```

##### Get content of file from Proxmox machine

```php
$fileContent = $machine->file("/etc/hosts")
```

##### Chech if file exists inside Proxmox machine

```php
$fileexists = $machine->fileExists("/etc/hosts")
```
