# Collection Builders

Robo provides task collections as a means of making error detection and recovery easier. When Robo tasks are added to a collection, their execution is deferred until the `$collection->run()` method is called.  If one of the tasks fail, then the operation will be aborted; rollback tasks may also be defined to restore the system to its original condition.

When using collections, a Robo script will go through three phases:

1. Determine which tasks will need to be run, and create a task builder.
  - Assign values to variables.
  - Do not alter the state of the system.
2. Create the necessary tasks via the task builder.
  - Use variables calculated in the first phase in task parameters.
3. Run the tasks via the `run()` method.
  - Check and report errors once after `run()` returns.

Following this pattern will keep your code linear and easy to understand.

## Collections API

Collections are made up of a combination of tasks and/or `callable` functions / method pointers, such as:

  - A task (implements TaskInterface)
  - A function name (string)
  - A closure (inline function)
  - A method reference (array with object and method name)

Examples of adding different kinds of tasks to a collection are provided below.

### TaskInterface Objects

```php
<?php
  $collection->add(
    $this->taskExec('ls')
  );
?>
```

### Functions

```php
<?php
  $collection->addCode('mytaskfunction');
?>
```

### Closures

```php
<?php
  $collection->addCode(
    function() use ($work)
    {
      // do something with $work      
    });
?>
```

### Methods

```php
<?php
  $collection->addCode([$myobject, 'mymethod']);
?>
```

## Using a Collection Builder

To manage a collection of tasks, use a collection builder. Collection builders allow tasks to be created via chained methods.  All of the tasks created by the same builder are added to a collection; when the `run()` method is called, all of the tasks in the collection run. 

The 'publish' command from Robo's own RoboFile is shown below.  It uses a collection builder to run some git and filesystem operations. The "completion" tasks are run after all other tasks complete, or during rollback processing when an operation fails.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
    public function publish()
    {
        $current_branch = exec('git rev-parse --abbrev-ref HEAD');

        $collection = $this->collectionBuilder();
        $collection->taskGitStack()
            ->checkout('site')
            ->merge('master')
        ->completion($this->taskGitStack()->checkout($current_branch))
        ->taskFilesystemStack()
            ->copy('CHANGELOG.md', 'docs/changelog.md')
        ->completion($this->taskFilesystemStack()->remove('docs/changelog.md'))
        ->taskExec('mkdocs gh-deploy');

        return $collection;
    }
}
?>
```

The example above also adds a couple of tasks as "completions"; these are run when the collection completes execution, as explained below.

## Rollbacks and Completions

Robo also provides rollbacks and completions, special tasks that are eligible to run only if all of the tasks added to the collection before them succeed. The section below explains the circumstances under which these tasks will run.

### Completion Tasks

Completions run whenever their collection completes or fails, but only if all of the tasks that come before it succeed. An example of this is shown in the first example above. A filesystem stack task copies CHANDELOG.md to docs/changelog.md; after this task is added to the collection, another filesystem stack task is added as a completion to delete docs/changelog.md. This is done because docs/changelog.md is only intended to exist long enough to be used by the `mkdocs` task, which is added later. 

### Rollback Tasks

In addition to completions, Robo also supports rollbacks. Rollback tasks can be used to clean up after failures, so the state of the system does not change when execution is interrupted by an error. A rollback task is executed if all of the tasks that come before it succeed, and at least one of the tasks that come after it fails.  If all tasks succeed, then no rollback tasks are executed.

### Rollback and Completion Methods

Any task may also implement \Robo\Contract\RollbackInterface; if this is done, then its `rollback()` method will be called if the task is `run()` on a collection that later fails.

Use `addAsCompletion($collection)` in place of `addAsRollback($collection)`, or implement \Robo\Contract\CompletionInterface. Completions otherwise work exactly like rollbacks.

### Rollback and Completion Callbacks

You may also provide arbitrary methods as `callable`s to serve as rollback or completion functions, as shown below:

``` php
<?php
  $collection->rollbackCode([$myobject, 'myrollback']);
  $collection->completionCode([$myobject, 'mycompletion']);
