# ApiGen Tasks
## ApiGen


Executes ApiGen command to generate documentation 

``` php
<?php
// ApiGen Command
$this->taskApiGen('./apigen.neon')
     ->templateConfig('vendor/apigen/apigen/templates/bootstrap/config.neon')
     ->wipeout(true)
      ->run();
?>
```

* `config($config)` 
* `source($src)`   * `param array|string|Traversable` $src one or more source values
* `destination($dest)` 
* `extensions($exts)`   * `param array|string` $exts one or more extensions
* `exclude($exclude)`   * `param array|string` $exclude one or more exclusions
* `skipDocPath($path)`   * `param array|string|Traversable` $exts one or more skip-doc-path values
* `skipDocPrefix($prefix)`   * `param array|string|Traversable` $prefix one or more skip-doc-prefix values
* `charset($charset)`   * `param array|string` $charset one or more charsets
* `mainProjectNamePrefix($name)` 
* `title($title)` 
* `baseUrl($baseUrl)` 
* `googleCseId($id)` 
* `googleAnalytics($trackingCode)` 
* `templateConfig($templateConfig)` 
* `allowedHtml($tags)`   * `param array|string` $tags one or more supported html tags
* `groups($groups)` 
* `autocomplete($types)`   * `param array|string` $types or more supported autocomple types
* `accessLevels($levels)`   * `param array|string` $levels one or more access levels 
* `internal($internal)`   * `param boolean|string` $internal 'yes' or true if internal, 'no' or false if not
* `php($php)`   * `param boolean|string` $php 'yes' or true to generate documentation for internal php classes,
* `tree($tree)`   * `param boolean|string` $tree 'yes' or true to generate a tree view of classes, 'no' or false otherwise
* `deprecated($dep)`   * `param boolean|string` $dep 'yes' or true to generate documentation for deprecated classes, 'no' or false otherwise
* `todo($todo)`   * `param boolean|string` $todo 'yes' or true to document tasks, 'no' or false otherwise
* `sourceCode($src)`   * `param boolean|string` $src 'yes' or true to generate highlighted source code, 'no' or false otherwise
* `download($zipped)`   * `param boolean|string` $zipped 'yes' or true to generate downloadable documentation, 'no' or false otherwise
* `report($path)` 
* `wipeout($wipeout)`   * `param boolean|string` $wipeout 'yes' or true to clear out the destination directory, 'no' or false otherwise
* `quiet($quiet)`   * `param boolean|string` $quiet 'yes' or true for quiet, 'no' or false otherwise
* `progressbar($bar)`   * `param boolean|string` $bar 'yes' or true to display a progress bar, 'no' or false otherwise
* `colors($colors)`   * `param boolean|string` $colors 'yes' or true colorize the output, 'no' or false otherwise
* `updateCheck($check)`   * `param boolean|string` $check 'yes' or true to check for updates, 'no' or false otherwise
* `debug($debug)`   * `param boolean|string` $debug 'yes' or true to enable debug mode, 'no' or false otherwise
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

