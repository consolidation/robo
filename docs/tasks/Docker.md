# Docker Tasks

## Build








* `tag($tag)` 



















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Commit










* `name($name)` 



















* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Pull

























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Remove

























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Result

#### *public static* stopOnFail




* `getCid()` 



* `getData()`  @return array
* `getExitCode()`  @return mixed
* `getMessage()`  @return mixed
* `getExecutionTime()` 
* `getTask()`  @return TaskInterface
* `cloneTask()` 
* `wasSuccessful()` 
* `__invoke()` 
* `stopOnFail()` 


















## Run









* `getPrinted()` 

* `exec($run)` 
* `volume($from, $to = null)` 
* `env($variable, $value = null)` 
* `publish($port = null)` 
* `containerWorkdir($dir)` 
* `user($user)` 
* `privileged()` 
* `name($name)` 




















* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed





## Start




























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




## Stop




























* `getPrinted()` 
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed




