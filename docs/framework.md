# Robo as a Framework

There are multiple ways to use and package Robo scripts; a few of the alternatives are presented below.

## Creating a Standalone Phar with Robo

It is possible to create a standalone phar that is implemented with Robo; doing this does not require the RoboFile to be located in the current working directory, or any particular location within your project. To achieve this, first set up your project as shown in the section [Implementing Composer Scripts with Robo](getting-started.md#implementing-composer-scripts-with-robo). Use of the "scripts" section is optional.

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
Create a new file for your Robo commands, e.g. `class RoboFile` in `namespace MyProject\Commands;` in the file `src\Commands\RoboFile.php`.  Optionally, add more task libraries as described in the [extending](extending.md) document.

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

$commandClasses = [ \MyProject\Commands\RoboFile::class ];
$statusCode = \Robo\Robo::run(
    $_SERVER['argv'], 
    $commandClasses, 
    'MyAppName', 
    '0.0.0-alpha0'
);
exit($statusCode);
```
When using Robo as a framework, the Robo file should be included in the autoloader, as Robo does not include a `RoboFile.php` file when used in this mode. Instead, specify the class or classes to load as a parameter to the Robo::run() method. By default, all output will be sent to a Symfony ConsoleOutput() that Robo will create for you. If you would like to use some other OutputInterface to capture the output, it may be specified via an optional fifth parameter.

Use [box-project/box2](https://github.com/box-project/box2) to create a phar for your application.  Note that if you use Robo's taskPackPhar to create your phar, then `\Phar::running()` will always return an empty string due to a bug in this phar builder. If you encounter any problems with this, then hardcode the path to your autoload file.  See the [robo](https://github.com/consolidation-org/Robo/blob/master/robo) script for details.

## Using Multiple RoboFiles in a Standalone Application

It is possible to provide as many command classes as you wish to the Robo `Runner()` constructor. You might wish to separate your Robo command implementations into separate Robo files if you have a lot of commands, or if you wish to group similar commands together in the same source file. If you do this, you can simply add more class references to the `$commandClasses` variable shown above.
```
$commandClasses = [ 
    \MyProject\Commands\BuildCommands::class, 
    \MyProject\Commands\DeployCommands::class 
];
```
If your application has a large number of command files, or if it supports command extensions, then you might wish to use the Command Discovery class to locate your files. The `CommandFileDiscovery` class will use the Symfony Finder class to search for all filenames matching the provided search pattern. It will return a list of class names using the provided base namespace.
``` php
$discovery = new \Consolidation\AnnotatedCommand\CommandFileDiscovery();
$discovery->setSearchPattern('*Command.php');
$commandClasses = $discovery->discover('php/MyProject/Commands', '\MyProject\Commands');
```
Pass the resulting `$commandClasses` to the `Runner()` constructor as shown above.  See the annotated-commands project for more information about the different options that the discovery command takes.

## Using Your Own Dependency Injection Container with Robo (Advanced)

It is also possible to completely replace the Robo application with your own.  To do this, set up your project as described in the sections above, but replace the Robo runner with your own main event loop.

Create the Robo dependency injection container:
```
use League\Container\Container;

$input = new \Symfony\Component\Console\Input\ArgvInput($argv);
$output = new \Symfony\Component\Console\Output\ConsoleOutput();
$conf = new \Robo\Config(); \\ or use your own subclass
$app = new \My\Application();
$container = \Robo\Robo::createDefaultContainer($input, $output, $app, $conf);
```
If you are using League\Container (recommended), then you may simply add and share your own classes to the same container.  If you are using some other DI container, then you should use [delegate lookup](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md#14-additional-feature-delegate-lookup) to combine them.
