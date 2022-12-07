# Remote Tasks
## Rsync


Executes rsync in a flexible manner.

``` php
$this->taskRsync()
  ->fromPath('src/')
  ->toHost('localhost')
  ->toUser('dev')
  ->toPath('/var/www/html/app/')
  ->remoteShell('ssh -i public_key')
  ->recursive()
  ->excludeVcs()
  ->checksum()
  ->wholeFile()
  ->verbose()
  ->progress()
  ->humanReadable()
  ->stats()
  ->run();
```

You could also clone the task and do a dry-run first:

``` php
$rsync = $this->taskRsync()
  ->fromPath('src/')
  ->toPath('example.com:/var/www/html/app/')
  ->archive()
  ->excludeVcs()
  ->progress()
  ->stats();

$dryRun = clone $rsync;
$dryRun->dryRun()->run();
if ('y' === $this->ask('Do you want to run (y/n)')) {
  $rsync->run();
}
```

* `fromPath($path)`

 * `param string|array` $path
* `toPath($path)`

 * `param string` $path
* `fromUser($fromUser)`

 * `param string` $fromUser
* `fromHost($fromHost)`

 * `param string` $fromHost
* `toUser($toUser)`

 * `param string` $toUser
* `toHost($toHost)`

 * `param string` $toHost
* `progress()`

 * `return $this`
* `stats()`

 * `return $this`
* `recursive()`

 * `return $this`
* `verbose()`

 * `return $this`
* `checksum()`

 * `return $this`
* `archive()`

 * `return $this`
* `compress()`

 * `return $this`
* `owner()`

 * `return $this`
* `group()`

 * `return $this`
* `times()`

 * `return $this`
* `delete()`

 * `return $this`
* `humanReadable()`

 * `return $this`
* `wholeFile()`

 * `return $this`
* `dryRun()`

 * `return $this`
* `itemizeChanges()`

 * `return $this`
* `excludeVcs()`

 * `return $this`
* `exclude($pattern)`

 * `param array|string` $pattern
* `excludeFrom($file)`

 * `param string` $file
* `includeFilter($pattern)`

 * `param array|string` $pattern
* `filter($pattern)`

 * `param array|string` $pattern
* `filesFrom($file)`

 * `param string` $file
* `remoteShell($command)`

 * `param string` $command
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

## Ssh


Runs multiple commands on a remote server.
Per default, commands are combined with &&, unless stopOnFail is false.

```php
<?php

$this->taskSshExec('remote.example.com', 'user')
    ->remoteDir('/var/www/html')
    ->exec('ls -la')
    ->exec('chmod g+x logs')
    ->run();

```

You can even exec other tasks (which implement CommandInterface):

```php
$gitTask = $this->taskGitStack()
    ->checkout('master')
    ->pull();

$this->taskSshExec('remote.example.com')
    ->remoteDir('/var/www/html/site')
    ->exec($gitTask)
    ->run();
```

You can configure the remote directory for all future calls:

```php
::configure('remoteDir', '/some-dir');
```

* `hostname($hostname)`

 * `param string` $hostname
* `user($user)`

 * `param string` $user
* `stopOnFail($stopOnFail = null)`

 * `param bool` $stopOnFail
* `remoteDir($remoteDir)`

 * `param string` $remoteDir
* `identityFile($filename)`

 * `param string` $filename
* `port($port)`

 * `param int` $port
* `forcePseudoTty()`

 * `return $this`
* `quiet()`

 * `return $this`
* `verbose()`

 * `return $this`
* `exec($command)`

 * `param string|string[]|CommandInterface` $command
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


