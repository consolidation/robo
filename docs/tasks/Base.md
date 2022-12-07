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

* `simulate($context)`

 * `param ` $context
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

 * `param string` $executable
* `exec($command)`

 * `param string|string[]|CommandInterface` $command
* `stopOnFail($stopOnFail = null)`

 * `param bool` $stopOnFail
* `result($result)`

 * `param ` $result
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir

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

 * `param string|\Robo\Contract\CommandInterface` $command
* `waitInterval($waitInterval)`

 * `param int` $waitInterval
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output


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

 * `param string` $arg
* `opt($option, $value = null)`

 * `param ` $option
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output


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

* `monitor($paths, $callable, $events = null)`

 * `param string|string[]` $paths
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

