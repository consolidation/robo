# Bower Tasks

## Install


Bower Install

``` php
<?php
// simple execution
$this->taskBowerInstall()->run();

// prefer dist with custom path
$this->taskBowerInstall('path/to/my/bower')
     ->noDev()
     ->run();
?>
```

* `allowRoot()`  adds `allow-root` option to bower
* `forceLatest()`  adds `force-latest` option to bower
* `noDev()`  adds `production` option to bower
* `offline()`  adds `offline` option to bower
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

## Update


Bower Update

``` php
<?php
// simple execution
$this->taskBowerUpdate->run();

// prefer dist with custom path
$this->taskBowerUpdate('path/to/my/bower')
     ->noDev()
     ->run();
?>
```

* `allowRoot()`  adds `allow-root` option to bower
* `forceLatest()`  adds `force-latest` option to bower
* `noDev()`  adds `production` option to bower
* `offline()`  adds `offline` option to bower
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

