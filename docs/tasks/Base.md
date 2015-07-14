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

#### *public static* instances* `background()`  Executes command in background mode (asynchronously)
* `timeout($timeout)`  Stop command if it runs longer then $timeout in seconds
* `idleTimeout($timeout)`  Stops command if it does not output something for a while
* `env(array $env)`  Sets the environment variables for the command
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

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

* `ExecStack exec(string)` 
* `ExecStack stopOnFail(string)` 

* `exec($command)` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

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

* `printed($isPrinted = null)` 
* `process($command)` 

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

* `arg($arg, $value)` 
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

* `monitor($paths, $callable)` 


