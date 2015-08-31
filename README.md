# RoboTask

**Modern and simple PHP task runner** inspired by Gulp and Rake aimed to automate common tasks:

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/Codegyre/Robo?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Build Status](https://travis-ci.org/Codegyre/Robo.svg?branch=master)](https://travis-ci.org/Codegyre/Robo) [![Latest Stable Version](https://poser.pugx.org/codegyre/robo/v/stable.png)](https://packagist.org/packages/codegyre/robo) [![Total Downloads](https://poser.pugx.org/codegyre/robo/downloads.png)](https://packagist.org/packages/codegyre/robo) [![Latest Unstable Version](https://poser.pugx.org/codegyre/robo/v/unstable.png)](https://packagist.org/packages/codegyre/robo) [![License](https://poser.pugx.org/codegyre/robo/license.png)](https://packagist.org/packages/codegyre/robo)

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
sudo chmod +x robo.phar && mv robo.phar /usr/bin/robo
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

The best way to learn Robo by example is to take a look into [its own RoboFile](https://github.com/Codegyre/Robo/blob/master/RoboFile.php)
 or [RoboFile of Codeception project](https://github.com/Codeception/Codeception/blob/master/RoboFile.php)

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

---

Cleaning logs and cache

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

Created by Michael Bodnarchuk [@davert](http://twitter.com/davert).

## License

MIT
