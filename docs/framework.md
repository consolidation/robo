# Robo as a Framework

There are multiple ways to use and package Robo scripts; a few of the alternatives are presented below.

## Creating a Standalone Phar with Robo

It is possible to create a standalone phar that is implemented with Robo; doing this does not require the RoboFile to be located in the current working directory, or any particular location within your project. To achieve this, first set up your project as shown in the section [Implementing Composer Scripts with Robo](getting-started.md#implementing-composer-scripts-with-robo). Use of the "scripts" section is optional.

Next, add an "autoload" section to your composer.json to provide a namespace for your Robo commands:
```json
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

$output = new \Symfony\Component\Console\Output\ConsoleOutput();

$commandClasses = [ \MyProject\Commands\RoboFile::class ];
$statusCode = \Robo\Robo::run(
    $_SERVER['argv'], 
    $commandClasses, 
    'MyAppName', 
    '0.0.0-alpha0',
    $output,
    'org/project'
);
exit($statusCode);
```
When using Robo as a framework, the Robo file should be included in the autoloader, as Robo does not include a `RoboFile.php` file when used in this mode. Instead, specify the class or classes to load as a parameter to the Robo::run() method. By default, all output will be sent to a Symfony ConsoleOutput() that Robo will create for you. If you would like to use some other OutputInterface to capture the output, it may be specified via an optional fifth parameter.

Use [box-project/box2](https://github.com/box-project/box2) or Robo's taskPackPhar to create a phar for your application. If your application's repository is hosted on GitHub, then passing the appropriate GitHub `org/project` to the `\Robo\Robo::run()` method, as shown above, will enable the `self:update` command to automatically update to the latest available version. Note that `self:update` only works with phar distributions.

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

Add the following to your startup file:
```php
<?php
use League\Container\Container;
use Robo\Robo;

$input = new \Symfony\Component\Console\Input\ArgvInput($argv);
$output = new \Symfony\Component\Console\Output\ConsoleOutput();
$config = Robo::createConfiguration(['myconf.yml']);
$app = new \MyApplication($config, $input, $output);
$status_code = $app->run($input, $output);
exit($status_code);

```

Then, create your own custom application:

```php
<?php

use Robo\Common\ConfigAwareTrait;
use Robo\Config;
use Robo\Robo;
use Robo\Runner as RoboRunner;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MyApplication {

  const APPLICATION_NAME = 'My Application';
  const REPOSITORY = 'org/project';

  use ConfigAwareTrait;

  private $runner;

  public function __construct(
    Config $config,
    InputInterface $input = NULL,
    OutputInterface $output = NULL
  ) {

    // Create applicaton.
    $this->setConfig($config);
    $application = new Application(self::APPLICATION_NAME, $config->get('version'));

    // Create and configure container.
    $container = Robo::createDefaultContainer($input, $output, $application,
      $config);
    $this->setContainer($container);
    $container->add(MyCustomService::class);

    // Instantiate Robo Runner.
    $this->runner = new RoboRunner([
      My\Custom\Command::class
    ]);
    $this->runner->setContainer($container);
    $this->runner->setSelfUpdateRepository(self::REPOSITORY);
  }

  public function run(InputInterface $input, OutputInterface $output) {
    $status_code = $this->runner->run($input, $output);

    return $status_code;
  }

}
```

If you are using League\Container (recommended), then you may simply add and share your own classes to the same container.  If you are using some other DI container, then you should use [delegate lookup](https://github.com/container-interop/fig-standards/blob/master/proposed/container.md#14-additional-feature-delegate-lookup) to combine them.

## Using a Custom Configuration Loader

Robo provides a very simple configuration loader. If you wish to use more capable loader, you may opt to do so. Replace the call to `Robo::createConfiguration()` with code similar to the following:
```
use Robo\Config\Config;
use Consolidation\Config\Loader\YamlConfigLoader;
use Consolidation\Config\Loader\ConfigProcessor;

$config = new Config();
$loader = new YamlConfigLoader();
$processor = new ConfigProcessor();
$processor->extend($loader->load('defaults.yml'));
$processor->extend($loader->load('myconf.yml'));
$config->import($processor->export());
```
You may also wish to subclass the provided `Config` and `ConfigProcessor` classes to customize their behavior.

The example above presumes that the configuration object starts off empty. If you need to repeat this process to extend the configuration in a later stage, you should call `$processor->add($config->export());` to ensure that the configuration processor is seeded with the previous configuration values.

Any configuraiton loader that produces a nested array may be used in place of the config loaders and config processor shown in the example above. For example, if you wish to find configuration files in a certain set of directories, allow .yml or .xml configuration files, and validate the schema of your configuration files (to alert users of any syntax errors or unrecognized configuration values), you might want to consider [Symfony/Config](http://symfony.com/doc/current/components/config/definition.html). Symfony/Config produces a clean array of configuration values; the result of `$processor->processConfiguration()` may be provided directly to Robo's `$config->import()` method.
