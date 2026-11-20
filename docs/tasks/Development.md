# Development Tasks
## Changelog


Helps to manage changelog file.
Creates or updates `changelog.md` file with recent changes in current version.

``` php
<?php
$version = "0.1.0";
$this->taskChangelog()
 ->version($version)
 ->change("released to github")
 ->run();
?>
```

Changes can be asked from Console

``` php
<?php
$changes = [];
while ($change = $this->ask('Change (leave empty to finish)')) {
    $changes[] = $change;
}
$this->taskChangelog()
 ->version($version)
 ->changes($changes)
 ->run();
?>
```

* `filename($filename)`
* `setBody($body)` Sets the changelog body text.
* `setHeader($header)`
* `log($item)`
* `anchor($anchor)`
* `version($version)`
* `changes(array $data)`
* `change($change)`
* `getChanges()`
* `processLogRow($i)`
* `setOutput($output)` Sets the Console Output.

## GenerateMarkdownDoc


Simple documentation generator from source files.
Takes classes, properties and methods with their docblocks and writes down a markdown file.

``` php
<?php
$this->taskGenDoc('models.md')
     ->docClass('Model\User') // take class Model\User
     ->docClass('Model\Post') // take class Model\Post
     ->filterMethods(function(\ReflectionMethod $r) {
         return $r->isPublic() or $r->isProtected(); // process public and protected methods
     })->processClass(function(\ReflectionClass $r, $text) {
         return "Class ".$r->getName()."\n\n$text\n\n###Methods\n";
     })->run();
```

By default this task generates a documentation for each public method of a class, interface or trait.
It combines method signature with a docblock. Both can be post-processed.

``` php
<?php
$this->taskGenDoc('models.md')
     ->docClass('Model\User')
     ->processClassSignature(false) // false can be passed to not include class signature
     ->processClassDocBlock(function(\ReflectionClass $r, $text) {
         return "[This is part of application model]\n" . $text;
     })->processMethodSignature(function(\ReflectionMethod $r, $text) {
         return "#### {$r->name}()";
     })->processMethodDocBlock(function(\ReflectionMethod $r, $text) {
         return strpos($r->name, 'save')===0 ? "[Saves to the database]\n" . $text : $text;
     })->run();
```

* `docClass($item)` Put a class you want to be documented.
* `filterMethods($filterMethods)` Using a callback function filter out methods that won't be documented.
* `filterClasses($filterClasses)` Using a callback function filter out classes that won't be documented.
* `filterProperties($filterProperties)` Using a callback function filter out properties that won't be documented.
* `processClass($processClass)` Post-process class documentation.
* `processClassSignature($processClassSignature)` Post-process class signature. Provide *false* to skip.
* `processClassDocBlock($processClassDocBlock)` Post-process class docblock contents. Provide *false* to skip.
* `processMethod($processMethod)` Post-process method documentation. Provide *false* to skip.
* `processMethodSignature($processMethodSignature)` Post-process method signature. Provide *false* to skip.
* `processMethodDocBlock($processMethodDocBlock)` Post-process method docblock contents. Provide *false* to skip.
* `processProperty($processProperty)` Post-process property documentation. Provide *false* to skip.
* `processPropertySignature($processPropertySignature)` Post-process property signature. Provide *false* to skip.
* `processPropertyDocBlock($processPropertyDocBlock)` Post-process property docblock contents. Provide *false* to skip.
* `reorder($reorder)` Use a function to reorder classes.
* `reorderMethods($reorderMethods)` Use a function to reorder methods in class.
* `reorderProperties($reorderProperties)`
* `filename($filename)`
* `prepend($prepend)` Inserts text at the beginning of markdown file.
* `append($append)` Inserts text at the end of markdown file.
* `text($text)`
* `textForClass($item)`
* `setOutput($output)` Sets the Console Output.

## Generate


Generate a Robo Task that is a wrapper around an existing class.

``` php
<?php
$this->taskGenerateTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')
  ->run();
```

* `setOutput($output)` Sets the Console Output.


## GitHubRelease


Publishes new GitHub release.

``` php
<?php
$this->taskGitHubRelease('0.1.0')
  ->uri('consolidation-org/Robo')
  ->description('Add stuff people need.')
  ->change('Fix #123')
  ->change('Add frobulation method to all widgets')
  ->run();
?>
```

* `tag($tag)`
* `draft($draft)`
* `name($name)`
* `description($description)`
* `prerelease($prerelease)`
* `comittish($comittish)`
* `appendDescription($description)`
* `changes(array $changes)`
* `change($change)`
* `repo($repo)`
* `owner($owner)`
* `uri($uri)`
* `user($user)`
* `password($password)`
* `accessToken($token)`
* `setOutput($output)` Sets the Console Output.

## OpenBrowser


Opens the default's user browser
code inspired from openBrowser() function in https://github.com/composer/composer/blob/master/src/Composer/Command/HomeCommand.php

``` php
<?php
// open one browser window
$this->taskOpenBrowser('http://localhost')
 ->run();

// open two browser windows
$this->taskOpenBrowser([
    'http://localhost/mysite',
    'http://localhost/mysite2'
  ])
  ->run();
```

* `setOutput($output)` Sets the Console Output.

## PackPhar


Creates Phar.

``` php
<?php
$pharTask = $this->taskPackPhar('package/codecept.phar')
  ->compress()
  ->stub('package/stub.php');

 $finder = Finder::create()
     ->name('*.php')
       ->in('src');

   foreach ($finder as $file) {
       $pharTask->addFile('src/'.$file->getRelativePathname(), $file->getRealPath());
   }

   $finder = Finder::create()->files()
       ->name('*.php')
       ->in('vendor');

   foreach ($finder as $file) {
       $pharTask->addStripped('vendor/'.$file->getRelativePathname(), $file->getRealPath());
   }
   $pharTask->run();

   // verify Phar is packed correctly
   $code = $this->_exec('php package/codecept.phar');
?>
```

* `compress($compress = true)`
* `stub($stub)`
* `addStripped($path, $file)`
* `addFile($path, $file)`
* `addFiles($files)`
* `executable($file)`
* `setOutput($output)` Sets the Console Output.

## PhpServer


Runs PHP server and stops it when task finishes.

``` php
<?php
// run server in /public directory
$this->taskServer(8000)
 ->dir('public')
 ->run();

// run with IP 0.0.0.0
$this->taskServer(8000)
 ->host('0.0.0.0')
 ->run();

// execute server in background
$this->taskServer(8000)
 ->background()
 ->run();
?>
```

* `host($host)`
* `dir($path)`
* `simulate($context)` {@inheritdoc}
* `arg($arg)` Pass argument to executable. Its value will be automatically escaped.
* `args($args)` Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)` Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = ' ')` Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = ' ')` Pass multiple options to executable. The associative array contains
* `optionList($option, $value = [], $separator = ' ')` Pass an option with multiple values to executable. Value can be a string or array.
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `setOutput($output)` Sets the Console Output.

## SemVer


Helps to maintain `.semver` file.

```php
<?php
$this->taskSemVer('.semver')
     ->increment()
     ->run();
?>
```


* `__toString()`
* `version($version)`
* `setFormat($format)`
* `setMetadataSeparator($separator)`
* `setPrereleaseSeparator($separator)`
* `increment($what = 'patch')`
* `prerelease($tag = 'RC')`
* `metadata($data)`


