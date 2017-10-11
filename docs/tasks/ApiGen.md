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
* `source($src)`   * `param array|string|Traversable` $src one or more source values
* `destination($dest)`   * `param string` $dest
* `extensions($exts)`   * `param array|string` $exts one or more extensions
* `exclude($exclude)`   * `param array|string` $exclude one or more exclusions
* `skipDocPath($path)`   * `param array|string|Traversable` $path one or more skip-doc-path values
* `skipDocPrefix($prefix)`   * `param array|string|Traversable` $prefix one or more skip-doc-prefix values
* `charset($charset)`   * `param array|string` $charset one or more charsets
* `mainProjectNamePrefix($name)`   * `param string` $name
* `title($title)`   * `param string` $title
* `baseUrl($baseUrl)`   * `param string` $baseUrl
* `googleCseId($id)`   * `param string` $id
* `googleAnalytics($trackingCode)`   * `param string` $trackingCode
* `templateConfig($templateConfig)`   * `param mixed` $templateConfig
* `allowedHtml($tags)`   * `param array|string` $tags one or more supported html tags
* `groups($groups)`   * `param string` $groups
* `autocomplete($types)`   * `param array|string` $types or more supported autocomplete types
* `accessLevels($levels)`   * `param array|string` $levels one or more access levels
* `internal($internal)`   * `param boolean|string` $internal 'yes' or true if internal, 'no' or false if not
* `php($php)`   * `param boolean|string` $php 'yes' or true to generate documentation for internal php classes,
* `tree($tree)`   * `param bool|string` $tree 'yes' or true to generate a tree view of classes, 'no' or false otherwise
* `deprecated($dep)`   * `param bool|string` $dep 'yes' or true to generate documentation for deprecated classes, 'no' or false otherwise
* `todo($todo)`   * `param bool|string` $todo 'yes' or true to document tasks, 'no' or false otherwise
* `sourceCode($src)`   * `param bool|string` $src 'yes' or true to generate highlighted source code, 'no' or false otherwise
* `download($zipped)`   * `param bool|string` $zipped 'yes' or true to generate downloadable documentation, 'no' or false otherwise
* `report($path)` 
* `wipeout($wipeout)`   * `param bool|string` $wipeout 'yes' or true to clear out the destination directory, 'no' or false otherwise
* `quiet($quiet)`   * `param bool|string` $quiet 'yes' or true for quiet, 'no' or false otherwise
* `progressbar($bar)`   * `param bool|string` $bar 'yes' or true to display a progress bar, 'no' or false otherwise
* `colors($colors)`   * `param bool|string` $colors 'yes' or true colorize the output, 'no' or false otherwise
* `updateCheck($check)`   * `param bool|string` $check 'yes' or true to check for updates, 'no' or false otherwise
* `debug($debug)`   * `param bool|string` $debug 'yes' or true to enable debug mode, 'no' or false otherwise
* `dir($dir)`  Changes working directory of command
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

