RoboTask
====

Modern and simple task runner for PHP aimed to automate common tasks:

* executing daemons (and workers)
* performing cleanups
* building releases
* running multiple Symfony Commands
* starting PHP server
* running tests
* writing cross-platform scripts

Based on Symfony2 Console Component.

## Installing

### Composer

* Add `"codegyre/robo": "*"` to `composer.json`.
* Run `composer install`
* Use `vendor/bin/robo` to execute robo tasks.

## Usage

All tasks are defined as **public methods** in `RoboFile.php`. It can be created by running `robo init`.
RoboFile has a set of predefined tasks taken from `\Robo\Tasks`. All tasks are included with traits.
All protected methods in traits that start with `task` prefix are predefined tasks and can be configured and executed in your tasks.

There are predefined tasks that can be executed from RoboFile

* `taskExec` executes script. Optionally can be started in background.
* `taskServer` starts PHP server. Optionally can be stopped on exit
* `taskCopyDir` copies one dir into another
* `taskCleanDir` empties specified dir
* `taskDeleteDir` removes dir
* `taskCommand` running Symfony Command. *(requires \Robo\Add\Command trait)*
* `taskPackPhar` creating phar archive *(requires \Robo\Add\Command trait)*

### Example: running Codeception Acceptance Test

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

This task cleans `app/cache` and `app/logs` dirs (ignoreing .gitignore and .gitkeep files)
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

[This and other example tasks](https://github.com/Codeception/Codeception/blob/2.0-dev/RoboFile.php). can be found in Codeception repo

### Example: Publishing New Release of Robo

To create a new release new tag should be added and pushed.

``` php
<?php
class Robofile extends \Robo\Tasks
{
    public function release()
    {
        $this->say("Releasing Robo");
        $this->taskExec("git tag")->args(\Robo\Runner::VERSION)->run();
        $this->taskExec("git push origin master --tags")->run();
    }
}
```

To create new release we run:

```
./robo release
➜  Releasing Robo
 [Robo\Task\Exec] running git tag 0.1.0
 [Robo\Task\Exec] running git push origin master --tags
Total 0 (delta 0), reused 0 (delta 0)
To git@github.com:Codegyre/Robo.git
 * [new tag]         0.1.0 -> 0.1.0

```

## API

Tasks are classes that implement `Robo\TaskInterface` with method `run` defined. Each other method of task should be used for specifing task options and returns `$this` for fluent interface:

Tasks are including into RoboFile with traits. Traits should contain protected methods with `task` prefix that return new instance of a task.

See: [Bundled Tasks](https://github.com/Codegyre/Robo/tree/master/src/Task) | [Corresponding Traits](https://github.com/Codegyre/Robo/tree/master/src/Add)