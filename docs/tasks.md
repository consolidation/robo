# Tasks

## ApiGen


Executes ApiGen command to generate documentation 

``` php
<?php
// ApiGen Command
$this->taskApiGen('./apigen.neon')
     ->templateConfig('vendor/apigen/apigen/templates/bootstrap/config.neon')
     ->wipeout(true)
      ->run();
?>
```









* `config($config)` 
* `source($src)`   * `param array|string|Traversable` $src one or more source values
* `destination($dest)` 
* `extensions($exts)`   * `param array|string` $exts one or more extensions
* `exclude($exclude)`   * `param array|string` $exclude one or more exclusions
* `skipDocPath($path)`   * `param array|string|Traversable` $exts one or more skip-doc-path values
* `skipDocPrefix($prefix)`   * `param array|string|Traversable` $prefix one or more skip-doc-prefix values
* `charset($charset)`   * `param array|string` $charset one or more charsets
* `mainProjectNamePrefix($name)` 
* `title($title)` 
* `baseUrl($baseUrl)` 
* `googleCseId($id)` 
* `googleAnalytics($trackingCode)` 
* `templateConfig($templateConfig)` 
* `allowedHtml($tags)`   * `param array|string` $tags one or more supported html tags
* `groups($groups)` 
* `autocomplete($types)`   * `param array|string` $types or more supported autocomple types
* `accessLevels($levels)`   * `param array|string` $levels one or more access levels 
* `internal($internal)`   * `param boolean|string` $internal 'yes' or true if internal, 'no' or false if not
* `php($php)`   * `param boolean|string` $php 'yes' or true to generate documentation for internal php classes,
* `tree($tree)`   * `param boolean|string` $tree 'yes' or true to generate a tree view of classes, 'no' or false otherwise
* `deprecated($dep)`   * `param boolean|string` $dep 'yes' or true to generate documentation for deprecated classes, 'no' or false otherwise
* `todo($todo)`   * `param boolean|string` $todo 'yes' or true to document tasks, 'no' or false otherwise
* `sourceCode($src)`   * `param boolean|string` $src 'yes' or true to generate highlighted source code, 'no' or false otherwise
* `download($zipped)`   * `param boolean|string` $zipped 'yes' or true to generate downloadable documentation, 'no' or false otherwise
* `report($path)` 
* `wipeout($wipeout)`   * `param boolean|string` $wipeout 'yes' or true to clear out the destination directory, 'no' or false otherwise
* `quiet($quiet)`   * `param boolean|string` $quiet 'yes' or true for quiet, 'no' or false otherwise
* `progressbar($bar)`   * `param boolean|string` $bar 'yes' or true to display a progress bar, 'no' or false otherwise
* `colors($colors)`   * `param boolean|string` $colors 'yes' or true colorize the output, 'no' or false otherwise
* `updateCheck($check)`   * `param boolean|string` $check 'yes' or true to check for updates, 'no' or false otherwise
* `debug($debug)`   * `param boolean|string` $debug 'yes' or true to enable debug mode, 'no' or false otherwise




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed














## Build








* `tag($tag)` 



















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





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

@method Development\Changelog filename(string $filename)
@method Development\Changelog anchor(string $anchor)
@method Development\Changelog version(string $version)




* `askForChanges()` 

* `changes(array $data)` 
* `change($change)` 
* `getChanges()` 





















## CleanDir


Deletes all files from specified dir, ignoring git files.

``` php
<?php
$this->taskCleanDir(['tmp','logs'])->run();
// as shortcut
$this->_cleanDir('app/cache');
?>
```























## Codecept


Executes Codeception tests

``` php
<?php
// config
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




















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed






## Commit










* `name($name)` 



















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





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




















## CopyDir


Copies one dir into another

``` php
<?php
$this->taskCopyDir(['dist/config' => 'config'])->run();
// as shortcut
$this->_copyDir(['dist/config' => 'config']);
?>
```























## DeleteDir


Deletes dir

``` php
<?php
$this->taskDeleteDir('tmp')->run();
// as shortcut
$this->_deleteDir(['tmp', 'log']);
?>
```






















## DumpAutoload


Composer Dump Autoload

``` php
<?php
// simple execution
taskComposerDumpAutoload::_run();

// dump auto loader with custom path
taskComposerDumpAutoload::init('path/to/my/composer.phar')
     ->preferDist()
     ->run();

// optimize autoloader dump with custom path
taskComposerDumpAutoload::init('path/to/my/composer.phar')
     ->optimize()
     ->run();

// optimize autoloader dump with custom path and no dev
taskComposerDumpAutoload::init('path/to/my/composer.phar')
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



















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





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

* `stop()` 



















* `getPrinted()` 

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





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

@method ExecStack exec(string)
@method ExecStack stopOnFail(string)







* `getPrinted()` 

* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command


























## Filesystem


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

