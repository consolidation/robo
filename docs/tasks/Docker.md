# Docker Tasks

## Build


Builds Docker image

```php
<?php
$this->taskDockerBuild()->run();

$this->taskDockerBuild('path/to/dir')
     ->tag('database')
     ->run();

?>

```

Class Build
@package Robo\Task\Docker

* `tag($tag)`
* `enableBuildKit()`
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Commit


Commits docker container to an image

```
$this->taskDockerCommit($containerId)
     ->name('my/database')
     ->run();

// alternatively you can take the result from DockerRun task:

$result = $this->taskDockerRun('db')
     ->exec('./prepare_database.sh')
     ->run();

$task->dockerCommit($result)
     ->name('my/database')
     ->run();
```

* `name($name)`
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Exec


Executes command inside running Docker container

```php
<?php
$test = $this->taskDockerRun('test_env')
     ->detached()
     ->run();

$this->taskDockerExec($test)
     ->interactive()
     ->exec('./runtests')
     ->run();

// alternatively use commands from other tasks

$this->taskDockerExec($test)
     ->interactive()
     ->exec($this->taskCodecept()->suite('acceptance'))
     ->run();
?>
```


* `detached()`
* `exec($command)`
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Pull


Pulls an image from DockerHub

```php
<?php
$this->taskDockerPull('wordpress')
     ->run();

?>
```


* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Remove


Remove docker container

```php
<?php
$this->taskDockerRemove($container)
     ->run();
?>
```


* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


## Run


Performs `docker run` on a container.

```php
<?php
$this->taskDockerRun('mysql')->run();

$result = $this->taskDockerRun('my_db_image')
     ->env('DB', 'database_name')
     ->volume('/path/to/data', '/data')
     ->detached()
     ->publish(3306)
     ->name('my_mysql')
     ->run();

// retrieve container's cid:
$this->say("Running container ".$result->getCid());

// execute script inside container
$result = $this->taskDockerRun('db')
     ->exec('prepare_test_data.sh')
     ->run();

$this->taskDockerCommit($result)
     ->name('test_db')
     ->run();

// link containers
$mysql = $this->taskDockerRun('mysql')
     ->name('wp_db') // important to set name for linked container
     ->env('MYSQL_ROOT_PASSWORD', '123456')
     ->run();

$this->taskDockerRun('wordpress')
     ->link($mysql)
     ->publish(80, 8080)
     ->detached()
     ->run();

?>
```


* `detached()`
* `exec($run)`
* `volume($from, $to = null)`
* `publish($port = null, $portTo = null)`
* `containerWorkdir($dir)`
* `user($user)`
* `privileged()`
* `name($name)`
* `link($name, $alias)`
* `tmpDir($dir)`
* `getTmpDir()`
* `getUniqId()`
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Start


Starts Docker container

```php
<?php
$this->taskDockerStart($cidOrResult)
     ->run();
?>
```

* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

## Stop


Stops Docker container

```php
<?php
$this->taskDockerStop($cidOrResult)
     ->run();
?>
```

* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


