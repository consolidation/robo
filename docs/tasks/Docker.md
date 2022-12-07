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

 * `param string` $tag
* `enableBuildKit()`

 * `return $this`
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

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

 * `param string` $name
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

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

 * `return $this`
* `exec($command)`

 * `param string|\Robo\Contract\CommandInterface` $command
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Pull


Pulls an image from DockerHub

```php
<?php
$this->taskDockerPull('wordpress')
     ->run();

?>
```


* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Remove


Remove docker container

```php
<?php
$this->taskDockerRemove($container)
     ->run();
?>
```


* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option


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

 * `return $this`
* `exec($run)`

 * `param string|\Robo\Contract\CommandInterface` $run
* `volume($from, $to = null)`

 * `param string` $from
* `publish($port = null, $portTo = null)`

 * `param null|int` $port
* `containerWorkdir($dir)`

 * `param string` $dir
* `user($user)`

 * `param string` $user
* `privileged()`

 * `return $this`
* `name($name)`

 * `param string` $name
* `link($name, $alias)`

 * `param string|\Robo\Task\Docker\Result` $name
* `tmpDir($dir)`

 * `param string` $dir
* `getTmpDir()`

 * `return string`
* `getUniqId()`

 * `return string`
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Start


Starts Docker container

```php
<?php
$this->taskDockerStart($cidOrResult)
     ->run();
?>
```

* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option

## Stop


Stops Docker container

```php
<?php
$this->taskDockerStop($cidOrResult)
     ->run();
?>
```

* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `args($args)`

 * `param string|string[]` $args
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option


