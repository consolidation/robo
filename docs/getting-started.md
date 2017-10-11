# Getting Started

To begin you need to create a RoboFile. Just run `robo init` in your project directory:

```
cd myproject
robo init
```

Your project directory may start out empty; Robo will create a new `RoboFile.php` for you. There will be RoboFile class which extends `\Robo\Tasks`, which includes all bundled tasks of Robo.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
}
?>
```

## Commands

All public methods of the RoboFile class will be treated as **commands**. You can run them from the CLI and pass arguments.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
    function hello($world)
    {
        $this->say("Hello, $world");
    }
}
?>
```

When we run:

```
robo hello davert
➜ Hello, davert
```

Method names should be camelCased. In CLI `camelCased` method will be available as `camel:cased` command.
`longCamelCased` method will be transformed to `long:camel-cased` command.

**Note:** This assumes you have installed Robo by downloading the [robo.phar](http://robo.li/robo.phar) file and copied it to a directory in your `$PATH`. For example, `cp robo.phar ~/bin/robo`.

### Arguments

All method parameters without default values are treated as required arguments. In our example command `hello` requires one argument.
If you pass a default value to parameter the argument becomes optional:

``` php
<?php
    function hello($world = 'world')
    {
        $this->say("Hello, $world");
    }
?>
```

```
robo hello
➜ Hello, world
```

To accept multiple, variable arguments, define a parameter as an `array`; Robo will then pass all CLI arguments in this variable:

``` php
<?php
    function hello(array $world)
    {
        $this->say("Hello, " . implode(', ', $world));
    }
?>
```

```
robo hello davert jon bill bob
➜ Hello, davert, jon, bill, bob
```

### Options

To define command options you should define the last method parameter as an associative array where the keys define the option names and the values provide each option's default values:

``` php
<?php
    function hello($opts = ['silent' => false])
    {
        if (!$opts['silent']) $this->say("Hello, world");
    }
?>
```

```
robo hello
➜ Hello, world

robo hello --silent
```

A one-character shortcut can be specified for option:

``` php
<?php
    function hello($opts = ['silent|s' => false])
    {
        if (!$opts['silent']) $this->say("Hello, world");
    }
?>
```

Now command can be executed with '-s' to run in silent mode: 

```
robo hello -s
```

The default value for options must be one of:

- The boolean value `false`, which indicates that the option takes no value.
- A **string** containing the default value for options that may be provided a value, but are not required to.
- NULL for options that may be provided an optional value, but that have no default when a value is not provided.
- The special value InputOption::VALUE_REQUIRED, which indicates that the user must provide a value for the option whenever it is used.
- An empty array, which indicates that the option may appear multiple times on the command line.

No other values should be used for the default value. For example, `$options = ['a' => 1]` is **incorrect**; instead, use `$options = ['a' => '1']`. Similarly, `$options = ['a' => true]` is unsupported, or at least not useful, as this would indicate that the value of `--a` was always `true`, whether or not it appeared on the command line.

### Load From Other Directories

Robo can execute commands from a RoboFile located in different directory.
You can specify the path to another RoboFile by including the `--load-from` option:

```
robo run --load-from /path/to/my/other/project
```

### Pass-Through Arguments

Sometimes you need to pass arguments from your command into a task. A command line after the `--` characters is treated as one argument.
Any special character like `-` will be passed into without change.

``` php
<?php
    function ls(array $args)
    {
        $this->taskExec('ls')->args($args)->run();
    }
?>
```

```
robo ls -- Robo -c --all
 [Robo\Task\ExecTask] running ls Robo -c --all
 .  ..  CHANGELOG.md  codeception.yml  composer.json  composer.lock  docs  .git  .gitignore  .idea  LICENSE  README.md  robo  RoboFile.php  robo.phar  src  tests  .travis.yml  vendor
```

### Help

The help text for a command in a RoboFile may be provided in Doc-Block comments. An example help Doc-Block comment is shown below:

``` php
<?php
/**
 * Calculate the fibonacci sequence between two numbers.
 *
 * Graphic output will look like
 *     +----+---+-------------+
 *     |    |   |             |
 *     |    |-+-|             |
 *     |----+-+-+             |
 *     |        |             |
 *     |        |             |
 *     |        |             |
 *     +--------+-------------+
 *
 * @param int $start Number to start from
 * @param int $steps Number of steps to perform
 * @param array $opts
 * @option $graphic Display the sequence graphically using cube
 *                  representation
 */
public function fibonacci($start, $steps, $opts = ['graphic' => false])
{
}
?>
```

The corresponding help text produced is:

```
robo fibonacci --help
Usage:
 fibonacci [--graphic] start steps

Arguments:
 start                 Number to start from
 steps                 Number of steps to perform

Options:
 --graphic             Display the sequence graphically using cube representation

Help:
 Graphic output will look like
     +----+---+-------------+
     |    |   |             |
     |    |-+-|             |
     |----+-+-+             |
     |        |             |
     |        |             |
     |        |             |
     +--------+-------------+
```

Arguments and options are populated from annotations.

Initially added with [PR by @jonsa](https://github.com/consolidation/Robo/pull/71); now provided by the [consolidation/annotated-command](https://github.com/consolidation/annotated-command) project, which was factored out from Robo.

### Ignored methods

Robo ignores any method of your RoboFile that begins with `get` or `set`. These methods are presumed to be data accessors, not commands.  To implement a command whose name contains `get` or `set`, use the `@command` annotation.

``` php
<?php
    /**
     * @command set-alignment
     */
    function setAlignment($value)
    {
        ...
    }
?>
```

## Tasks

Robo commands typically divide the work they need to accomplish into **tasks**. The command first determines what needs to be done, inspecting current state if necessary, and then sets up and executes one or more tasks that make the actual changes needed by the command.  (See also the documentation on [Collections](collections.md), which allow you to combine groups of tasks which can provide rollback functions to recover from failure situations.)

For details on how to add custom tasks to Robo, see the [extending](extending.md) document.

### Shortcuts

Some tasks may have shortcuts. If a task does not require multi-step configuration, it can be executed with a single line:
 
```php
<?php
$this->_exec('ps aux');
$this->_copy('config/env.example.yml','config/env.yml');
?>
```

### Result

Each task must return an instance of `Robo\Result`. A Robo Result contains the task instance, exit code, message, and any variable data that the task may wish to return.

*Note*: A task may also return `NULL` or an array as a shortcut for a successful result. In this instance, Robo will convert the value into a `Robo\Result`, and will apply the provided array values, if any, to the result's variable data. This practice is supported, but not recommended.

The `run` method of `CompileAssets` class may look like this:

```
return new Robo\Result($this, $exitCode, "Assets compiled");
```

or

```
return Robo\Result::success($this, "Assets compiled");
return Robo\Result::error($this, "Failed to compile assets");
```

You can use this results to check if execution was successful, either using the `wasSuccessful()` method, or via the `invoke` shortcut. We will use the `Exec` task in next example to illustrate this:

``` php
<?php
class RoboFile
{
    use Robo\Task\Base\loadShortcuts;

    function test()
    {
        $res1 = $this->_exec('phpunit tests/integration');
        $res2 = $this->_exec('phpunit tests/unit');

        // print message when tests passed
        if ($res1->wasSuccessful() and $res2->wasSuccessful()) $this->say("All tests passed");
    }
}
?>
```
When making multi-step commands that call one task after another, it is best to use a collection to group the tasks together. The collection will handle error detection and rollback, and will return a single Result object when done. For more information, see the [Collections](collections.md) documentation.

Some tasks may also attach data to the Result object.  If this is done, the data may be accessed as an array; for example, `$result['path'];`. This is not common.

Commands should return a Result object obtained from a task; this will ensure that the command exit code is set correctly.  If a command does not have a Result object available, then it may use a ResultData object.  ResultData objects are just like Result objects, except the do not contain a reference to a task.

return new Robo\ResultData($exitcode, 'Error message.');

If the command returns a TaskInterface instead of a result, then the task will be executed, and the result from that task will be used as the final result of the command. See also `Formatters`, below.

### Stack

Some tasks contain `Stack` in their name. These are called "stack" tasks, and they execute similar tasks one after the other.  Each of the primary methods in a stack class executes an operation.

Stack tasks also contain a `stopOnFail` method which can be used to stop task execution if one of its commands was unsuccessful.

### Global StopOnFail

There is a global `stopOnFail` method as well, that can be used to stop a command on first failure of a task.

```
$this->stopOnFail(true);
```

### Progress

Robo supports progress indicators via the Symfony ProgressBar class.  Long-running tasks that wish to display the progress indicator may do so via four simple steps:

- Override the `progressIndicatorSteps()` method and return the number of "steps" in the operation.
- Call `$this->startProgressIndicator()` to begin the progress indicator running.
- Call `$this->advanceProgressIndicator()` a number of times equal to the result returned by `progressIndicatorSteps()`
- Call `$this->stopProgressIndicator()` when the operation is completed.

An example of this is shown below:

``` php
<?php
class MyTask extends BaseTask
{
    protected $steps = 10;
    
    public function progressIndicatorSteps()
    {
        return $this->steps;
    }
    
    public function run()
    {
        $exitCode = 0;
        $errorMessage = "";
    
        $this->startProgressIndicator();
        for ($i = 0; $i < $this->steps; ++$i) {
            $this->advanceProgressIndicator();
        }
        $this->stopProgressIndicator();

        return new Result($this, $exitCode, $errorMessage, ['time' => $this->getExecutionTime()]);
    }
}
?>
```
Tasks should not attempt to use a specific progress indicator (e.g. the Symfony ProgressBar class) directly, as the ProgressIndicatorAwareTrait allows for an appropriate progress indicator to be used (or omitted) as best suits the application.

Note that when using [Collections](collections.md), the progress bar will automatically be shown if the collection takes longer than two seconds to run.  Each task in the collection will count for one "step"; if the task supports progress indicators as shown above, then it will add an additional number of steps as indicated by its `progressIndicatorSteps()` method.

## Configuration

On startup, Robo will load a configuration file, `robo.yml`, if it exists in the current working directory.

**Note:** The configuration features below are experimental. Changes that break compatibility may be introduced until it is declared stable in the 1.1.0 release.

### Configuration for Command Options

The preferred method for commands to use to read configuration is to simply define commandline options for each configuration value. Configuration may be provided for any command option in the `robo.yml` configuration file.

For example, given the following Robo command:

``` php
<?php
    function hello($opts = ['who' => 'unknown'])
    {
        $this->say("Hello, " . $opts['who']);
    }
?>
```

The `who` option can be defined as follows:

```
command:
  hello:
    options:
      who: world
```

If you run this command, then it will print `Hello, world`. If the `--who` option is provided on the command line, that value will take precidence over the value stored in configuration. Thus, `hello --who=everyone` will print `Hello, everyone`.

Command groups may also share configuration options. For example, if you have commands `foo:bar`, `foo:baz` and `foo:boz`, all of which share a common option `color`, then the following configuration will provide the value `blue` to `foo:bar` and `foo:baz`, and the value `green` to `foo:boz`:

```
command:
  foo:
    options:
      color: blue
    boz:
      options:
        color: green
```

### Configuration for Task Settings

Robo will automatically configure tasks with values from configuration. For example, given the following task definition:
```
$this->taskMyOperation()
  ->dir($buildDir)
  ->extrapolated(false)
  ->run();
```
You could instead remove the setter methods and move the parameter values to a configruation file:
```
$this->taskComposerInstall()
  ->run();
```
Then, presuming that `taskMyOperation` was implemented in a class `\MyOrg\Task\TaskGroup\MyOperation`, then the corresponding configuration file would appear as follows:
```
task:
  TaskGroup:
    MyOperation:
      settings:
        dir: /my/path
        extrapolated: false
```
The key for configuration-injected settings is `task.PARTIAL_NAMESPACE.CLASSNAME.settings.key`. PARTIAL_NAMESPACE is the namespace for the class, with each `\` replaced with a `.`, and with each component of the namespace up to and including `Task` removed.

Tasks in the same namespace may also share configuration-injected settings. For example, the configuration below will set the `dir` option of any task implemented by a class in the `*\TaskGroup\MyOperation` namespace, unless the task has a more specific configuration value stored with its classname:
```
task:
  TaskGroup:
    settings:
      dir: /my/path
      extrapolated: false
```

### Accessing Configuration Directly

In a RoboFile, use `Robo::Config()->get('task.TaskGroup.MyOperation.settings.dir');` to fetch the `dir` configuration option from the previous example.

In the implementation of `taskMyOperation()` itself, it is in general not necessary to access configuration values directly, as it is preferable to allow Robo to inject configuration as described above. However, if desired, configuration may be accessed from within the method of any task that extends `\Robo\Task\BaseTask` (or otherwise uses `ConfigAwareTrait`) may do so via `static::getConfigValue('key', 'default');`.

### Providing Default Configuration in Code

RoboFiles that wish to provide default configuration values that can be overridden via robo.yml values or commandline options may do so in the class' constructor method.  The example below demonstrates how to set up a default value for the `task.Ssh.remoteDir` configuration property in code:
```
class RoboFile
{
    public function __construct()
    {    
        Robo\Task\Remote\Ssh::configure('remoteDir', '/srv/www');
    }
}
```
If `task.Remote.Ssh.remoteDir` is set to some other value in the robo.yml configuration file in the current directory, then the value from the configuration file will take precedence.

### Loading Configuration From Another Source

Sometimes, a RoboFile might want to define its own private configuration file to use in addition to the standard `robo.yml` file. This can also be done in the constructor.
```
class RoboFile
{
    public function __construct()
    {    
        Robo::loadConfiguration([__DIR__ . '/myconf.yml']);
    }
}
```
Note that configuration loaded in this way will take precedence over the configuration loaded by default by Robo.

It is possible to have even more control than this if you [create your own application using Robo as a Framework](framework.md).

## IO

As you noticed, you can print text via the `say` method, which is taken from the `Robo\Output` trait.

```
$this->say("Hello");
```

Also, you can ask for input from console:

```
$name = $this->ask("What is your name?");
```

There are also `askDefault`, `askHidden`, and `confirm` methods.

In addition, Robo makes all of the methods of Symfony Style available through the `io()` method:
```
$this->io()->title("Build all site assets");
```

This allows Robo scripts to follow the [Symfony Console Style Guide](http://symfony.com/blog/new-in-symfony-2-8-console-style-guide) if desired.

### Formatters

It is preferable for commands that look up and display information should avoid doing IO directly, and should instead return the data they wish to display as an array. This data can then be converted into different data formats, such as "table" and "json". The user may select which formatter to use via the --format option. For details on formatters, see the [consolidation/output-formatters](https://github.com/consolidation/output-formatters) project.

## Working with Composer

### Adding a RoboFile to your Project

Robo is designed to work well with Composer. To use Robo scripts in your Composer-based project, simply add `robo` to your composer.json file:
```
$ cd myproject
$ composer require consolidation/Robo:~1
$ ./vendor/bin/robo mycommand
```
If you do not want to type the whole path to Robo, you may add `./vendor/bin` to your `$PATH` (relative paths work), or use `composer exec` to find and run Robo:
```
$ composer exec robo mycommand
```

### Implementing Composer Scripts with Robo

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

Once you have set up your composer.json file (and ran `composer update` if you manually changed the `require` or `require-dev` sections), Composer will ensure that your project-local copy of Robo in the `vendor/bin` dir is in your `$PATH` when you run the additional Composer scripts that you declared:
```
$ cd myproject
$ composer test
$ composer phar
```
This will call the public methods `test()` and `phar()` in your RoboFile.php when using `composer test` and `composer phar`, respectively.

Advertising your build commands as Composer scripts is a useful way to provide the key commands used for testing, building or packaging your application. Also, if your application should happen to provide a commandline tool to perform the operations of the application itself, then defining your build commands in their own RoboFile provides desirable separation, keeping your build commands out of the help and list commands of your primary script.

If you would like to simplify the output of your script (e.g. when running on a CI service), replace the `--ansi` option in the example above with `--no-ansi`, and  colored terminal output and progress bars will be disabled. 

## Robo as a Framework

For an overview on how to turn your Robo scripts into standalone tools, see the example [robo.script](https://github.com/consolidation/Robo/blob/master/examples/robo.script), and the section [Robo as a Framework](framework.md).
