# Getting Started

To begin you need to create a Robofile. Just run `robo` in empty dir:

```
php vendor/bin/robo
```

You will be asked to create a file. New `RoboFile.php` is created with all tasks included.

``` php
<?php
class RoboFile
{
    use Robo\Output;
}
?>
```

All public methods of this class will be trated as **commands**. You can run them from CLI and pass arguments.

``` php
<?php
class RoboFile extends Robo\BundledTasks
{
    use Robo\Output;
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

Methods should be camelCased. In CLI `camelCased` method will be available as `camel:cased` command

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
class RoboFile extends Robo\BundledTasks
{
    use Robo\Output;
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
    use Robo\Task\Exec

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

# Output

As you noticed you can print text with `say` method taken from `Robo\Output` trait.

```
$this->say("Hello");
```

Also you can ask for input from console

```
$name = $this->ask("What is your namr?");
```

Inside tasks you should print process details with `printTaskInfo`

```
$this->printTaskInfo('Processing...');
```