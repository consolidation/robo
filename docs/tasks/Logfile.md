# Logfile Tasks

## Rotate logfile


Rotate and purge obsolete logs.

``` php
<?php
$this->taskRotateLog('logfile.log')->run();
// as shortcut
$this->_rotateLog('logfile.log');
?>
```

* `keep(int $keep)` Logfiles to keep, default is 3.

## Truncate logfile


Truncates or create empty logfile if logfile not exists.

``` php
<?php
$this->taskTruncateLog('logfile.log')->run();
// as shortcut
$this->_rotateTruncate('logfile.log');
?>
```
