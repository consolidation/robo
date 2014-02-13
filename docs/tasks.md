

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

* filename(string $filename)
* anchor(string $anchor)
* version(string $version)
* askForChanges()
* changes(Parameter #0 [ <required> array $data ])
* change(Parameter #0 [ <required> $change ])
* getChanges()


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
$this->taskDeleteDir(['tmp', 'log])->run();
?>
```

### ReplaceInFileTask

Performs search and replace inside a files.

``` php
<?php
$this->replaceInFile('VERSION')
 ->from('0.2.0')
 ->to('0.3.0)
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

* regex(string)
* from(string)
* to(string)

### WriteToFileTask
* line(Parameter #0 [ <required> $line ])
* lines(Parameter #0 [ <required> $lines ])
* text(Parameter #0 [ <required> $text ])
* textFromFile(Parameter #0 [ <required> $filename ])
* place(Parameter #0 [ <required> $name ], Parameter #1 [ <required> $val ])



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

* tag(string $tag)
* name(string $name)
* body(string $body)
* draft(boolean $isDraft)
* prerelease(boolean $isPrerelease)
* comittish(string $branch)
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

 $code = $this->taskExec('php package/codecept.phar')->run();
?>
```
* compress(Parameter #0 [ <optional> $compress = true ])
* stub(Parameter #0 [ <required> $stub ])
* addStripped(Parameter #0 [ <required> $path ], Parameter #1 [ <required> $file ])
* addFile(Parameter #0 [ <required> $path ], Parameter #1 [ <required> $file ])


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
