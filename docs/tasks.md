# Tasks


### ComposerInstallTask

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
* preferDist()

* preferSource()

* noDev()

### ComposerUpdateTask

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
@package Robo\Task
* preferDist()

* preferSource()

* noDev()


### ChangelogTask

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

@method \Robo\Task\ChangelogTask filename(string $filename)
@method \Robo\Task\ChangelogTask anchor(string $anchor)
@method \Robo\Task\ChangelogTask version(string $version)
* askForChanges()

* changes(Parameter #0 [ <required> array $data ])

* change(Parameter #0 [ <required> $change ])

* getChanges()

### GenMarkdownDocTask

Simple documentation generator from source files.
Takes docblocks from classes and methods and generates markdown file.

``` php
$this->taskGenDoc('models.md')
     ->docClass('Model\User')
     ->docClass('Model\Post')
     ->filterMethods(function(\ReflectionMethod $r) {
         return $r->isPublic(); // process only public methods
     })->processClass(function(\ReflectionClass $r, $text) {
         return "Class ".$r->getName()."\n\n$text\n\n###Methods\n";
     })->run();
```

@method \Robo\Task\GenMarkdownDocTask docClass(string $classname)
@method \Robo\Task\GenMarkdownDocTask filterMethods(\Closure $func)
@method \Robo\Task\GenMarkdownDocTask filterClasses(\Closure $func)
@method \Robo\Task\GenMarkdownDocTask processMethod(\Closure $func)
@method \Robo\Task\GenMarkdownDocTask processClass(\Closure $func)
@method \Robo\Task\GenMarkdownDocTask reorder(\Closure $func)
@method \Robo\Task\GenMarkdownDocTask reorderMethods(\Closure $func)
@method \Robo\Task\GenMarkdownDocTask prepend($text)
@method \Robo\Task\GenMarkdownDocTask append($text)
* indentDoc(Parameter #0 [ <required> $doc ], Parameter #1 [ <optional> $indent = 3 ])


### ExecTask

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
* background()

* arg(Parameter #0 [ <required> $arg ])

* args(Parameter #0 [ <required> $args ])

* stop()


### RequireTask

Requires php file to be executed inside a closure.

``` php
<?php
$this->taskRequire('script/create_users.php')->run();
$this->taskRequire('script/make_admin.php')
 ->locals(['user' => $user])
 ->run();
?>
```
* local(Parameter #0 [ <required> array $locals ])


### CleanDirTask

Deletes all files from specified dir, ignoring git files.

``` php
<?php
$this->taskCleanDir('app/cache')->run();
$this->taskCleanDir(['tmp','logs'])->run();
?>
```

### CopyDirTask

Copies one dir into another

``` php
<?php
$this->taskCopyDir(['dist/config' => 'config'])->run();
?>
```

### DeleteDirTask

Deletes dir

``` php
<?php
$this->taskDeleteDir('tmp')->run();
$this->taskDeleteDir(['tmp', 'log'])->run();
?>
```

### ReplaceInFileTask

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

@method \Robo\Task\ReplaceInFileTask regex(string)
@method \Robo\Task\ReplaceInFileTask from(string)
@method \Robo\Task\ReplaceInFileTask to(string)

### WriteToFileTask

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
@method \Robo\Task\WriteToFileTask append()
* line(Parameter #0 [ <required> $line ])

* lines(Parameter #0 [ <required> $lines ])

* text(Parameter #0 [ <required> $text ])

* textFromFile(Parameter #0 [ <required> $filename ])

* place(Parameter #0 [ <required> $name ], Parameter #1 [ <required> $val ])


### GitStackTask

Runs Git commands in stack

``` php
<?php
$this->taskGit()
 ->add('-A')
 ->commit('adding everything')
 ->push('origin','master')
 ->run()

$this->taskGit()
 ->add('doc/*')
 ->commit('doc updated')
 ->push()
 ->run();
?>
```
* cloneRepo(Parameter #0 [ <required> $repo ], Parameter #1 [ <optional> $to = '' ])

* add(Parameter #0 [ <required> $pattern ])

* commit(Parameter #0 [ <required> $message ], Parameter #1 [ <optional> $options = '' ])

* pull(Parameter #0 [ <optional> $origin = '' ], Parameter #1 [ <optional> $branch = '' ])

* push(Parameter #0 [ <optional> $origin = '' ], Parameter #1 [ <optional> $branch = '' ])

* checkout(Parameter #0 [ <required> $branch ])



### GitHubReleaseTask

Publishes new GitHub release.

``` php
<?php
$this->taskGitHubRelease('0.1.0')
  ->uri('Codegyre/Robo')
  ->askDescription()
  ->run();
?>
```

@method \Robo\Task\GitHubReleaseTask tag(string $tag)
@method \Robo\Task\GitHubReleaseTask name(string $name)
@method \Robo\Task\GitHubReleaseTask body(string $body)
@method \Robo\Task\GitHubReleaseTask draft(boolean $isDraft)
@method \Robo\Task\GitHubReleaseTask prerelease(boolean $isPrerelease)
@method \Robo\Task\GitHubReleaseTask comittish(string $branch)
* askName()

* askDescription()

* askForChanges()

* changes(Parameter #0 [ <required> array $changes ])

* uri(Parameter #0 [ <required> $uri ])

* askAuth()


### PackPharTask

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
* compress(Parameter #0 [ <optional> $compress = true ])

* stub(Parameter #0 [ <required> $stub ])

* addStripped(Parameter #0 [ <required> $path ], Parameter #1 [ <required> $file ])

* addFile(Parameter #0 [ <required> $path ], Parameter #1 [ <required> $file ])

* executable(Parameter #0 [ <required> $file ])


### PhpServerTask

Runs PHP server and stops it when task finishes.

``` php
<?php
$this->taskServer(8000)
 ->dir('public')
 ->run();
?>
```
* dir(Parameter #0 [ <required> $path ])

* background()

* arg(Parameter #0 [ <required> $arg ])

* args(Parameter #0 [ <required> $args ])

* stop()


### SymfonyCommandTask

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
* arg(Parameter #0 [ <required> $arg ], Parameter #1 [ <required> $value ])

* opt(Parameter #0 [ <required> $option ], Parameter #1 [ <optional> $value = NULL ])


### WatchTask

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
* monitor(Parameter #0 [ <required> $paths ], Parameter #1 [ <required> Closure $callable ])

