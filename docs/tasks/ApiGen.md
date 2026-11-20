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

* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `config($config)`
* `source($src)`
* `destination($dest)`
* `extensions($exts)`
* `exclude($exclude)`
* `skipDocPath($path)`
* `skipDocPrefix($prefix)`
* `charset($charset)`
* `mainProjectNamePrefix($name)`
* `title($title)`
* `baseUrl($baseUrl)`
* `googleCseId($id)`
* `googleAnalytics($trackingCode)`
* `templateConfig($templateConfig)`
* `allowedHtml($tags)`
* `groups($groups)`
* `autocomplete($types)`
* `accessLevels($levels)`
* `internal($internal)`
* `php($php)` or false otherwise.
* `tree($tree)` otherwise.
* `deprecated($dep)` false otherwise.
* `todo($todo)`
* `sourceCode($src)` otherwise.
* `download($zipped)` otherwise.
* `report($path)`
* `wipeout($wipeout)` otherwise.
* `quiet($quiet)`
* `progressbar($bar)`
* `colors($colors)`
* `updateCheck($check)`
* `debug($debug)`
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


