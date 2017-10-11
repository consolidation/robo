# Composer Tasks

## Config


Composer Config

``` php
<?php
// simple execution
$this->taskComposerConfig()->set('bin-dir', 'bin/')->run();
?>
```

* `set($key, $value)`  Set a configuration value
* `useGlobal($useGlobal = null)`  Operate on the global repository
* `repository($id, $uri, $repoType = null)`   * `return` $this
* `removeRepository($id)`   * `return` $this
* `disableRepository($id)`   * `return` $this
* `enableRepository($id)`   * `return` $this
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

## CreateProject


Composer CreateProject

``` php
<?php
// simple execution
$this->taskComposerCreateProject()->source('foo/bar')->target('myBar')->run();
?>
```

* `source($source)`   * `return` $this
* `target($target)`   * `return` $this
* `version($version)`   * `return` $this
* `keepVcs($keep = null)` 
* `noInstall($noInstall = null)` 
* `repository($repository)`   * `return` $this
* `stability($stability)`   * `return` $this
* `buildCommand()`  Copy class fields into command options as directed.
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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

* `optimize($optimize = null)`   * `return` $this
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

## Init


Composer Init

``` php
<?php
// simple execution
$this->taskComposerInit()->run();
?>
```

* `projectName($projectName)`   * `return` $this
* `description($description)`   * `return` $this
* `author($author)`   * `return` $this
* `projectType($type)`   * `return` $this
* `homepage($homepage)`   * `return` $this
* `dependency($project, $version = null)`  'require' is a keyword, so it cannot be a method name.
* `stability($stability)`   * `return` $this
* `license($license)`   * `return` $this
* `repository($repository)`   * `return` $this
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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

* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

## Remove


Composer Remove

``` php
<?php
// simple execution
$this->taskComposerRemove()->run();
?>
```

* `dev($dev = null)`   * `return` $this
* `noProgress($noProgress = null)`   * `return` $this
* `noUpdate($noUpdate = null)`   * `return` $this
* `updateNoDev($updateNoDev = null)`   * `return` $this
* `noUpdateWithDependencies($updateWithDependencies = null)`   * `return` $this
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

## RequireDependency


Composer Require

``` php
<?php
// simple execution
$this->taskComposerRequire()->dependency('foo/bar', '^.2.4.8')->run();
?>
```

* `dependency($project, $version = null)`  'require' is a keyword, so it cannot be a method name.
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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

* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

## Validate


Composer Validate

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `noCheckAll($noCheckAll = null)`   * `return` $this
* `noCheckLock($noCheckLock = null)`   * `return` $this
* `noCheckPublish($noCheckPublish = null)`   * `return` $this
* `withDependencies($withDependencies = null)`   * `return` $this
* `strict($strict = null)`   * `return` $this
* `preferDist($preferDist = null)`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `dev($dev = null)`  adds `dev` option to composer
* `noDev()`  adds `no-dev` option to composer
* `ansi($ansi = null)`  adds `ansi` option to composer
* `noAnsi()`  adds `no-ansi` option to composer
* `interaction($interaction = null)` 
* `noInteraction()`  adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = null)`  adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = null)`  adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = null)`  disable plugins
* `workingDir($dir)`  adds `--working-dir $dir` option to composer
* `buildCommand()`  Copy class fields into command options as directed.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

