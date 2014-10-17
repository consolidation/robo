# Tasks


## BowerInstall


Bower Install

``` php
<?php
// simple execution
$this->taskBowerInstall()->run();

// prefer dist with custom path
$this->taskBowerInstall('path/to/my/bower')
     ->noDev()
     ->run();
?>
```





* `allowRoot()`  adds `allow-root` option to bower
* `forceLatest()`  adds `force-latest` option to bower
* `noDev()`  adds `production` option to bower
* `offline()`  adds `offline` option to bower


* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter







## BowerUpdate


Bower Update

``` php
<?php
// simple execution
$this->taskBowerUpdate()->run();

// prefer dist with custom path
$this->taskBowerUpdate('path/to/my/bower')
     ->noDev()
     ->run();
?>
```





* `allowRoot()`  adds `allow-root` option to bower
* `forceLatest()`  adds `force-latest` option to bower
* `noDev()`  adds `production` option to bower
* `offline()`  adds `offline` option to bower


* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter








## Codeception


Executes Codeception tests

``` php
<?php
$this->taskCodecept()
     ->suite('acceptance')
     ->env('chrome')
     ->group('admin')
     ->xml()
     ->html()
     ->run();
?>
```







* `suite($suite)` 
* `test($testName)` 
* `group($group)`  set group option. Can be called multiple times
* `excludeGroup($group)` 
* `json($file = null)`  generate json report
* `xml($file = null)`  generate xml JUnit report
* `html($dir = null)`  Generate html report
* `tap($file = null)`  generate tap report
* `configFile($file)`  provides config file other then default `codeception.yml` with `-c` option
* `coverage()`  turn on collecting code coverage
* `silent()`  execute in silent mode
* `coverageXml($xml = null)`  collect code coverage in xml format. You may pass name of xml file to save results
* `coverageHtml($html = null)`  collect code coverage and generate html report. You may pass
* `env($env)` 
* `debug()` 









* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter


## ComposerInstall


Composer Install

``` php
<?php
// simple execution
$this->taskComposerInstall()->run();

// prefer dist with custom path
$this->taskComposerInstall('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader with custom path
$this->taskComposerInstall('path/to/my/composer.phar')
     ->optimizeAutoloader()
     ->run();
?>
```








* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer









* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
## ComposerUpdate


Composer Update

``` php
<?php
// simple execution
$this->taskComposerUpdate()->run();

// prefer dist with custom path
$this->taskComposerUpdate('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader with custom path
$this->taskComposerUpdate('path/to/my/composer.phar')
     ->optimizeAutoloader()
     ->run();
?>
```








* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer









* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
## ComposerDumpAutoload


Composer Update

``` php
<?php
// simple execution
$this->taskComposerDumpAutoload()->run();

// dump auto loader with custom path
$this->taskComposerDumpAutoload('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader dump with custom path
$this->taskComposerDumpAutoload('path/to/my/composer.phar')
     ->optimize()
     ->run();

// optimize autoloader dump with custom path and no dev
$this->taskComposerDumpAutoload('path/to/my/composer.phar')
     ->optimize()
     ->noDev()
     ->run();
?>
```








* `optimize()` 


* `preferDist()`  adds `prefer-dist` option to composer
* `preferSource()`  adds `prefer-source` option to composer
* `noDev()`  adds `no-dev` option to composer
* `optimizeAutoloader()`  adds `optimize-autoloader` option to composer








* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

## Concat


Merges files into one. Used for preparing assets.

``` php
<?php
$this->taskConcat([
     'web/assets/screen.css',
     'web/assets/print.css',
     'web/assets/theme.css'
 ])
 ->to('web/assets/style.css')
 ->run()
?>
```


* `to($dst)`  set the destination file









## Changelog


Helps to manage changelog file.
Creates or updates `changelog.md` file with recent changes in current version.

``` php
<?php
$version = "0.1.0";
$this->taskChangelog()
 ->version($version)
 ->change("released to github")
 ->run();
?>
```

Changes can be asked from Console

``` php
<?php
$this->taskChangelog()
 ->version($version)
 ->askForChanges()
 ->run();
?>
```

* `filename(string $filename)` 
* `anchor(string $anchor)` 
* `version(string $version)` 



* `askForChanges()` 

* `changes(array $data)` 
* `change($change)` 
* `getChanges()` 

















## GenMarkdownDoc


Simple documentation generator from source files.
Takes classes, properties and methods with their docblocks and writes down a markdown file.

``` php
<?php
$this->taskGenDoc('models.md')
     ->docClass('Model\User') // take class Model\User
     ->docClass('Model\Post') // take class Model\Post
     ->filterMethods(function(\ReflectionMethod $r) {
         return $r->isPublic() or $r->isProtected(); // process public and protected methods
     })->processClass(function(\ReflectionClass $r, $text) {
         return "Class ".$r->getName()."\n\n$text\n\n###Methods\n";
     })->run();
```

