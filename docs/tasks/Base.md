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

* `simulate($context)` {@inheritdoc}
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

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

* `executable($executable)`
* `exec($command)`
* `stopOnFail($stopOnFail = true)`
* `result($result)`
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

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

* `process($command)`
* `waitInterval($waitInterval)` Parallel processing will wait `$waitInterval` seconds after launching each process and before
* `setOutput($output)` Sets the Console Output.


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
* `setOutput($output)` Sets the Console Output.


## Watch


Runs task when specified file or dir was changed.
Uses Lurker library.
Monitor third parameter takes Lurker filesystem events types to watch.
By default its set to MODIFY event.

``` php
<?php
$this->taskWatch()
     ->monitor(
         'composer.json',
         function() {
             $this->taskComposerUpdate()->run();
         }
     )->monitor(
         'src',
         function() {
             $this->taskExec('phpunit')->run();
         },
         \Lurker\Event\FilesystemEvent::ALL
     )->monitor(
         'migrations',
         function() {
             //do something
         },
         [
             \Lurker\Event\FilesystemEvent::CREATE,
             \Lurker\Event\FilesystemEvent::DELETE
         ]
     )->run();
?>
```

Pass through the changed file to the callable function

```
$this
 ->taskWatch()
 ->monitor(
     'filename',
     function ($event) {
         $resource = $event->getResource();
         ... do something with (string)$resource ...
     },
     FilesystemEvent::ALL
 )
 ->run();
```

The $event parameter is a [standard Symfony file resource object](https://api.symfony.com/3.1/Symfony/Component/Config/Resource/FileResource.html)

* `monitor($paths, $callable, $events = 2)`
* `setOutput($output)` Sets the Console Output.

