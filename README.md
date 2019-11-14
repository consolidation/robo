# RoboTask

**Modern and simple PHP task runner** inspired by Gulp and Rake aimed to automate common tasks:

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/consolidation/Robo?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) 
[![Latest Stable Version](https://poser.pugx.org/consolidation/robo/v/stable.png)](https://packagist.org/packages/consolidation/robo) 
[![Latest Unstable Version](https://poser.pugx.org/consolidation/robo/v/unstable.png)](https://packagist.org/packages/consolidation/robo) 
[![Total Downloads](https://poser.pugx.org/consolidation/robo/downloads.png)](https://packagist.org/packages/consolidation/robo) 
[![PHP 5 supported](https://img.shields.io/badge/PHP%205-supported-92a9ed)](https://travis-ci.org/consolidation/Robo)

[![Build Status](https://travis-ci.org/consolidation/Robo.svg?branch=master)](https://travis-ci.org/consolidation/Robo) 
[![Windows CI](https://ci.appveyor.com/api/projects/status/0823hnh06pw8ir4d?svg=true)](https://ci.appveyor.com/project/greg-1-anderson/robo)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/consolidation/Robo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/consolidation/Robo/?branch=master)
[![License](https://img.shields.io/badge/license-MIT-408677.svg)](LICENSE)

* writing cross-platform scripts
* processing assets (less, sass, minification)
* running tests
* executing daemons (and workers)
* watching filesystem changes
* deployment with sftp/ssh/docker

## Branches

| Branch | Symfony Versions | PHP Versions |
| ------ | ---------------- | ------------ |
| [master](https://github.com/consolidation/robo)      | 4 only    | [![PHP 7 only](https://img.shields.io/badge/PHP%207-only-92a9ed)](https://travis-ci.org/consolidation/Robo) |
| [1.x](https://github.com/consolidation/robotree/1.x) | 2, 3 or 4 | [![PHP 5 supported](https://img.shields.io/badge/PHP%205-supported-408677)](https://travis-ci.org/consolidation/Robo) |

Robo versions 2.x and 1.x are largely compatible; however, Robo 2.x supports fewer versions of PHP and Symfony. This is to make ongoing maintenance easier; Robo 1.x will be supported less and less over time. Projects should update to Robo ^2 if possible.

## Installing

### Phar

[Download robo.phar >](http://robo.li/robo.phar)

```
wget http://robo.li/robo.phar
```

To install globally put `robo.phar` in `/usr/bin`. (`/usr/local/bin/` in OSX 10.11+)

```
chmod +x robo.phar && sudo mv robo.phar /usr/bin/robo
```

OSX 10.11+
```
chmod +x robo.phar && sudo mv robo.phar /usr/local/bin/robo
```

Now you can use it just like `robo`.

### Composer

* Run `composer require consolidation/robo:~1`
* Use `vendor/bin/robo` to execute Robo tasks.

## Usage

All tasks are defined as **public methods** in `RoboFile.php`. It can be created by running `robo init`.
All protected methods in traits that start with `task` prefix are tasks and can be configured and executed in your tasks.

## Examples

The best way to learn Robo by example is to take a look into [its own RoboFile](https://github.com/consolidation/Robo/blob/master/RoboFile.php)
 or [RoboFile of Codeception project](https://github.com/Codeception/Codeception/blob/2.4/RoboFile.php). There are also some basic example commands in `examples/RoboFile.php`.

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

Create your own tasks and send them as Pull Requests or create packages [with `"type": "robo-tasks"` in `composer.json` on Packagist](https://packagist.org/?type=robo-tasks).

## Credits

Follow [@robo_php](http://twitter.com/robo_php) for updates.

Brought to you by [Consolidation Team](https://github.com/orgs/consolidation/people) and our [awesome contributors](https://github.com/consolidation/Robo/graphs/contributors).

## License

[MIT](https://github.com/consolidation/Robo/blob/master/LICENSE)
