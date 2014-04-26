# Tasks


## Robo\Task\ComposerInstallTask


Composer Install

``` php
<?php
// simple execution
$this->taskComposerInstall()->run();

// prefer dist with custom path
$this->taskComposerInstall('path/to/my/composer.phar')
     ->preferDist()
     ->run();
?>
```

## Robo\Task\ComposerUpdateTask


Composer Update

``` php
<?php
// simple execution
$this->taskComposerUpdate()->run();

// prefer dist with custom path
$this->taskComposerUpdate('path/to/my/composer.phar')
     ->preferDist()
     ->run();
?>
```


## Robo\Task\ChangelogTask


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

*  filename(string $filename)
*  anchor(string $anchor)
*  version(string $version)

## Robo\Task\GenMarkdownDocTask


Simple documentation generator from source files.
Takes classes, properties and methods with their docblocks and writes down a markdown file.

``` php
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
$this->taskGenDoc('models.md')
     ->docClass('Model\User')
     })->processClassDocBlock(function(\ReflectionClass $r, $text) {
         return "[This is part of application model]\n" . $text;
     ))->processMethodSignature(function(\ReflectionMethod $r, $text) {
         return "#### {$r->name}()";
     ))->processMethodDocBlock(function(\ReflectionMethod $r, $text) {
         return strpos($r->name, 'save')===0 ? "[Saves to the database]\n" . $text : $text;
     })->run();
```

*  docClass(string $classname)
*  filterMethods(\Closure $func)
*  filterClasses(\Closure $func)
*  filterProperties(\Closure $func)
*  processClass(\Closure $func)
*  processClassSignature(\Closure $func)
*  processClassDocBlock(\Closure $func)
*  processMethod(\Closure $func)
*  processMethodSignature(\Closure $func)
*  processMethodDocBlock(\Closure $func)
*  reorder(\Closure $func)
*  reorderMethods(\Closure $func)
*  prepend($text)
*  append($text)


## Robo\Task\ExecTask


Executes shell script. Closes it when running in background mode.
Initial code from https://github.com/tiger-seo/PhpBuiltinServer by tiger-seo

``` php
<?php
$this->taskExec('compass')->arg()->run();

$this->taskExec('compass watch')->background()->run();

if ($this->taskExec('phpunit .')->run()->wasSuccessful()) {
 $this->say('tests passed');
}
?>
```

## Robo\Task\ExecStackTask


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

*  exec(string)
*  stopOnFail(string)


## Robo\Task\RequireTask


Requires php file to be executed inside a closure.

``` php
<?php
$this->taskRequire('script/create_users.php')->run();
$this->taskRequire('script/make_admin.php')
 ->locals(['user' => $user])
 ->run();
?>
```


## Robo\Task\CleanDirTask


Deletes all files from specified dir, ignoring git files.

``` php
<?php
$this->taskCleanDir('app/cache')->run();
$this->taskCleanDir(['tmp','logs'])->run();
?>
```

## Robo\Task\CopyDirTask


Copies one dir into another

``` php
<?php
$this->taskCopyDir(['dist/config' => 'config'])->run();
?>
```

## Robo\Task\DeleteDirTask


Deletes dir

``` php
<?php
$this->taskDeleteDir('tmp')->run();
$this->taskDeleteDir(['tmp', 'log'])->run();
?>
```

## Robo\Task\ReplaceInFileTask


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

*  regex(string)
*  from(string)
*  to(string)

## Robo\Task\WriteToFileTask


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
*  append()


## Robo\Task\GitStackTask


Runs Git commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.

``` php
<?php
$this->taskGit()
 ->stopOnFail()
 ->add('-A')
 ->commit('adding everything')
 ->push('origin','master')
 ->run()

$this->taskGit()
 ->stopOnFail()
 ->add('doc/*')
 ->commit('doc updated')
 ->push()
 ->run();
?>
```



## Robo\Task\GitHubReleaseTask


Publishes new GitHub release.

``` php
<?php
$this->taskGitHubRelease('0.1.0')
  ->uri('Codegyre/Robo')
  ->askDescription()
  ->run();
?>
```

*  tag(string $tag)
*  name(string $name)
*  body(string $body)
*  draft(boolean $isDraft)
*  prerelease(boolean $isPrerelease)
*  comittish(string $branch)


## Robo\Task\PHPUnitTask



## Robo\Task\PackPharTask


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


## Robo\Task\ParallelExecTask


Class ParallelExecTask
@package Robo\Task

*  timeout(int $timeout)
*  idleTimeout(int $timeout)


## Robo\Task\PhpServerTask


Runs PHP server and stops it when task finishes.

``` php
<?php
$this->taskServer(8000)
 ->dir('public')
 ->run();
?>
```


## Robo\Task\SymfonyCommandTask


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


## Robo\Task\WatchTask


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

