# Getting Started

To begin you need to create a Robofile. Just run `robo` in empty dir:

```
robo
```

You will be asked to create a file. New `RoboFile.php` extends `\Robo\Tasks` class. It includes all bundled tasks from traits.
It's not necessary for your RoboFile to extend `\Robo\Tasks` so if you need to customize tasks inclusion do not inherit from it.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
}
?>
```

## Commands

All public methods of this class will be treated as **commands**. You can run them from CLI and pass arguments.

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

Methods should be camelCased. In CLI `camelCased` method will be available as `camel:cased` command.
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

If you define parameter as `array` you can accept multiple arguments:


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

To define command options you should define last method parameter as an associative array where keys are options and values are default values:

``` php
<?php
    function hello($opts = ['silent' => false])
    {
        if (!$opt['silent']) $this->say("Hello, world");
    }
?>
```

```
robo hello
➜ Hello, world

robo hello --silent
```

A one-char shortcut can be specified for option:

``` php
<?php
    function hello($opts = ['silent|s' => false])
    {
        if (!$opt['silent']) $this->say("Hello, world");
    }
?>
```

Now command can be executed with '-s' to run in silent mode: 

```
robo hello -s
```


### Pass-Through Arguments

Sometimes you need to pass arguments from you command into a task. A command line after the `--` characters is treated as one argument.
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

Doc-Block comments can be used to display help per commands. It turns

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

into

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

All tasks are loaded by Traits. There is convention when all tasks should start with `task` prefix and use **chained method calls** for configuration.
Task execution should be triggered by method `run`. A very basic task may look like:

``` php
<?php
trait CompileAssets
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
To use it you should include this task via trait:

``` php
<?php
class RoboFile extends Robo\Tasks
{
    use CompileAssets;

    function build()
    {
        $this->taskCompileAssets('web/css-src')
            ->to('web/assets.min.css')
            ->run();
    }
}
?>
```

You can use various preinstalled tasks in the same way. Just include corresponding trait and call the `$this->taskXXX` method.

*It is recommended to have store trait loading task in `loadTasks` file of the same namespace*

## Shortcuts

Some tasks may have shortcuts. If task does not require multi-step configuration it can be executed with a one line:
 
```php
<?php
$this->_exec('ps aux');
$this->_copy('config/env.example.yml','config/env.yml');
?>
```

## Result

Each task should provide `Robo\Result` class in response. It contains task instance, exit code, message, and some data.
The `run` method of `CompileAssets` class may look like this:

```
return new Robo\Result($this, $exitCode, "Assets compiled");
```

or

```
return Robo\Result::success($this, "Assets compiled");
return Robo\Result::error($this, "Failed to compile assets");
```

Thus you can use this results to check if execution was successful, and use some data from them. Lets use `Exec` task in next example:

``` php
<?php
class RoboFile
{
    use Robo\Output;
    use Robo\Task\Exec;

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

### Stack

There are tasks with a `Stack` name inside it. They execute similar tasks one by one.
They contain option `stopOnFail` which can be used to stop task execution if one of its commands was unsuccessful.

### Global StopOnFail

There is a global `stopOnFail` method as well, that can be used to stop a command on first failure of a task.

```
$this->stopOnFail(true);
```

## Output

As you noticed you can print text with `say` method taken from `Robo\Output` trait.

```
$this->say("Hello");
```

Also you can ask for input from console

```
$name = $this->ask("What is your name?");
```

There are also `askDefault`, `askHidden`, and `confirm` methods.

Inside tasks you should print process details with `printTaskInfo`, ``printTaskSuccess`, `printTaskError`.
To allow tasks access IO use `Robo\Common\TaskIO` trait or inherit task class from `Robo\Task\BaseTask` (recommended).

```
$this->printTaskInfo('Processing...');
```