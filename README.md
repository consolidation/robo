**Modern and simple PHP task runner** inspired by Grunt and Rake aimed to automate common tasks:

* executing daemons (and workers)
* performing cleanups
* watching filesystem changes
* running multiple Symfony / Artisan Commands
* starting PHP server
* running tests
* writing cross-platform scripts

What makes Robo different?

* Robo is pure PHP.
* Robo provides clean OOP interface for declaring tasks.
* Robo is very simple and intuitive in use.
* Robo is framework-agnostic.
* Robo uses Symfony Console component but allows you to put all your commands in one file.

## Installing

### Phar

[Download](http://codegyre.github.io/Robo/robo.phar) >

`wget http://codegyre.github.io/Robo/robo.phar`


To install globally put `robo.phar` in `/usr/bin`.

```
sudo chmod +x robo.phar && mv robo.phar /user/bin/robo
```

Now you can use it just like `robo`.

### Composer

* Add `"codegyre/robo": "*"` to `composer.json`.
* Run `composer install`
* Use `vendor/bin/robo` to execute Robo tasks.

## Usage

All tasks are defined as **public methods** in `RoboFile.php`. It can be created by running `robo`.
All protected methods in traits that start with `task` prefix are tasks and can be configured and executed in your tasks.

## Examples

To run test we need to start a server first, and launch a Selenium Server

``` php
<?php
class RoboFile extends \Robo\Tasks
{

    function testAcceptance($seleniumPath = '~/selenium-server-standalone-2.39.0.jar')
    {
       // launches PHP server on port 8000 for web dir
       // server will be executed in background and stopped in the end
       $this->taskServer(8000)
            ->background()
            ->dir('web')
            ->run();

       // running Selenium server in background
        $this->taskExec('java -jar '.$pathToSelenium)
            ->background()
            ->run();

        // loading Symfony Command and running with passed argument
        $this->taskCommand(new \Codeception\Command\Run('run'))
            ->arg('suite','acceptance')
            ->run();
    }
}
?>
```

If you execute `robo` you will see this task added to list of available task with name: `test:acceptance`.
To execute it you shoud run `robo test:acceptance`. You may change path to selenium server by passing new path as a argument:

```
robo test:acceptance "C:\Downloads\selenium.jar"
```

Using `watch` task so you can use it for running tests or building assets.

``` php
<?php
class RoboFile extends \Robo\Tasks {
    use \Robo\Task\Watch;

    function watchComposer()
    {
        // when composer.json changes `composer update` will be executed
        $this->taskWatch()->monitor('composer.json', function() {
            $this->taskComposerUpdate()->run();
        })->run();
    }
}
?>
```


### Example: Cleaning Logs and Cache

``` php
<?php
class RoboFile extends \Robo\Tasks
{
    public function clean()
    {
        $this->taskCleanDir([
            'app/cache'
            'app/logs'
        ])->run();

        $this->taskDeleteDir([
            'web/assets/tmp_uploads',
        ])->run();
    }

?>
```

This task cleans `app/cache` and `app/logs` dirs (ignoring .gitignore and .gitkeep files)
Can be executed by running:

```
robo clean
```

### Example: Creating Phar Archive

This example was extracted from Codeception and simplified:

``` php
function buildPhar()
{
    $pharTask = $this->taskPackPhar('package/codecept.phar')
        ->compress()
        ->stub('package/stub.php');

    $finder = Finder::create()
        ->ignoreVCS(true)
        ->name('*.php')
        ->in('src');

    foreach ($finder as $file) {
        $pharTask->addFile('src/'.$file->getRelativePathname(), $file->getRealPath());
    }

    $finder = Finder::create()->files()
        ->ignoreVCS(true)
        ->name('*.php')
        ->exclude('Tests')
        ->exclude('tests')
        ->in('vendor');

    foreach ($finder as $file) {
        $pharTask->addStripped('vendor/'.$file->getRelativePathname(), $file->getRealPath());
    }

    $pharTask->addFile('autoload.php', 'autoload.php')
        ->addFile('codecept', 'package/bin')
        ->run();

    $code = $this->taskExec('php package/codecept.phar')->run();
    if ($code !== 0) {
        throw new Exception("There was problem compiling phar");
    }
}
```

[This and other example tasks](https://github.com/Codeception/Codeception/blob/2.0-dev/RoboFile.php) can be found in Codeception repo.

### Example: Publishing New Release of Robo

``` php
<?php
    public function release()
    {
        $this->say("Releasing Robo");

        $changelog = $this->taskChangelog()
            ->version(\Robo\Runner::VERSION)
            ->askForChanges()
            ->run();

        if (!$changelog->wasSuccessful()) return false;

        $this->taskGit()
            ->add('CHANGELOG.md')
            ->commit('updated changelog')
            ->push()
            ->run();

        $this->taskGitHubRelease(\Robo\Runner::VERSION)
            ->uri('Codegyre/Robo')
            ->askDescription()
            ->changes($changelog->getData())
            ->run();
    }
}
?>
```

To create new release we run:

```
✗ ./robo release
➜  Releasing Robo
?  Changed in this release:Mered Tasks and Traits to same file
?  Changed in this release:Added Watcher task
?  Changed in this release:Added GitHubRelease task
?  Changed in this release:Added Changelog task
?  Changed in this release:Added ReplaceInFile task
?  Changed in this release:
 [Robo\Task\ChangelogTask] Creating CHANGELOG.md
 [Robo\Task\ReplaceInFileTask] CHANGELOG.md updated
 [Robo\Task\ExecTask] running git add CHANGELOG.md
 [Robo\Task\ExecTask] running git commit -m "updated changelog"
 [Robo\Task\GitHubReleaseTask] {"url":"https://api.github.com/repo...
```

## We need more tasks!

Create your own tasks and send them as Pull Requests or create packages prefixed with `robo-` on Packagist.

## Concepts

Tasks are classes that implement `Robo\TaskInterface` with method `run` defined. Each other method of task should be used for specifying task options and returns `$this` for fluent interface:

Tasks are included into RoboFile with traits. Traits should contain protected methods with `task` prefix that return a new instance of a task.