?>
```

## Temporary Objects

Since the concept of temporary objects that are cleaned up  on failure is a common pattern, Robo provides built-in support for them. Temporary directories and files are provided out of the box; other kinds of temporary objects can be easily created using the Temporary global collection.

### Temporary Directories

It is recommended that operations that perform multiple filesystem operations should, whenever possible, do most of their work in a temporary directory. Temporary directories are created by `$this->taskTmpDir()`, and are automatically be removed when the collection completes or rolls back. As an added convenience, the CollectionBuilder class has a `tmpDir()` method that creates a temporary directory via `taskTmpDir()`, and then returns the path to the temporary directory.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
    function myOperation()
    {
        $collection = $this->collectionBuilder();
        
        // Create a temporary directory, and fetch its path.
        $work = $collection->tmpDir();

        $collection
          ->taskWriteToFile("$work/README.md")
            ->line('-----')
            ->line(date('Y-m-d').' Generated file: do not edit.')
            ->line('----');
        
        // If all of the preceding tasks succeed, then rename the temporary 
        // directory to its final name.
        $collection->taskFilesystemStack()
          ->rename($work, 'destination');
        
        return $collection->run();
    }
}
?>
```

In the previous example, the path to the temporary directory is stored in the variable `$work`, and is passed as needed to the parameters of the other tasks as they are added to the collection. After the task collection is run, the temporary directory will be automatically deleted. In the example above, the temporary directory is renamed by the last task in the collection. This allows the working directory to persist; the collection will still attempt to remove the working directory, but no errors will be thrown if it no longer exists in its original location. Following this pattern allows Robo scripts to easily and safely do work that cleans up after itself on failure, without introducing a lot of branching or additional error recovery code.  This paradigm is common enough to warrant a shortcut method of accomplishing the same thing.  The example below is identical to the one above, save for the fact that it uses the `workDir()` method instead of `tmpDir()`.  `workDir()` renames the temporary directory to its final name if the collection completes; any directory that exists in the same location will be overwritten at that time, but will persist if the collection roles back.

``` php
<?php
class RoboFile extends \Robo\Tasks
{
    function myOperation()
    {
        $collection = $this->collectionBuilder();
        
        // Create a temporary directory, and fetch its path.
        // If all of the tasks succeed, then rename the temporary directory
        // to its final name.
        $work = $collection->workDir('destination');

        $collection
          ->taskWriteToFile("$work/README.md")
            ->line('-----')
            ->line(date('Y-m-d').' Generated file: do not edit.')
            ->line('----');
        
        return $collection->run();
    }
}
?>
```

Temporary directories may also be created via the shortcut `$this->_tmpDir();`. Temporary directories created in this way are deleted when the script terminates.

### Temporary Files

Robo also provides an API for creating temporary files. They may be created via `$this->taskTmpFile()`; they are used exactly like `$this->taskWrite()`, except they are given a random name on creation, and are deleted when their collection completes.  If they are not added to a collection, then they are deleted when the script terminates.

### The Temporary Global Collection

Robo maintains a special collection called the Temporary global collection. This collection is used to keep track of temporary objects that are not part of any collection. For example, Robo temporary directories and temporary files are managed by the Temporary global collection. These temporary objects are cleaned up automatically when the script terminates.

It is easy to create your own temporary tasks that behave in the same way as the provided temporary directory and temporary file tasks. There are two steps required:

- Implement \Robo\Contract\CompletionInterface
- Wrap the task via Temporary::wrap()

For example, the implementation of taskTmpFile() looks like this:

``` php
<?php
    protected function taskTmpFile($filename = 'tmp', $extension = '', $baseDir = '', $includeRandomPart = true)
    {
        return Temporary::wrap(new TmpFile($filename, $extension, $baseDir, $includeRandomPart));
    }
?>
```

The `complete()` method of the task will be called once the Collection the temporary object is attached to finishes running. If the temporary is not added to a collection, then its `complete()` method will be called when the script terminates.

