# Composer Tasks

## CheckPlatformReqs


Composer Check Platform Requirements

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Config


Composer Config

``` php
<?php
// simple execution
$this->taskComposerConfig()->set('bin-dir', 'bin/')->run();
?>
```

* `set($key, $value)`

 * `param string` $key
* `useGlobal($useGlobal = null)`

 * `param bool` $useGlobal
* `repository($id, $uri, $repoType = null)`

 * `param string` $id
* `removeRepository($id)`

 * `param string` $id
* `disableRepository($id)`

 * `param string` $id
* `enableRepository($id)`

 * `param string` $id
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## CreateProject


Composer CreateProject

``` php
<?php
// simple execution
$this->taskComposerCreateProject()->source('foo/bar')->target('myBar')->run();
?>
```

* `source($source)`

 * `param string` $source
* `target($target)`

 * `param string` $target
* `version($version)`

 * `param string` $version
* `keepVcs($keep = null)`

 * `param bool` $keep
* `noInstall($noInstall = null)`

 * `param bool` $noInstall
* `repository($repository)`

 * `param string` $repository
* `stability($stability)`

 * `param string` $stability
* `buildCommand()`

 {@inheritdoc}
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

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

* `optimize($optimize = null)`

 * `param bool` $optimize
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Init


Composer Init

``` php
<?php
// simple execution
$this->taskComposerInit()->run();
?>
```

* `projectName($projectName)`

 * `param string` $projectName
* `description($description)`

 * `param string` $description
* `author($author)`

 * `param string` $author
* `projectType($type)`

 * `param string` $type
* `homepage($homepage)`

 * `param string` $homepage
* `dependency($project, $version = null)`

 * `param string` $project
* `stability($stability)`

 * `param string` $stability
* `license($license)`

 * `param string` $license
* `repository($repository)`

 * `param string` $repository
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

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

* `noSuggest($noSuggest = null)`

 * `param bool` $noSuggest
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Remove


Composer Remove

``` php
<?php
// simple execution
$this->taskComposerRemove()->run();
?>
```

* `dev($dev = null)`

 * `param bool` $dev
* `noProgress($noProgress = null)`

 * `param bool` $noProgress
* `noUpdate($noUpdate = null)`

 * `param bool` $noUpdate
* `updateNoDev($updateNoDev = null)`

 * `param bool` $updateNoDev
* `noUpdateWithDependencies($updateWithDependencies = null)`

 * `param bool` $updateWithDependencies
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## RequireDependency


Composer Require

``` php
<?php
// simple execution
$this->taskComposerRequire()->dependency('foo/bar', '^.2.4.8')->run();
?>
```

* `dependency($project, $version = null)`

 * `param string` $project
* `noSuggest($noSuggest = null)`

 * `param bool` $noSuggest
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option


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

* `noSuggest($noSuggest = null)`

 * `param bool` $noSuggest
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Validate


Composer Validate

``` php
<?php
// simple execution
$this->taskComposerValidate()->run();
?>
```

* `noCheckAll($noCheckAll = null)`

 * `param bool` $noCheckAll
* `noCheckLock($noCheckLock = null)`

 * `param bool` $noCheckLock
* `noCheckPublish($noCheckPublish = null)`

 * `param bool` $noCheckPublish
* `withDependencies($withDependencies = null)`

 * `param bool` $withDependencies
* `strict($strict = null)`

 * `param bool` $strict
* `preferDist($preferDist = null)`

 * `param bool` $preferDist
* `preferSource()`

 * `return $this`
* `dev($dev = null)`

 * `param bool` $dev
* `noDev()`

 * `return $this`
* `ansi($ansi = null)`

 * `param bool` $ansi
* `noAnsi()`

 * `return $this`
* `interaction($interaction = null)`

 * `param bool` $interaction
* `noInteraction()`

 * `return $this`
* `optimizeAutoloader($optimize = null)`

 * `param bool` $optimize
* `ignorePlatformRequirements($ignore = null)`

 * `param bool` $ignore
* `disablePlugins($disable = null)`

 * `param bool` $disable
* `noScripts($disable = null)`

 * `param bool` $disable
* `workingDir($dir)`

 * `param string` $dir
* `buildCommand()`

 Copy class fields into command options as directed.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

