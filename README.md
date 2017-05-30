# RoboTask

**Modern and simple PHP task runner** inspired by Gulp and Rake aimed to automate common tasks:

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/consolidation-org/Robo?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) 
[![Latest Stable Version](https://poser.pugx.org/consolidation/robo/v/stable.png)](https://packagist.org/packages/consolidation/robo) 
[![Latest Unstable Version](https://poser.pugx.org/consolidation/robo/v/unstable.png)](https://packagist.org/packages/consolidation/robo) 
[![Total Downloads](https://poser.pugx.org/consolidation/robo/downloads.png)](https://packagist.org/packages/consolidation/robo) 
[![PHP 7 ready](http://php7ready.timesplinter.ch/consolidation/Robo/badge.svg)](https://travis-ci.org/consolidation/Robo)
[![License](https://poser.pugx.org/consolidation/robo/license.png)](https://www.versioneye.com/user/projects/57c4a6fe968d64004d97620a?child=57c4a6fe968d64004d97620a#tab-licenses)

[![Build Status](https://travis-ci.org/consolidation/Robo.svg?branch=master)](https://travis-ci.org/consolidation/Robo) 
[![Windows CI](https://ci.appveyor.com/api/projects/status/0823hnh06pw8ir4d?svg=true)](https://ci.appveyor.com/project/greg-1-anderson/robo)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/consolidation/Robo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation/Robo/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/57c4a6fe968d64004d97620a/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57c4a6fe968d64004d97620a)

* writing cross-platform scripts
* processing assets (less, sass, minification)
* running tests
* executing daemons (and workers)
* watching filesystem changes
* deployment with sftp/ssh/docker

## Installing

### Phar

[Download robo.phar >](http://robo.li/robo.phar)

```
wget http://robo.li/robo.phar
```

To install globally put `robo.phar` in `/usr/bin`.

```
chmod +x robo.phar && sudo mv robo.phar /usr/bin/robo
```

Now you can use it just like `robo`.

### Composer

* Run `composer require consolidation/robo:~1`
* Use `vendor/bin/robo` to execute Robo tasks.

## Usage

All tasks are defined as **public methods** in `RoboFile.php`. It can be created by running `robo`.
All protected methods in traits that start with `task` prefix are tasks and can be configured and executed in your tasks.

## Examples

The best way to learn Robo by example is to take a look into [its own RoboFile](https://github.com/consolidation-org/Robo/blob/master/RoboFile.php)
 or [RoboFile of Codeception project](https://github.com/Codeception/Codeception/blob/master/RoboFile.php). There are also some basic example commands in examples/RoboFile.php.

Here are some snippets from them:

---

Run acceptance test with local server and selenium server started.


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
        $this->taskExec('java -jar ' . $seleniumPath)
            ->background()
            ->run();

        // loading Symfony Command and running with passed argument
        $this->taskSymfonyCommand(new \Codeception\Command\Run('run'))
            ->arg('suite','acceptance')
            ->run();
    }
}
```

If you execute `robo` you will see this task added to list of available task with name: `test:acceptance`.
To execute it you should run `robo test:acceptance`. You may change path to selenium server by passing new path as a argument:

```
robo test:acceptance "C:\Downloads\selenium.jar"
```

Using `watch` task so you can use it for running tests or building assets.

``` php
<?php
class RoboFile extends \Robo\Tasks {

    function watchComposer()
    {
        // when composer.json changes `composer update` will be executed
        $this->taskWatch()->monitor('composer.json', function() {
            $this->taskComposerUpdate()->run();
        })->run();
    }
}
```

---

Cleaning logs and cache

``` php
<?php
class RoboFile extends \Robo\Tasks
{
    public function clean()
    {
        $this->taskCleanDir([
            'app/cache',
            'app/logs'
        ])->run();

        $this->taskDeleteDir([
            'web/assets/tmp_uploads',
        ])->run();
    }
}
```

This task cleans `app/cache` and `app/logs` dirs (ignoring .gitignore and .gitkeep files)
Can be executed by running:

```
robo clean
```

----

Creating Phar archive

``` php
function buildPhar()
{
    $files = Finder::create()->ignoreVCS(true)->files()->name('*.php')->in(__DIR__);
    $packer = $this->taskPackPhar('robo.phar');
    foreach ($files as $file) {
        $packer->addFile($file->getRelativePathname(), $file->getRealPath());
    }
    $packer->addFile('robo','robo')
        ->executable('robo')
        ->run();
}
```

---

## We need more tasks!

Create your own tasks and send them as Pull Requests or create packages prefixed with `robo-` on Packagist.

## Credits

Follow [@robo_php](http://twitter.com/robo_php) for updates.

Brought to you by [Consolidation Team](https://github.com/orgs/consolidation/people) and our [awesome contributors](https://github.com/consolidation/Robo/graphs/contributors).

## License

[MIT](https://github.com/consolidation/Robo/blob/master/LICENSE)
