# Testing Tasks
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
* `test($testName)` 
* `group($group)`  set group option. Can be called multiple times
* `excludeGroup($group)` 
* `json($file = null)`  generate json report
* `xml($file = null)`  generate xml JUnit report
* `html($dir = null)`  Generate html report
* `tap($file = null)`  generate tap report
* `configFile($file)`  provides config file other then default `codeception.yml` with `-c` option
* `coverage()`  turn on collecting code coverage
* `silent()`  execute in silent mode
* `coverageXml($xml = null)`  collect code coverage in xml format. You may pass name of xml file to save results
* `coverageHtml($html = null)`  collect code coverage and generate html report. You may pass
* `env($env)` 
* `debug()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

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
* `group($group)` 
* `excludeGroup($group)` 
* `json($file = null)`  adds `log-json` option to runner
* `xml($file = null)`  adds `log-xml` option
* `tap($file = null)` 
* `bootstrap($file)` 
* `configFile($file)` 
* `debug()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

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
* `noAnsi()` 
* `noInteraction()` 
* `config($config_file)` 
* `format($formater)` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

