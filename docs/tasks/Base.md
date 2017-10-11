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

* `simulate($context)`  {@inheritdoc}
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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

* `executable($executable)`   * `param string` $executable
* `exec($command)`   * `param string|string[]` $command
* `stopOnFail($stopOnFail = null)`   * `param bool` $stopOnFail
* `result($result)` 
* `dir($dir)`  Changes working directory of command

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

* `process($command)`   * `param string|\Robo\Contract\CommandInterface` $command
* `waitInterval($waitInterval)`  Parallel processing will wait `$waitInterval` seconds after launching each process and before

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

