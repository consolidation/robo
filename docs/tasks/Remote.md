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

* `fromPath($path)` This can either be a full rsync path spec (user@host:path) or just a path.
* `toPath($path)` This can either be a full rsync path spec (user@host:path) or just a path.
* `fromUser($fromUser)`
* `fromHost($fromHost)`
* `toUser($toUser)`
* `toHost($toHost)`
* `progress()`
* `stats()`
* `recursive()`
* `verbose()`
* `checksum()`
* `archive()`
* `compress()`
* `owner()`
* `group()`
* `times()`
* `delete()`
* `humanReadable()`
* `wholeFile()`
* `dryRun()`
* `itemizeChanges()`
* `excludeVcs()` Excludes .git, .svn and .hg items at any depth.
* `exclude($pattern)`
* `excludeFrom($file)`
* `includeFilter($pattern)`
* `filter($pattern)`
* `filesFrom($file)`
* `remoteShell($command)`
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

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
* `user($user)`
* `stopOnFail($stopOnFail = true)` Whether or not to chain commands together with && and stop the chain if one command fails.
* `remoteDir($remoteDir)` Changes to the given directory before running commands.
* `identityFile($filename)`
* `port($port)`
* `forcePseudoTty()`
* `quiet()`
* `verbose()`
* `exec($command)`
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


