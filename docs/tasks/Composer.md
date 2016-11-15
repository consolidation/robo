# Composer Tasks

## DumpAutoload


Composer Dump Autoload

``` php
<?php
// simple execution
$this->taskComposerDumpAutoload()->run();

// dump auto loader with custom path
$this->taskComposerDumpAutoload('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader dump with custom path
$this->taskComposerDumpAutoload('path/to/my/composer.phar')
     ->optimize()
     ->run();

// optimize autoloader dump with custom path and no dev
$this->taskComposerDumpAutoload('path/to/my/composer.phar')
     ->optimize()
     ->noDev()
     ->run();
?>
```

* `optimize()`   * `return` $this
* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `ansi()`  adds `ansi` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

## Install


Composer Install

``` php
<?php
// simple execution
$this->taskComposerInstall()->run();

// prefer dist with custom path
$this->taskComposerInstall('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader with custom path
$this->taskComposerInstall('path/to/my/composer.phar')
     ->optimizeAutoloader()
     ->run();
?>
```

* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `ansi()`  adds `ansi` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

## Remove


Composer Validate

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `dev()`   * `return` $this
* `noProgress()`   * `return` $this
* `noUpdate()`   * `return` $this
* `updateNoDev()`   * `return` $this
* `noUpdateWithDependencies()`   * `return` $this
* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `ansi()`  adds `ansi` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

## Update


Composer Update

``` php
<?php
// simple execution
$this->taskComposerUpdate()->run();

// prefer dist with custom path
$this->taskComposerUpdate('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader with custom path
$this->taskComposerUpdate('path/to/my/composer.phar')
     ->optimizeAutoloader()
     ->run();
?>
```

* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `ansi()`  adds `ansi` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

## Validate


Composer Validate

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `noCheckAll()`   * `return` $this
* `noCheckLock()`   * `return` $this
* `noCheckPublish()`   * `return` $this
* `withDependencies()`   * `return` $this
* `strict()`   * `return` $this
* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `ansi()`  adds `ansi` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

