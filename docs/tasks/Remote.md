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

* `fromPath($path)`  This can either be a full rsync path spec (user@host:path) or just a path.
* `toPath($path)`  This can either be a full rsync path spec (user@host:path) or just a path.
* `fromUser($fromUser)`   * `param string` $fromUser
* `fromHost($fromHost)`   * `param string` $fromHost
* `toUser($toUser)`   * `param string` $toUser
* `toHost($toHost)`   * `param string` $toHost
* `progress()`   * `return` $this
* `stats()`   * `return` $this
* `recursive()`   * `return` $this
* `verbose()`   * `return` $this
* `checksum()`   * `return` $this
* `archive()`   * `return` $this
* `compress()`   * `return` $this
* `owner()`   * `return` $this
* `group()`   * `return` $this
* `times()`   * `return` $this
* `delete()`   * `return` $this
* `humanReadable()`   * `return` $this
* `wholeFile()`   * `return` $this
* `dryRun()`   * `return` $this
* `itemizeChanges()`   * `return` $this
* `excludeVcs()`  Excludes .git, .svn and .hg items at any depth.
* `exclude($pattern)`   * `param array|string` $pattern
* `excludeFrom($file)`   * `param string` $file
* `includeFilter($pattern)`   * `param array|string` $pattern
* `filter($pattern)`   * `param array|string` $pattern
* `filesFrom($file)`   * `param string` $file
* `remoteShell($command)`   * `param string` $command
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

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

* `hostname($hostname)`   * `param string` $hostname
* `user($user)`   * `param string` $user
* `stopOnFail($stopOnFail = null)`  Whether or not to chain commands together with && and stop the chain if one command fails.
* `remoteDir($remoteDir)`  Changes to the given directory before running commands.
* `identityFile($filename)`   * `param string` $filename
* `port($port)`   * `param int` $port
* `forcePseudoTty()`   * `return` $this
* `quiet()`   * `return` $this
* `verbose()`   * `return` $this
* `exec($command)`   * `param string|string[]|CommandInterface` $command
* `simulate($context)`  {@inheritdoc}
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

