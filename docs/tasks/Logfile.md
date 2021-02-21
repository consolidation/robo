# Logfile Tasks

## RotateLog


Rotates a log (or any other) file

``` php
<?php
$this->taskRotateLog(['logfile.log'])->run();
// or use shortcut
$this->_rotateLog(['logfile.log']);

?>
```

* `keep($keep)`   * `param int` $keep
* `chmod($chmod)`   * `param int` $chmod
* `setOutput($output)`  Sets the Console Output.



## TruncateLog


Truncates a log (or any other) file

``` php
<?php
$this->taskTruncateLog(['logfile.log'])->run();
// or use shortcut
$this->_truncateLog(['logfile.log']);

?>
```

* `chmod($chmod)`   * `param int` $chmod
* `setOutput($output)`  Sets the Console Output.

