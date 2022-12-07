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

* `args($args)`

 * `param string|string[]` $args
* `config($config)`

 * `param string` $config
* `source($src)`

 * `param array|string|\Traversable` $src One or more source values.
* `destination($dest)`

 * `param string` $dest
* `extensions($exts)`

 * `param array|string` $exts One or more extensions.
* `exclude($exclude)`

 * `param array|string` $exclude One or more exclusions.
* `skipDocPath($path)`

 * `param array|string|\Traversable` $path One or more skip-doc-path values.
* `skipDocPrefix($prefix)`

 * `param array|string|\Traversable` $prefix One or more skip-doc-prefix values.
* `charset($charset)`

 * `param array|string` $charset One or more charsets.
* `mainProjectNamePrefix($name)`

 * `param string` $name
* `title($title)`

 * `param string` $title
* `baseUrl($baseUrl)`

 * `param string` $baseUrl
* `googleCseId($id)`

 * `param string` $id
* `googleAnalytics($trackingCode)`

 * `param string` $trackingCode
* `templateConfig($templateConfig)`

 * `param mixed` $templateConfig
* `allowedHtml($tags)`

 * `param array|string` $tags One or more supported html tags.
* `groups($groups)`

 * `param string` $groups
* `autocomplete($types)`

 * `param array|string` $types One or more supported autocomplete types.
* `accessLevels($levels)`

 * `param array|string` $levels One or more access levels.
* `internal($internal)`

 * `param boolean|string` $internal 'yes' or true if internal, 'no' or false if not.
* `php($php)`

 * `param bool|string` $php 'yes' or true to generate documentation for internal php classes, 'no'
* `tree($tree)`

 * `param bool|string` $tree 'yes' or true to generate a tree view of classes, 'no' or false
* `deprecated($dep)`

 * `param bool|string` $dep 'yes' or true to generate documentation for deprecated classes, 'no' or
* `todo($todo)`

 * `param bool|string` $todo 'yes' or true to document tasks, 'no' or false otherwise.
* `sourceCode($src)`

 * `param bool|string` $src 'yes' or true to generate highlighted source code, 'no' or false
* `download($zipped)`

 * `param bool|string` $zipped 'yes' or true to generate downloadable documentation, 'no' or false
* `report($path)`

 * `param string` $path
* `wipeout($wipeout)`

 * `param bool|string` $wipeout 'yes' or true to clear out the destination directory, 'no' or false
* `quiet($quiet)`

 * `param bool|string` $quiet 'yes' or true for quiet, 'no' or false otherwise.
* `progressbar($bar)`

 * `param bool|string` $bar 'yes' or true to display a progress bar, 'no' or false otherwise.
* `colors($colors)`

 * `param bool|string` $colors 'yes' or true colorize the output, 'no' or false otherwise.
* `updateCheck($check)`

 * `param bool|string` $check 'yes' or true to check for updates, 'no' or false otherwise.
* `debug($debug)`

 * `param bool|string` $debug 'yes' or true to enable debug mode, 'no' or false otherwise.
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir
* `arg($arg)`

 * `param string` $arg
* `rawArg($arg)`

 * `param string` $arg
* `option($option, $value = null, $separator = null)`

 * `param string` $option
* `options(array $options, $separator = null)`

 * `param array` $options
* `optionList($option, $value = null, $separator = null)`

 * `param string` $option


