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
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
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
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

