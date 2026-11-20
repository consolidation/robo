# Gulp Tasks

## Run


Gulp Run

``` php
<?php
// simple execution
$this->taskGulpRun()->run();

// run task 'clean' with --silent option
$this->taskGulpRun('clean')
     ->silent()
     ->run();
?>
```

* `noColor()` adds `--no-color` option to gulp
* `color()` adds `--color` option to gulp
* `simple()` adds `--tasks-simple` option to gulp
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


