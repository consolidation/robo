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

* `tags($tags)`  Tag or Tags to filter.
* `lightReport()`  Display result using the light reporter.
* `tap()`  Display result using the tap reporter.
* `bootstrap($file)`  Path to the bootstrap file.
* `configFile($file)`  Path to the config file.
* `debug()`  Use atoum's debug mode.
* `files($files)`  Test file or test files to run.
* `directories($directories)`  Test directory or directories to run.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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


* `stopOnFail()`   * `return` $this
* `noInteraction()`   * `return` $this
* `config($config_file)`   * `param` $config_file
* `colors()`   * `return` $this
* `noColors()`   * `return` $this
* `suite($suite)`   * `param string` $suite
* `verbose($level = null)`   * `param string` $level
* `format($formater)`   * `param string` $formater
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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


* `suite($suite)`   * `param string` $suite
* `test($testName)`   * `param string` $testName
* `group($group)`  set group option. Can be called multiple times
* `excludeGroup($group)`   * `param string` $group
* `json($file = null)`  generate json report
* `xml($file = null)`  generate xml JUnit report
* `html($dir = null)`  Generate html report
* `tap($file = null)`  generate tap report
* `configFile($file)`  provides config file other then default `codeception.yml` with `-c` option
* `coverage($cov = null)`  collect codecoverage in raw format. You may pass name of cov file to save results
* `coverageXml($xml = null)`  collect code coverage in xml format. You may pass name of xml file to save results
* `coverageHtml($html = null)`  collect code coverage and generate html report. You may pass
* `debug()`   * `return` $this
* `noRebuild()`   * `return` $this
* `failGroup($failGroup)`   * `param string` $failGroup
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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

* `filter($filter)`   * `param string` $filter
* `group($group)`   * `param string` $group
* `excludeGroup($group)`   * `param string` $group
* `json($file = null)`  adds `log-json` option to runner
* `xml($file = null)`  adds `log-junit` option
* `tap($file = null)`   * `param string` $file
* `bootstrap($file)`   * `param string` $file
* `configFile($file)`   * `param string` $file
* `debug()`   * `return` $this
* `files($files)`  Directory of test files or single test file to run.
* `file($file)`  Test the provided file.
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

