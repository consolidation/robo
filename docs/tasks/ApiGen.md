# ApiGen Tasks
## ApiGen


Executes ApiGen command to generate documentation

``` php
<?php
// ApiGen Command
$this->taskApiGen('./vendor/apigen/apigen.phar')
     ->config('./apigen.neon')
     ->templateConfig('vendor/apigen/apigen/templates/bootstrap/config.neon')
     ->wipeout(true)
      ->run();
?>
```

* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `config($config)`   * `param string` $config
* `source($src)`   * `param array|string|\Traversable` $src
* `destination($dest)`   * `param string` $dest
* `extensions($exts)`   * `param array|string` $exts
* `exclude($exclude)`   * `param array|string` $exclude
* `skipDocPath($path)`   * `param array|string|\Traversable` $path
* `skipDocPrefix($prefix)`   * `param array|string|\Traversable` $prefix
* `charset($charset)`   * `param array|string` $charset
* `mainProjectNamePrefix($name)`   * `param string` $name
* `title($title)`   * `param string` $title
* `baseUrl($baseUrl)`   * `param string` $baseUrl
* `googleCseId($id)`   * `param string` $id
* `googleAnalytics($trackingCode)`   * `param string` $trackingCode
* `templateConfig($templateConfig)`   * `param mixed` $templateConfig
* `allowedHtml($tags)`   * `param array|string` $tags
* `groups($groups)`   * `param string` $groups
* `autocomplete($types)`   * `param array|string` $types
* `accessLevels($levels)`   * `param array|string` $levels
* `internal($internal)`   * `param boolean|string` $internal
* `php($php)`   * `param bool|string` $php
* `tree($tree)`   * `param bool|string` $tree
* `deprecated($dep)`   * `param bool|string` $dep
* `todo($todo)`   * `param bool|string` $todo
* `sourceCode($src)`   * `param bool|string` $src
* `download($zipped)`   * `param bool|string` $zipped
* `report($path)`   * `param string` $path
* `wipeout($wipeout)`   * `param bool|string` $wipeout
* `quiet($quiet)`   * `param bool|string` $quiet
* `progressbar($bar)`   * `param bool|string` $bar
* `colors($colors)`   * `param bool|string` $colors
* `updateCheck($check)`   * `param bool|string` $check
* `debug($debug)`   * `param bool|string` $debug
* `setOutput($output)`  Sets the Console Output.
* `setProcessInput($input)`  Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.


