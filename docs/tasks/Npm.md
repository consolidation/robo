# Npm Tasks

## Install


Npm Install

``` php
<?php
// simple execution
$this->taskNpmInstall()->run();

// prefer dist with custom path
$this->taskNpmInstall('path/to/my/npm')
     ->noDev()
     ->run();
?>
```

* `noDev()`  adds `production` option to npm
* `addToCollection($collection, $taskName = null, $rollbackTask = null)` 
* `addAsRollback($collection)` 
* `addAsCompletion($collection)` 
* `addToCollectionAndIgnoreErrors($collection, $taskName = null)` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

## Update


Npm Update

```php
<?php
// simple execution
$this->taskNpmUpdate()->run();

// prefer dist with custom path
$this->taskNpmUpdate('path/to/my/npm')
     ->noDev()
     ->run();
?>
```

* `noDev()`  adds `production` option to npm
* `addToCollection($collection, $taskName = null, $rollbackTask = null)` 
* `addAsRollback($collection)` 
* `addAsCompletion($collection)` 
* `addToCollectionAndIgnoreErrors($collection, $taskName = null)` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

