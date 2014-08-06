# Getting Started

To begin you need to create a Robofile. Just run `robo` in empty dir:

```
php vendor/bin/robo
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
php vendor/bin/robo hello davert
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
php vendor/bin/robo hello
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
php vendor/bin/robo hello davert jon bill bob
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
php vendor/bin/robo hello
➜ Hello, world

php vendor/bin/robo hello --silent
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
php vendor/bin/robo ls -- Robo -c --all
 [Robo\Task\ExecTask] running ls Robo -c --all
 .  ..  CHANGELOG.md  codeception.yml  composer.json  composer.lock  docs  .git  .gitignore  .idea  LICENSE  README.md  robo  RoboFile.php  robo.phar  src  tests  .travis.yml  vendor
```

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

class CompileAssetsTask implements Robo\TaskInterface
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
        $res1 = $this->taskExec('phpunit tests/integration')->run();
        $res2 = $this->taskExec('phpunit tests/unit')->run();

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

Inside tasks you should print process details with `printTaskInfo`

```
$this->printTaskInfo('Processing...');
```
