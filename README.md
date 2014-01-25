RoboTask
====

Modern and simple task runner for PHP. Is aimed to automate common tasks:

* executing daemons (and workers)
* performing cleanups
* building release
* running multiple Symfony Commands
* starting PHP server
* running tests
* writing cross-platform scripts

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

## Example: running Codeception Acceptance Test

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

## Example: Cleaning Logs and Cache

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

## Example: Creating Phar Archive

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