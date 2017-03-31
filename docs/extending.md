# Extending

Robo tasks can be added to your Robo application by using Composer to suppliment the set of built-in tasks that Robo provides by default. To find existing Robo task extensions, search in Packagist for projects of type [robo-tasks](https://packagist.org/search/?type=robo-tasks).

The convention used to add new tasks for use in your RoboFiles is to create a wrapper trait named `loadTasks` that instantiates the implementation class for each task. Each task method in the trait should start with the prefix `task`, and should use **chained method calls** for configuration. Task execution should be triggered by the method `run`. 

To include additional tasks in your RoboFile, you must `use` the appropriate `loadTasks` in your RoboFile. See the section [Including Additional Tasks](#including-additional-tasks) below. To create your own Robo extension that provides tasks for use in RoboFiles, then you must write your own class that implements TaskInterface, and create a `loadTasks` trait for it as described in the section [Creating a Robo Extension](#creating-a-robo-extension).

## Including Additional Tasks

Additional tasks may be installed into projects that have included Robo via Composer. For example:
```
$ cd myproject
$ composer require boedah/robo-drush
```
If any of the tasks you include require external Composer projects themselves, then you must `composer require` these as well.  See the `suggests` section of Robo's composer.json file for a list of some projects you might need to require.

Once the extension you wish to use has been added to your vendor directory, you may then include it from your RoboFile:
``` php
class RoboFile extends \Robo\Tasks
{
  use \Boedah\Robo\Task\Drush\loadTasks;
  
  public function test()
  {
    // ...
  }
}
```
Once you have done this, all of the tasks defined in the extension you selected will be available for use in your commands.

Note that at the moment, it is not possible to extend Robo when using the robo.phar. This capability may be added in the future via [embedded composer](https://github.com/dflydev/dflydev-embedded-composer).

## Creating a Robo Extension

A Robo tasks extension is created by advertising a Composer package of type `robo-tasks` on [Packagist](https://packagist.org/).  For an overview on how this is done, see the article [Creating your very own Composer Package](https://knpuniversity.com/screencast/question-answer-day/create-composer-package).  Specific instructions for creating Robo task extensions are provided below.

### Create your composer.json File

Your composer.json file should look something like the example below:
```
{
    "name": "boedah/robo-drush",
    "description": "Drush CommandStack for Robo Task Runner",
    "type": "robo-tasks",
    "autoload": {
        "psr-4": {
            "Boedah\\Robo\\Task\\Drush\\": "src"
        }
    },
    "require": {
        "php": ">=5.5.0",
        "consolidation/robo": "~1"
    }
}
```
Customize the name and autoload paths as necessary, and add any additional required projects needed by the tasks that your extensions will provide.  The type of your project should always be `robo-tasks`.  Robo only supports php >= 5.5.0; you may require a higher version of php if necessary.

### Create the loadTasks.php Trait

It is recommended to place your trait-loading task in a `loadTasks` file in the same namespace as the task implementation.
```
namespace Boedah\Robo\Task\Drush;

use Robo\Container\SimpleServiceProvider;

trait loadTasks
{
    /**
     * @param string $pathToDrush
     * @return DrushStack
     */
    protected function taskDrushStack($pathToDrush = 'drush')
    {
        return $this->task(__FUNCTION__, $pathToDrush);
    }
}
```
Note that the name of the service for a given task must start with the word "task", and must have the same name as the function used to call the task.  `$this->task()` looks up the service by name; using the PHP built-in constant __FUNCTION__ for this parameter ensures that the names of these items remain in alignment.

### Task implementation

The implementation of each task class should extend \Robo\Task\BaseTask, or some class that extends the same, and should used chained initializer methods and defer all operations that alter the state of the system until its `run()` method.  If you follow these patterns, then your task extensions will be usable via Robo collection builders, as explained in the [collections](collections.md) documentation.

There are many examples of task implementations in the Robo\Task namespace.  A very basic task example is provided below.  The namespace is `MyAssetTasks`, and the example task is `CompileAssets`. To customize to your purposes, choose an appropriate namespace, and then define as many tasks as you need.

``` php
<?php
namespace MyAssetTasks;

trait loadTasks
{
    /**
     * Example task to compile assets
     *
     * @param string $pathToCompileAssets
     * @return \MyAssetTasks\CompileAssets
     */
    protected function taskCompileAssets($path = null)
    {
        // Always construct your tasks with the `task()` task builder.
        return $this->task(CompileAssets::class, $path);
    }
}

class CompileAssets implements \Robo\Contract\TaskInterface
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

To use the tasks you define in a RoboFile, use its `loadTasks` trait as explained in the section [Including Additional Tasks](#including-additional-tasks), above.

### TaskIO

To allow tasks access IO, use the `Robo\Common\TaskIO` trait, or inherit your task class from `Robo\Task\BaseTask` (recommended).

Inside tasks you should print process details with `printTaskInfo`, `printTaskSuccess`, and `printTaskError`.
```
$this->printTaskInfo('Processing...');
```
The Task IO methods send all output through a PSR-3 logger. Tasks should use task IO exclusively; methods such as 'say' and 'ask' should reside in the command method. This allows tasks to be usable in any context that has a PSR-3 logger, including background or server processes where it is not possible to directly query the user.

### Tasks That Use Tasks

If one task implementation needs to use other tasks while it is running, it should do so via a `CollectionBuilder` object, as explained in the [Collections](collections.md) documentation.

To obtain access to a `CollectionBuilder`, a task should implement `BuilderAwareInterface` and use `BuilderAwareTrait`.  It will then have access to a collection builder via the `$this->collectionBuilder()` method.

### Testing Extensions

If you wish to use the `task()` methods from your `loadTasks` trait in your unit tests, it is necessary to also use the Robo `TaskAccessor` trait, and define a `collectionBuilder()` method to provide a builder.  Collection builders are used to initialize all Robo tasks.  The easiest way to get a usable collection builder in your tests is to initialize Robo's default dependency injection container, and use it to request a new builder.

An example of how to do this in a PHPUnit test is shown below.
```
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Symfony\Component\Console\Output\NullOutput;
use Robo\TaskAccessor;
use Robo\Robo;
use Robo\Collection\CollectionBuilder;

class DrushStackTest extends \PHPUnit_Framework_TestCase implements ContainerAwareInterface
{
    use \Boedah\Robo\Task\Drush\loadTasks;
    use TaskAccessor;
    use ContainerAwareTrait;

    // Set up the Robo container so that we can create tasks in our tests.
    function setup()
    {
        $container = Robo::createDefaultContainer(null, new NullOutput());
        $this->setContainer($container);
    }

    // Scaffold the collection builder
    public function collectionBuilder()
    {
        $emptyRobofile = new \Robo\Tasks;
        return CollectionBuilder::create($this->getContainer(), $emptyRobofile);
    }

    public function testYesIsAssumed()
    {
        $command = $this->taskDrushStack()
            ->drush('command')
            ->getCommand();
        $this->assertEquals('drush command -y', $command);
    }
}
```
To assert that the output of a command contains some value, use a `Symfony\Component\Console\Output\BufferedOutput` in place of null output when calling Robo::createDefaultContainer().
