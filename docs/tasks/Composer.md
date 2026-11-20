# Composer Tasks

## CheckPlatformReqs


Composer Check Platform Requirements

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Config


Composer Config

``` php
<?php
// simple execution
$this->taskComposerConfig()->set('bin-dir', 'bin/')->run();
?>
```

* `set($key, $value)` Set a configuration value.
* `useGlobal($useGlobal = true)` Operate on the global repository
* `repository($id, $uri, $repoType = 'vcs')`
* `removeRepository($id)`
* `disableRepository($id)`
* `enableRepository($id)`
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## CreateProject


Composer CreateProject

``` php
<?php
// simple execution
$this->taskComposerCreateProject()->source('foo/bar')->target('myBar')->run();
?>
```

* `source($source)`
* `target($target)`
* `version($version)`
* `keepVcs($keep = true)`
* `noInstall($noInstall = true)`
* `repository($repository)`
* `stability($stability)`
* `buildCommand()` {@inheritdoc}
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

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

* `optimize($optimize = true)`
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Exec


Class Exec

@package Robo\Task\Composer

* `scriptOption($option, $value = null)` Add a script option.
* `buildCommand()` Add script options to command.
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Init


Composer Init

``` php
<?php
// simple execution
$this->taskComposerInit()->run();
?>
```

* `projectName($projectName)`
* `description($description)`
* `author($author)`
* `projectType($type)`
* `homepage($homepage)`
* `dependency($project, $version = null)` 'require' is a keyword, so it cannot be a method name.
* `stability($stability)`
* `license($license)`
* `repository($repository)`
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

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

* `noSuggest($noSuggest = true)` adds `no-suggest` option to composer
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Remove


Composer Remove

``` php
<?php
// simple execution
$this->taskComposerRemove()->run();
?>
```

* `dependency($project)` 'remove' is a keyword, so it cannot be a method name.
* `dev($dev = true)`
* `noProgress($noProgress = true)`
* `noUpdate($noUpdate = true)`
* `updateNoDev($updateNoDev = true)`
* `noUpdateWithDependencies($updateWithDependencies = true)`
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## RequireDependency


Composer Require

``` php
<?php
// simple execution
$this->taskComposerRequire()->dependency('foo/bar', '^.2.4.8')->run();
?>
```

* `dependency($project, $version = null)` 'require' is a keyword, so it cannot be a method name.
* `noSuggest($noSuggest = true)` adds `no-suggest` option to composer
* `noUpdate()` adds `no-update` option to composer
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## RunScript


Class RunScript

@package Robo\Task\Composer

* `scriptOption($option, $value = null)` Add a script option.
* `buildCommand()` Add script options to command.
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


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

* `noSuggest($noSuggest = true)` adds `no-suggest` option to composer
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Validate


Composer Validate

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `noCheckAll($noCheckAll = true)`
* `noCheckLock($noCheckLock = true)`
* `noCheckPublish($noCheckPublish = true)`
* `withDependencies($withDependencies = true)`
* `strict($strict = true)`
* `preferDist($preferDist = true)` adds `prefer-dist` option to composer
* `preferSource()` adds `prefer-source` option to composer
* `dev($dev = true)` adds `dev` option to composer
* `noDev()` adds `no-dev` option to composer
* `ansi($ansi = true)` adds `ansi` option to composer
* `noAnsi()` adds `no-ansi` option to composer
* `interaction($interaction = true)`
* `noInteraction()` adds `no-interaction` option to composer
* `optimizeAutoloader($optimize = true)` adds `optimize-autoloader` option to composer
* `ignorePlatformRequirements($ignore = true)` adds `ignore-platform-reqs` option to composer
* `disablePlugins($disable = true)` disable plugins
* `noScripts($disable = true)` skip scripts
* `workingDir($dir)` adds `--working-dir $dir` option to composer
* `buildCommand()` Copy class fields into command options as directed.
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

