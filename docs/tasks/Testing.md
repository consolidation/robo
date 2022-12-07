# Testing Tasks
## Atoum


Runs [atoum](http://atoum.org/) tests

``` php
<?php
$this->taskAtoum()
 ->files('path/to/test.php')
 ->configFile('config/dev.php')
 ->run()

?>
```

* `tags($tags)`

 * `param string|string[]` $tags
* `lightReport()`

 * `return $this`
* `tap()`

 * `return $this`
* `bootstrap($file)`

 * `param string` $file
* `configFile($file)`

 * `param string` $file
* `debug()`

 * `return $this`
* `files($files)`

 * `param ` $files
* `directories($directories)`

 * `param ` $directories
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

## Behat


Executes Behat tests

``` php
<?php
$this->taskBehat()
     ->format('pretty')
     ->noInteraction()
     ->run();
?>
```


* `stopOnFail()`

 * `return $this`
* `noInteraction()`

 * `return $this`
* `config($config_file)`

 * `param string` $config_file
* `colors()`

 * `return $this`
* `noColors()`

 * `return $this`
* `suite($suite)`

 * `param string` $suite
* `verbose($level = null)`

 * `param string` $level
* `format($formater)`

 * `param string` $formater
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

## Codecept


Executes Codeception tests

``` php
<?php
// config
$this->taskCodecept()
     ->suite('acceptance')
     ->env('chrome')
     ->group('admin')
     ->xml()
     ->html()
     ->run();

?>
```


* `suite($suite)`

 * `param string` $suite
* `test($testName)`

 * `param string` $testName
* `group($group)`

 * `param string` $group
* `excludeGroup($group)`

 * `param string` $group
* `json($file = null)`

 * `param string` $file
* `xml($file = null)`

 * `param string` $file
* `html($dir = null)`

 * `param string` $dir
* `tap($file = null)`

 * `param string` $file
* `configFile($file)`

 * `param string` $file
* `coverage($cov = null)`

 * `param null|string` $cov
* `coverageXml($xml = null)`

 * `param string` $xml
* `coverageHtml($html = null)`

 * `param string` $html
* `debug()`

 * `return $this`
* `noRebuild()`

 * `return $this`
* `noExit()`

 * `return $this`
* `failGroup($failGroup)`

 * `param string` $failGroup
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

## PHPUnit


Runs PHPUnit tests

``` php
<?php
$this->taskPHPUnit()
 ->group('core')
 ->bootstrap('test/bootstrap.php')
 ->run()

?>
```

* `filter($filter)`

 * `param string` $filter
* `group($group)`

 * `param string` $group
* `excludeGroup($group)`

 * `param string` $group
* `json($file = null)`

 * `param string` $file
* `xml($file = null)`

 * `param string` $file
* `tap($file = null)`

 * `param string` $file
* `bootstrap($file)`

 * `param string` $file
* `configFile($file)`

 * `param string` $file
* `debug()`

 * `return $this`
* `files($files)`

 @deprecated
* `file($file)`

 * `param string` $file Path to file to test.
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

## Phpspec


Executes Phpspec tests

``` php
<?php
$this->taskPhpspec()
     ->format('pretty')
     ->noInteraction()
     ->run();
?>
```


* `stopOnFail()`


* `noCodeGeneration()`


* `quiet()`


* `verbose($level = null)`

 * `param string` $level
* `noAnsi()`

 * `return $this`
* `noInteraction()`

 * `return $this`
* `config($config_file)`

 * `param string` $config_file
* `format($formater)`

 * `param string` $formater
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


