# Remote Tasks
## Rsync


Executes rsync in a flexible manner.

``` php
$this->taskRsync()
  ->fromPath('src/')
  ->toHost('localhost')
  ->toUser('dev')
  ->toPath('/var/www/html/app/')
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

* ` fromUser(string $user)` 
* ` fromHost(string $hostname)` 
* ` toUser(string $user)` 
* ` toHost(string $hostname)` 

* `fromPath($path)`  This can either be a full rsync path spec (user@host:path) or just a path.
* `toPath($path)`  This can either be a full rsync path spec (user@host:path) or just a path.
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
* `timeout($seconds)` 
* `humanReadable()` 
* `wholeFile()` 
* `dryRun()` 
* `itemizeChanges()` 
* `excludeVcs()`  Excludes .git/, .svn/ and .hg/ folders.
* `exclude($pattern)` 
* `excludeFrom($file)` 
* `filesFrom($file)` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

## Ssh


Runs multiple commands on a remote server.
Per default, commands are combined with &&, unless stopOnFail is false.

``` php
<?php

$this->taskSshExec('remote.example.com', 'user')
    ->exec('cd /var/www/html')
    ->exec('ls -la')
    ->exec('chmod g+x logs')
    ->run();

```

You can even exec other tasks (which implement CommandInterface):

``` php
$gitTask = $this->taskGitStack()
    ->checkout('master')
    ->pull();

$this->taskSshExec('remote.example.com')
    ->exec('cd /var/www/html/site')
    ->exec($gitTask)
    ->run();
```

* `identityFile($filename)` 
* `port($port)` 
* `forcePseudoTty()` 
* `quiet()` 
* `verbose()` 
* `exec($command)`   * `param string|CommandInterface` $command
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

