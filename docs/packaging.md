# Packaging

There are multiple ways to use and package Robo scripts; a few of the alternatives are presented below.

## Adding a RoboFile to your Project

In its simplest form, Robo is used by creating a RoboFile.php at the root of your project.

If `robo` is in your $PATH:
```
$ cd myproject
$ robo mycommand
```
Alternately, add `robo` to your composer.json file:
```
$ cd myproject
$ composer require consolidation/Robo:~1
$ ./vendor/bin/robo mycommand
```
There are more examples of this in the [Getting Started](getting-started.md) guide.

## Implementing Composer Scripts with Robo

When using Robo in your project, it is convenient to define Composer scripts that call your Robo commands.  Simply add the following to your composer.json file:
```
{
    "name": "myorg/myproject",
    "require": {
        "consolidation/Robo": "~1"
    },
    "scripts": {
        "test": "composer robo test",
        "phar": "composer robo phar:build",
        "robo": "robo --ansi --load-from $(pwd)/scripts/BuildCommands.php"
    }
}
```
*Note*: When you include Robo as a library like this, some external projects used by certain core Robo tasks are not automatically included in your project.  See the `"suggest":` section of Robo's composer.json for a list of external projects you might also want to require in your project.

Once you have set up your composer.json file (and ran `composer update` if you manually changed the `require` or `require-dev` sections), Composer will ensure that your project-local copy of Robo in the `vendor/bin` dir is in your $PATH when you run the additional Composer scripts that you declared:
```
$ cd myproject
$ composer test
$ composer phar
```
This will call the public methods `test()` and `phar()` in your RoboFile.php when using `composer test` and `composer phar`, respectively.

Advertising your build commands as Composer scripts is a useful way to provide the key commands used for testing, building or packaging your application. Also, if your application should happen to provide a commandline tool to perform the operations of the application itself, then defining your build commands in their own RoboFile provides desirable separation, keeping your build commands out of the help and list commands of your primary script.

If you would like to simplify the output of your script (e.g. when running on a CI service), replace the `--ansi` option in the example above with `--no-ansi`, and  colored terminal output and progress bars will be disabled. 

## Creating a Standalone Phar with Robo

It is also possible to create a standalone phar that is implemented with Robo, and does not require the RoboFile to be located in the current working directory. To achieve this, first set up your project as shown in the section "Implementing Composer Scripts with Robo". Use of the "scripts" section is optional.

Next, add an "autoload" section to your composer.json to provide a namespace for your Robo commands:
```
{
    "name": "myorg/myproject",
    "require": {
        "consolidation/Robo": "~1"
    },
    "autoload":{
        "psr-4":{
            "MyProject\\":"src"
        }
    }
}
```
Create a new file for your Robo commands, e.g. `class RoboCommands` in `namespace MyProject\Commands;` in the file `src\Commands\RoboCommands.php`.  Optionally, add more task libraries as described in the [extending](extending.md) document.

Create a startup script similar to the one below, and add it to the root of your project, or some other location of your choosing:

``` php
#!/usr/bin/env php
<?php

/**
 * If we're running from phar load the phar autoload file.
 */
$pharPath = \Phar::running(true);
if ($pharPath) {
    require_once "$pharPath/vendor/autoload.php";
} else {
    if (file_exists(__DIR__.'/vendor/autoload.php')) {
        require_once __DIR__.'/vendor/autoload.php';
    } elseif (file_exists(__DIR__.'/../../autoload.php')) {
        require_once __DIR__ . '/../../autoload.php';
    }
}

$commandClasses = [ \MyProject\Commands\RoboCommands::class ];
$runner = new \Robo\Runner($commandClasses);
$statusCode = $runner->execute($_SERVER['argv']);
exit($statusCode);
```
Use [box-project/box2](https://github.com/box-project/box2) to create a phar for your application.  Note that if you use Robo's taskPackPhar to create your phar, then `\Phar::running()` will always return an empty string due to a bug in this phar builder. If you encounter any problems with this, then hardcode the path to your autoload file.  See the [robo](https://github.com/consolidation-org/Robo/blob/master/robo) script for details.

## Using Multiple RoboFiles in a Standalone Application

It is possible to provide as many command classes as you wish to the Robo `Runner()` constructor. If your application has a large number of command files, or if it supports command extensions, then you might wish to use the Command Discovery class to locate your applications.
``` php
$discovery = new \Consolidation\AnnotatedCommand\CommandFileDiscovery();
$discovery->setSearchPattern('*Command.php');
$commandClasses = $discovery->discover('php/Terminus/AnnotatedCommands', '\Terminus\AnnotatedCommands');
```
Pass the resulting `$commandClasses` to the `Runner()` constructor as shown above.  See the annotated-commands project for more information about the different options that the discovery command takes.

## Using Your Own Dependency Injection Container with Robo (Advanced)

It is also possible to completely replace the Robo application with your own.  To do this, set up your project as described in the sections above, but replace the Robo runner with your own main event loop.

Create the Robo dependency injection container:
```
use League\Container\Container;

$output = new \Symfony\Component\Console\Output\ConsoleOutput();
$container = \Robo\Robo::createDefaultContainer($input, $output);
```
If you are using League\Container (recommended), then you may simply add and share your own classes to the same container.  If you are using some other DI container, then you should use [delegate lookup](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md#14-additional-feature-delegate-lookup) to combine them.