By default this task generates a documentation for each public method of a class.
It combines method signature with a docblock. Both can be post-processed.

``` php
<?php
$this->taskGenDoc('models.md')
     ->docClass('Model\User')
     ->processClassSignature(false) // false can be passed to not include class signature
     ->processClassDocBlock(function(\ReflectionClass $r, $text) {
         return "[This is part of application model]\n" . $text;
     })->processMethodSignature(function(\ReflectionMethod $r, $text) {
         return "#### {$r->name}()";
     })->processMethodDocBlock(function(\ReflectionMethod $r, $text) {
         return strpos($r->name, 'save')===0 ? "[Saves to the database]\n" . $text : $text;
     })->run();
```

* `docClass(string $classname)`  put a class you want to be documented
* `filterMethods(\Closure $func)`  using callback function filter out methods that won't be documented
* `filterClasses(\Closure $func)`  using callback function filter out classes that won't be documented
* `filterProperties(\Closure $func)`  using callback function filter out properties that won't be documented
* `processClass(\Closure $func)`  post-process class documentation
* `processClassSignature(\Closure $func)`  post-process class signature. Provide *false* to skip.
* `processClassDocBlock(\Closure $func)`  post-process class docblock contents. Provide *false* to skip.
* `processMethod(\Closure $func)`  post-process method documentation. Provide *false* to skip.
* `processMethodSignature(\Closure $func)`  post-process method signature. Provide *false* to skip.
* `processMethodDocBlock(\Closure $func)`  post-process method docblock contents. Provide *false* to skip.
* `processProperty(\Closure $func)`  post-process property documentation. Provide *false* to skip.
* `processPropertySignature(\Closure $func)`  post-process property signature. Provide *false* to skip.
* `processPropertyDocBlock(\Closure $func)`  post-process property docblock contents. Provide *false* to skip. 
* `reorder(\Closure $func)`  use a function to reorder classes
* `reorderMethods(\Closure $func)`  use a function to reorder methods in class
* `prepend($text)`  inserts text into beginning of markdown file
* `append($text)`  inserts text in the end of markdown file

















































## SemVer


Helps to maintain `.semver` file.

```php
<?php
$this->taskSemVer('.semver')
     ->increment()
     ->run();
?>
```






* `__toString()` 
* `setFormat($format)` 
* `setMetadataSeparator($separator)` 
* `setPrereleaseSeparator($separator)` 
* `increment($what = null)` 
* `prerelease($tag = null)` 
* `metadata($data)` 




## Exec


Executes shell script. Closes it when running in background mode.

``` php
<?php
$this->taskExec('compass')->arg()->run();

$this->taskExec('compass watch')->background()->run();

if ($this->taskExec('phpunit .')->run()->wasSuccessful()) {
 $this->say('tests passed');
}
?>
```









* `background()`  Executes command in background mode (asynchronously)
* `timeout($timeout)`  Stop command if it runs longer then $timeout in seconds
* `idleTimeout($timeout)`  Stops command if it does not output something for a while

* `stop()` 









* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

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

* `exec(string)` 
* `stopOnFail(string)` 






* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command







## CleanDir


Deletes all files from specified dir, ignoring git files.

``` php
<?php
$this->taskCleanDir('app/cache')->run();
$this->taskCleanDir(['tmp','logs'])->run();
?>
```










## CopyDir


Copies one dir into another

``` php
<?php
$this->taskCopyDir(['dist/config' => 'config'])->run();
?>
```










## DeleteDir


Deletes dir

``` php
<?php
$this->taskDeleteDir('tmp')->run();
$this->taskDeleteDir(['tmp', 'log'])->run();
?>
```










## MirrorDir


Mirrors a directory to another

``` php
<?php
$this->taskMirrorDir(['dist/config/' => 'config/'])->run();
?>
```










## ReplaceInFile


Performs search and replace inside a files.

``` php
<?php
$this->replaceInFile('VERSION')
 ->from('0.2.0')
 ->to('0.3.0')
 ->run();

$this->replaceInFile('README.md')
 ->from(date('Y')-1)
 ->to(date('Y'))
 ->run();

$this->replaceInFile('config.yml')
 ->regex('~^service:~')
 ->to('services:')
 ->run();
?>
```

* `regex(string)` 
* `from(string)` 
* `to(string)` 













## WriteToFile


Writes to file

``` php
<?php
$this->taskWriteToFile('blogpost.md')
     ->line('-----')
     ->line(date('Y-m-d').' '.$title)
     ->line('----')
     ->run();
?>
```
* `append()` 



* `line($line)` 
* `lines($lines)` 
* `text($text)` 
* `textFromFile($filename)` 
* `place($name, $val)` 









## Require


Requires php file to be executed inside a closure.

``` php
<?php
$this->taskRequire('script/create_users.php')->run();
$this->taskRequire('script/make_admin.php')
 ->locals(['user' => $user])
 ->run();
?>
```


* `local(array $locals)` 

## FileSystemStack