// one line
$this->taskFileSystem::_touch('.gitignore');
$this->taskFileSystem::_mkdir('logs');

?>
```

Class FileSystemStackTask
@package Robo\Task

* `stopOnFail($stop = null)` 
* `mkdir($dir)` 
* `touch($file)` 
* `copy($from, $to, $force = null)` 
* `chmod($file, $permissions, $umask = null, $recursive = null)` 
* `remove($file)` 
* `rename($from, $to)` 
* `symlink($from, $to)` 
* `mirror($from, $to)` 
* `chgrp($file, $group)` 
* `chown($file, $user)` 




















## GenerateMarkdownDoc


Simple documentation generator from source files.
Takes classes, properties and methods with their docblocks and writes down a markdown file.

``` php
<?php
$this->taskGenerateMarkdownDoc('models.md')
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
$this->taskGenerateMarkdownDoc('models.md')
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

@method \Robo\Task\Development\GenerateMarkdownDoc docClass(string $classname) put a class you want to be documented
@method \Robo\Task\Development\GenerateMarkdownDoc filterMethods(\Closure $func) using callback function filter out methods that won't be documented
@method \Robo\Task\Development\GenerateMarkdownDoc filterClasses(\Closure $func) using callback function filter out classes that won't be documented
@method \Robo\Task\Development\GenerateMarkdownDoc filterProperties(\Closure $func) using callback function filter out properties that won't be documented
@method \Robo\Task\Development\GenerateMarkdownDoc processClass(\Closure $func) post-process class documentation
@method \Robo\Task\Development\GenerateMarkdownDoc processClassSignature(\Closure $func) post-process class signature. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processClassDocBlock(\Closure $func) post-process class docblock contents. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processMethod(\Closure $func) post-process method documentation. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processMethodSignature(\Closure $func) post-process method signature. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processMethodDocBlock(\Closure $func) post-process method docblock contents. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processProperty(\Closure $func) post-process property documentation. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processPropertySignature(\Closure $func) post-process property signature. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc processPropertyDocBlock(\Closure $func) post-process property docblock contents. Provide *false* to skip.
@method \Robo\Task\Development\GenerateMarkdownDoc reorder(\Closure $func) use a function to reorder classes
@method \Robo\Task\Development\GenerateMarkdownDoc reorderMethods(\Closure $func) use a function to reorder methods in class
@method \Robo\Task\Development\GenerateMarkdownDoc prepend($text) inserts text into beginning of markdown file
@method \Robo\Task\Development\GenerateMarkdownDoc append($text) inserts text in the end of markdown file
























































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

@method \Robo\Task\Vcs\GitHubRelease tag(string $tag)
@method \Robo\Task\Vcs\GitHubRelease name(string $name)
@method \Robo\Task\Vcs\GitHubRelease body(string $body)
@method \Robo\Task\Vcs\GitHubRelease draft(boolean $isDraft)
@method \Robo\Task\Vcs\GitHubRelease prerelease(boolean $isPrerelease)
@method \Robo\Task\Vcs\GitHubRelease comittish(string $branch)











* `askName()` 
* `askDescription()` 
* `askForChanges()` 
* `changes(array $changes)` 

* `uri($uri)` 

* `askAuth()` 





















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

* `getPrinted()` 

* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command

























## Install


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




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Install


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




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Install


Npm Install

``` php
<?php
// simple execution
$this->taskNpmInstall()->run();

// prefer dist with custom path
$this->taskNpmInstall('path/to/my/npm')
     ->noDev()
     ->run();
?>
```







* `noDev()`  adds `production` option to npm




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Minify


Minifies asset file (CSS or JS).

``` php
<?php
$this->taskMinify( 'web/assets/theme.css' )
     ->run()
?>
```
Please install additional dependencies to use:

```
"patchwork/jsqueeze": "~1.0",
"natxet/CssMin": "~3.0"
```





* `to($dst)`  Sets destination. Tries to guess type from it.
* `type($type)`  Sets type with validation.




* `__toString()`  @return string




















## MirrorDir


Mirrors a directory to another

``` php
<?php
$this->taskMirrorDir(['dist/config/' => 'config/'])->run();
// or use shortcut
$this->_mirrorDir(['dist/config/' => 'config/']);

?>
```






















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




















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## PackPhar


Creates Phar

``` php
<?php
$pharTask = $this->PackPhar('package/codecept.phar')
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

   // verify Phar is packed correctly
   $code = $this->_exec('php package/codecept.phar');
?>
```









* `getPrinted()` 

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


@method \Robo\Task\Base\ParallelExec timeout(int $timeout) stops process if it runs longer then `$timeout` (seconds)
@method \Robo\Task\Base\ParallelExec idleTimeout(int $timeout) stops process if it does not output for time longer then `$timeout` (seconds)





* `getPrinted()` 
* `printed($isPrinted = null)` 
* `process($command)` 


























## PhpServer


Runs PHP server and stops it when task finishes.

