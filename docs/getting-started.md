# Getting Started

To begin you need to create a Robofile. Just run `robo init` in empty dir:

```
robo init
```

This will create a new `RoboFile.php` for you. There will be RoboFile class which extends `\Robo\Tasks`, which includes all bundled tasks of Robo.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
}
?>
```

## Commands

All public methods of this class will be treated as **commands**. You can run them from the CLI and pass arguments.

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
    function ls($args)
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
 * @param $steps int Number of steps to perform
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

Added with [PR by @jonsa](https://github.com/Codegyre/Robo/pull/71) 

## Tasks

The convention used to add new tasks for use in your RoboFiles is to create a wrapper trait that instantiates the implementation class for each task. Each task method in the trait should start with the prefix `task`, and should use **chained method calls** for configuration. Task execution should be triggered by the method `run`. 

*It is recommended to have store your trait loading task in a `loadTasks` file in the same namespace as the task implementation.*

A very basic task is shown below:

``` php
<?php
namespace CompileAssets;

trait loadTasks
{
    function taskCompileAssets($path)
    {
        return new CompileAssetsTask($path);
    }
}

class CompileAssetsTask implements Robo\Contract\TaskInterface
{
    // configuration params
    protected $path;
    protected $to;
    function __construct($path)
    {
        $this->path = $path;
    }

    function to($filename)
    {
        $this->to = $filename;
        // must return $this
        return $this;
    }

    // must implement Run
    function run()
    {
        //....
    }
}
?>
```
To use it in a RoboFile, you should include this task via its trait:

``` php
<?php
class RoboFile extends Robo\Tasks
{
    use CompileAssets\loadTasks;

    function build()
    {
        $this->taskCompileAssets('web/css-src')
            ->to('web/assets.min.css')
            ->run();
    }
}
?>
```

Robo\Tasks includes all of the standard task traits by default, so a RoboFile may call the `$this->taskXXX` method for any of these tasks. To use an external task, ensure that its class files are available (e.g. `require` its project in your composer.json file) and include corresponding trait or traits.

## Shortcuts

Some tasks may have shortcuts. If a task does not require multi-step configuration, it can be executed with a single line:
 
```php
<?php
$this->_exec('ps aux');
$this->_copy('config/env.example.yml','config/env.yml');
?>
```

## Result

Each task must return an instance of `Robo\Result`. A Robo Result contains the task instance, exit code, message, and any variable data that the task may wish to return.

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

        // alternatively
        if ($res1() and $res2()) $this->say("All tests passed");

        return $res() and $res(); // will exit with 1 if tests failed
    }
}
?>
```

Some tasks may also attach data to the Result object.  If this is done, the data may be accessed as an array; for example, `$result['path'];`. This is not common.

### Stack

Some tasks contain `Stack` in their name. These are called "stack" tasks, and they execute similar tasks one after the other.  Each of the primary methods in a stack class executes an operation.

Stack tasks also contain a `stopOnFail` method which can be used to stop task execution if one of its commands was unsuccessful.

### Global StopOnFail

There is a global `stopOnFail` method as well, that can be used to stop a command on first failure of a task.

```
$this->stopOnFail(true);
```

## Output

As you noticed, you can print text via the `say` method, which is taken from the `Robo\Output` trait.

```
$this->say("Hello");
```

Also, you can ask for input from console:

```
$name = $this->ask("What is your name?");
```

There are also `askDefault`, `askHidden`, and `confirm` methods.

Inside tasks you should print process details with `printTaskInfo`, ``printTaskSuccess`, and `printTaskError`.

To allow tasks access IO, use the `Robo\Common\TaskIO` trait, or inherit your task class from `Robo\Task\BaseTask` (recommended).

```
$this->printTaskInfo('Processing...');
```