Wrapper for [Symfony FileSystem](http://symfony.com/doc/current/components/filesystem.html) Component.
Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.

``` php
<?php
$this->taskFileSystemStack()
     ->mkdir('logs')
     ->touch('logs/.gitignore')
     ->chgrp('www', 'www-data')
     ->symlink('/var/log/nginx/error.log', 'logs/error.log')
     ->run();
?>
```

Class FileSystemStackTask
@package Robo\Task

* `stopOnFail($stop = null)` 
* `mkdir($dir)` 
* `touch($file)` 
* `copy($from, $to, $force = null)` 
* `chmod($file, $permissions, $recursive = null)` 
* `remove($file)` 
* `rename($from, $to)` 
* `symlink($from, $to)` 
* `mirror($from, $to)` 
* `chgrp($file, $group)` 
* `chown($file, $user)` 









## GitStack


Runs Git commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.

``` php
<?php
$this->taskGitStack()
 ->stopOnFail()
 ->add('-A')
 ->commit('adding everything')
 ->push('origin','master')
 ->run()

$this->taskGitStack()
 ->stopOnFail()
 ->add('doc/*')
 ->commit('doc updated')
 ->push()
 ->run();
?>
```






* `cloneRepo($repo, $to = null)`  Executes `git clone`
* `add($pattern)`  Executes `git add` command with files to add pattern
* `commit($message, $options = null)`  Executes `git commit` command with a message
* `pull($origin = null, $branch = null)`  Executes `git pull` command.
* `push($origin = null, $branch = null)`  Executes `git push` command
* `checkout($branch)`  Executes `git checkout` command


* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command













## GitHubRelease


Publishes new GitHub release.

``` php
<?php
$this->taskGitHubRelease('0.1.0')
  ->uri('Codegyre/Robo')
  ->askDescription()
  ->run();
?>
```

* `tag(string $tag)` 
* `name(string $name)` 
* `body(string $body)` 
* `draft(boolean $isDraft)` 
* `prerelease(boolean $isPrerelease)` 
* `comittish(string $branch)` 











* `askName()` 
* `askDescription()` 
* `askForChanges()` 
* `changes(array $changes)` 

* `uri($uri)` 

* `askAuth()` 










## PHPUnit


Runs PHPUnit tests

``` php
<?php
$this->taskPHPUnit()
 ->group('core')
 ->bootstrap('test/bootstrap.php')
 ->run()

?>
```




* `filter($filter)` 
* `group($group)` 
* `excludeGroup($group)` 
* `json($file = null)`  adds `log-json` option to runner
* `xml($file = null)`  adds `log-xml` option
* `tap($file = null)` 
* `bootstrap($file)` 
* `configFile($file)` 
* `debug()` 









* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

## PackPhar


Creates Phar

``` php
<?php
$pharTask = $this->taskPackPhar('package/codecept.phar')
  ->compress()
  ->stub('package/stub.php');

 $finder = Finder::create()
     ->name('*.php')
       ->in('src');

   foreach ($finder as $file) {
       $pharTask->addFile('src/'.$file->getRelativePathname(), $file->getRealPath());
   }

   $finder = Finder::create()->files()
       ->name('*.php')
       ->in('vendor');

   foreach ($finder as $file) {
       $pharTask->addStripped('vendor/'.$file->getRelativePathname(), $file->getRealPath());
   }
   $pharTask->run();

   $code = $this->taskExec('php package/codecept.phar')->run();
?>
```








* `compress($compress = null)`   * `param boolean` $compress
* `stub($stub)`   * `param` $stub

* `addStripped($path, $file)` 
* `addFile($path, $file)` 
* `executable($file)` 









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


* `timeout(int $timeout)`  stops process if it runs longer then `$timeout` (seconds)
* `idleTimeout(int $timeout)`  stops process if it does not output for time longer then `$timeout` (seconds)



* `printed($isPrinted = null)` 
* `process($command)` 












## PhpServer


Runs PHP server and stops it when task finishes.

``` php
<?php
$this->taskServer(8000)
 ->dir('public')
 ->run();
?>
```








* `dir($path)`  changes working directory of command

* `background()`  Executes command in background mode (asynchronously)
* `timeout($timeout)`  Stop command if it runs longer then $timeout in seconds
* `idleTimeout($timeout)`  Stops command if it does not output something for a while

* `stop()` 









* `printed($arg)`  Should command output be printed

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

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

* `fromUser(string $user)` 
* `fromHost(string $hostname)` 
* `toUser(string $user)` 
* `toHost(string $hostname)` 









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



* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter









## SshExec


Runs multiple commands on a remote server.
Per default, commands are combined with &&, unless stopOnFail is false.

``` php
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















* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter

## SymfonyCommand


Executes Symsony Command

``` php
<?php
// Symfony Command
$this->taskCommand(new \Codeception\Command\Run('run'))
     ->arg('suite','acceptance')
     ->opt('debug')
     ->run();

// Artisan Command
$this->taskCommand(new ModelGeneratorCommand())
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








