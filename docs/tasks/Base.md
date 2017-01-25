# Base Tasks
## Exec


Executes shell script. Closes it when running in background mode.

``` php
<?php
$this->taskExec('compass')->arg('watch')->run();
// or use shortcut
$this->_exec('compass watch');

$this->taskExec('compass watch')->background()->run();

if ($this->taskExec('phpunit .')->run()->wasSuccessful()) {
 $this->say('tests passed');
}

?>
```

* `background()`  Executes command in background mode (asynchronously)
* `timeout($timeout)`  Stop command if it runs longer then $timeout in seconds
* `idleTimeout($timeout)`  Stops command if it does not output something for a while
* `interactive($arg)`  Sets process TTY mode, allowing user interaction with executed command.
* `setInput($arg)`  Sets the input stream for the executed PHP process. Can be resource created with `fopen()` or string.
* `env(array $env)`  Sets the environment variables for the command
* `simulate($context)`  {@inheritdoc}
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `optionList($option, $value = null)`  Pass multiple options to executable. Value can be a string or array.

## ExecStack


Execute commands one by one in stack.
Stack can be stopped on first fail if you call `stopOnFail()`.

```php
<?php
$this->taskExecStack()
 ->stopOnFail()
 ->exec('mkdir site')
 ->exec('cd site')
 ->run();

?>
```

* `$this stopOnFail()` 
* `executable($executable)`   * `param string` $executable
* `exec($command)`   * `param string|string[]` $command
* `stopOnFail($stopOnFail = null)`   * `param bool` $stopOnFail
* `result($result)` 
* `dir($dir)`  Changes working directory of command
* `printed($arg)`  _Deprecated_. Should command output be printed.
* `printOutput($arg)`  Should command output be printed directly to screen, bypassing the logger. Default to true.
* `logOutput($arg)`  Should command output be logged. This implicitly disables output printing. Defaults to false.
* `logMetadata($arg)`  Should command metadata (command, working directory, and timer) be logged. Defaults to true.
* `setLogLevel($arg)`  Determines the verbosity level at which command output is displayed on screen. Accepts [LogLevel](https://github.com/php-fig/log/blob/master/Psr/Log/LogLevel.php#L8) constants.
* `silent($arg)`  Shortcut for setting logMetadata(false) and logOutput(false). Defaults to false.

### Handling output

All tasks extending from `Robo\Task\BaseTask` inherit `setLogLevel()`, which sets the level at which task information is displayed on screen. For instance, `setLogLevel(LogLevel::INFO)` will display output only when a command is run with the `-v` argument.


## ParallelExec


Class ParallelExecTask

``` php
<?php
$this->taskParallelExec()
  ->process('php ~/demos/script.php hey')
  ->process('php ~/demos/script.php hoy')
  ->process('php ~/demos/script.php gou')
  ->run();
?>
```


* ` timeout(int $timeout)`  stops process if it runs longer then `$timeout` (seconds)
* ` idleTimeout(int $timeout)`  stops process if it does not output for time longer then `$timeout` (seconds)

* `printed($isPrinted = null)`   * `param bool` $isPrinted
* `process($command)`   * `param string|\Robo\Contract\CommandInterface` $command
* `timeout($timeout)`   * `param int` $timeout
* `idleTimeout($idleTimeout)`   * `param int` $idleTimeout

## SymfonyCommand


Executes Symfony Command

``` php
<?php
// Symfony Command
$this->taskSymfonyCommand(new \Codeception\Command\Run('run'))
     ->arg('suite','acceptance')
     ->opt('debug')
     ->run();

// Artisan Command
$this->taskSymfonyCommand(new ModelGeneratorCommand())
     ->arg('name', 'User')
     ->run();
?>
```

* `arg($arg, $value)`   * `param string` $arg
* `opt($option, $value = null)` 

## Watch


Runs task when specified file or dir was changed.
Uses Lurker library.

``` php
<?php
$this->taskWatch()
 ->monitor('composer.json', function() {
     $this->taskComposerUpdate()->run();
})->monitor('src', function() {
     $this->taskExec('phpunit')->run();
})->run();
?>
```

* `monitor($paths, $callable)`   * `param string|string[]` $paths

