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

* `silent()`  adds `silent` option to gulp
* `noColor()`  adds `--no-color` option to gulp
* `color()`  adds `--color` option to gulp
* `simple()`  adds `--tasks-simple` option to gulp
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