## Chained State

When using a collection builder, it is possible to pass state from one task to another. State is generated during the `run()` method of each task, and returned in a `Robo\Result` object. Each result has a "message" and a key/value data store that contains the task's state. This state can be made available to later tasks in the builder.

### Implicitly Passing State

Sometimes it may be desirable to process the files produced by one task using a following task that alters the result.

For example, if you have one task that takes a set of source files and generates destination files, and another task that encrypts a set of files, you could encrypt the results from the first task by running both of the tasks independently:
``` php
<?php
    $result = $this->taskGenerate()
        ->files($sources)
        ->run();
    
    $result = $this->taskEncrypt()
        ->files($result['files'])
        ->run();
?>
```
If the Encrypt task implements `\Robo\State\Consumer` and accepts 'files' from the current state, then these tasks may be chained together as follows:
``` php
<?php
    $collection = $this->collectionBuilder();
    $collection
        ->taskGenerate()
            ->files($sources)
        ->taskEncrypt()
        ->run();
?>
```
Tasks that do not implement the `Consumer` interface may still be chained together by explicitly connecting the state from one task with the task configuration methods, as explained in the following section:

### Explicitly Passing State

State from the key/value data store, if set, is automatically stored in the collection's state. The `storeState()` method can be used to store the result "message".

To pass state from one task to another, the `deferTaskConfiguration()` method may be used. This method defers initialization until immediately before the task's `run()` method is called. It then calls a single named setter method, passing it the value of some state variable. 

For example, the builder below will create a new directory named after the output of the `uname -n` command returned by taskExec. Note that it is necessary to call `printOutput(false)` in order to make the output of taskExec available to the state system.
``` php
<?php
    $this->collectionBuilder()
        ->taskExec('uname -n')
            ->printOutput(false)
            ->storeState('system-name')
        ->taskFilesystemStack()
            ->deferTaskConfiguration('mkdir', 'system-name')
        ->run();
?>
```
More complex task configuration may be done via the `defer()` method. `defer()` works like `deferTaskConfiguration()`, except that it will run an arbitrary `callable` immediately prior to the execution of the task. The example below works exactly the same as the previous example, but is implemented using `defer()` instead of `deferTaskConfiguration()`.
``` php
<?php
    $this->collectionBuilder()
        ->taskExec('uname -n')
            ->printOutput(false)
            ->storeState('system-name')
        ->taskFilesystemStack()
            ->defer(
                function ($task, $state) {
                    $task->mkdir($state['system-name']);
                }
            )
        ->run();
?>
```
In general, it is preferable to collect all of the information needed first, and then use that data to configure the necessary tasks. For example, the previous example could be implemented more simply by calling `$system_name = exec('uname -n');` and `taskFilesystemStack->mkdir($system_name);`. Chained state can be helpful in instances where there is a more complex relationship between the tasks.

## Named Tasks

It is also possible to provide names for the tasks added to a collection. This has two primary benefits:

1. Any result data returned from a named task is stored in the Result object under the task name.
2. It is possible for other code to add more tasks before or after any named task.

This feature is useful if you have functions that create task collections, and return them as a function results. The original caller can then use the `$collection->before()` or `$collection->after()` to insert sequenced tasks into the set of operations to be performed. One reason this might be done would be to define a base set of operations to perform (e.g. in a deploy), and then apply modifications for other environments (e.g. dev or stage).

```php
<?php
  $collection->addCode(
    function() use ($work)
    {
      // do something with $work      
    },
    "taskname");
?>
```

Given a collection with named tasks, it is possible to insert more tasks before or after a task of a given name.

```php
<?php
  $collection->after("taskname",
    function() use ($work)
    {
      // do something with $work after "taskname" executes, if it succeeds.    
    });
?>
```

```php
<?php
  $collection->before("taskname",
    function() use ($work)
    {
      // do something with $work before "taskname" executes.    
    });
?>
```

It is recommended that named tasks be avoided unless specifically needed.