``` php
<?php
// run server in /public directory
$this->taskPhpServer(8000)
 ->dir('public')
 ->run();

// run with IP 0.0.0.0
$this->taskPhpServer(8000)
 ->host('0.0.0.0')
 ->run();

// execute server in background
$this->taskPhpServer(8000)
 ->background()
 ->run();
?>
```













* `host($host)` 
* `dir($path)`  changes working directory of command

* `background()`  Executes command in background mode (asynchronously)
* `timeout($timeout)`  Stop command if it runs longer then $timeout in seconds
* `idleTimeout($timeout)`  Stops command if it does not output something for a while

* `stop()` 



















* `getPrinted()` 

* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `printed($arg)`  Should command output be printed





## Phpspec


Executes Phpspec tests

``` php
<?php
$this->taskPhpspec()
     ->format('pretty')
     ->noInteraction()
     ->run();
?>
```









* `stopOnFail()` 
* `noCodeGeneration()` 
* `quiet()` 
* `verbose($level = null)` 
* `noAnsi()` 
* `noInteraction()` 
* `config($config_file)` 
* `format($formater)` 




















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Pull

























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Remove

























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## ReplaceInFile


Performs search and replace inside a files.

``` php
<?php
$this->taskReplaceInFile('VERSION')
 ->from('0.2.0')
 ->to('0.3.0')
 ->run();

$this->taskReplaceInFile('README.md')
 ->from(date('Y')-1)
 ->to(date('Y'))
 ->run();

$this->taskReplaceInFile('config.yml')
 ->regex('~^service:~')
 ->to('services:')
 ->run();
?>
```

@method regex(string)
@method from(string)
@method to(string)

























## Result

#### *public static* stopOnFail




* `getCid()` 



* `getData()`  @return array
* `getExitCode()`  @return mixed
* `getMessage()`  @return mixed
* `getExecutionTime()` 
* `getTask()`  @return TaskInterface
* `cloneTask()` 
* `wasSuccessful()` 
* `__invoke()` 
* `stopOnFail()` 



















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

@method \Robo\Task\Remote\Rsync fromUser(string $user)
@method \Robo\Task\Remote\Rsync fromHost(string $hostname)
@method \Robo\Task\Remote\Rsync toUser(string $user)
@method \Robo\Task\Remote\Rsync toHost(string $hostname)












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
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed






## Run









* `getPrinted()` 

* `exec($run)` 
* `volume($from, $to = null)` 
* `env($variable, $value = null)` 
* `publish($port = null)` 
* `containerWorkdir($dir)` 
* `user($user)` 
* `privileged()` 
* `name($name)` 




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Run


Gulp Run

``` php
<?php
// simple execution
$this->taskGulpRun()->run();

// run task 'clean' with --silent option
$this->taskGulpRun('clean')
     ->silent()
     ->run();
?>
```







* `silent()`  adds `silent` option to gulp
* `noColor()`  adds `--no-color` option to gulp
* `color()`  adds `--color` option to gulp
* `simple()`  adds `--tasks-simple` option to gulp




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





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




## Ssh


Runs multiple commands on a remote server.
Per default, commands are combined with &&, unless stopOnFail is false.

``` php
<?php

$this->taskSsh('remote.example.com', 'user')
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

$this->taskSsh('remote.example.com')
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
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Start




























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Stop




























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## SvnStack


Runs Svn commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.

``` php
<?php
$this->taskSvnStack()
 ->checkout('http://svn.collab.net/repos/svn/trunk')
 ->run()

// alternatively
$this->_svnCheckout('http://svn.collab.net/repos/svn/trunk');

$this->taskSvnStack('username', 'password')
 ->stopOnFail()
 ->update()
 ->add('doc/*')
 ->commit('doc updated')
 ->run();
?>
```








* `update($path = null)`  Updates `svn update` command
* `add($pattern = null)`  Executes `svn add` command with files to add pattern
* `commit($message, $options = null)`  Executes `svn commit` command with a message
* `checkout($branch)`  Executes `svn checkout` command
* `getPrinted()` 

* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command


























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




















## TaskInfo




* `getDescription()` 
* `getName()` 
* `getArguments()` 
* `getOptions()` 
* `getHelp()` 
* `getArgumentDescription($name)` 
* `getOptionDescription($name)` 






## Tasks








































































## Update


Bower Update

``` php
<?php
// simple execution
$this->taskBowerUpdate->run();

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




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Update


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




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Update


Npm Update

```php
<?php
// simple execution
$this->taskNpmUpdate()->run();

// prefer dist with custom path
$this->taskNpmUpdate('path/to/my/npm')
     ->noDev()
     ->run();
?>
```







* `noDev()`  adds `production` option to npm




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `getPrinted()` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





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
@method append()



* `line($line)` 
* `lines($lines)` 
* `text($text)` 
* `textFromFile($filename)` 
* `place($name, $val)` 




















