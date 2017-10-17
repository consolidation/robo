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
$this->taskChangelog()
 ->version($version)
 ->askForChanges()
 ->run();
?>
```

* `filename($filename)`   * `param string` $filename
* `setBody($body)`  Sets the changelog body text.
* `setHeader($header)`   * `param string` $header
* `log($item)`   * `param string` $item
* `anchor($anchor)`   * `param string` $anchor
* `version($version)`   * `param string` $version
* `changes(array $data)`   * `param array` $data
* `change($change)`   * `param string` $change
* `getChanges()`  @return array
* `processLogRow($i)`   * `param` $i

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

By default this task generates a documentation for each public method of a class.
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

* `docClass($item)`  Put a class you want to be documented.
* `filterMethods($filterMethods)`  Using a callback function filter out methods that won't be documented.
* `filterClasses($filterClasses)`  Using a callback function filter out classes that won't be documented.
* `filterProperties($filterProperties)`  Using a callback function filter out properties that won't be documented.
* `processClass($processClass)`  Post-process class documentation.
* `processClassSignature($processClassSignature)`  Post-process class signature. Provide *false* to skip.
* `processClassDocBlock($processClassDocBlock)`  Post-process class docblock contents. Provide *false* to skip.
* `processMethod($processMethod)`  Post-process method documentation. Provide *false* to skip.
* `processMethodSignature($processMethodSignature)`  Post-process method signature. Provide *false* to skip.
* `processMethodDocBlock($processMethodDocBlock)`  Post-process method docblock contents. Provide *false* to skip.
* `processProperty($processProperty)`  Post-process property documentation. Provide *false* to skip.
* `processPropertySignature($processPropertySignature)`  Post-process property signature. Provide *false* to skip.
* `processPropertyDocBlock($processPropertyDocBlock)`  Post-process property docblock contents. Provide *false* to skip.
* `reorder($reorder)`  Use a function to reorder classes.
* `reorderMethods($reorderMethods)`  Use a function to reorder methods in class.
* `reorderProperties($reorderProperties)`   * `param callable` $reorderProperties
* `filename($filename)`   * `param string` $filename
* `prepend($prepend)`  Inserts text at the beginning of markdown file.
* `append($append)`  Inserts text at the end of markdown file.
* `text($text)`   * `param string` $text
* `textForClass($item)`   * `param string` $item

## Generate


Generate a Robo Task that is a wrapper around an existing class.

``` php
<?php
$this->taskGenerateTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')
  ->run();
```




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

* `tag($tag)`   * `param string` $tag
* `draft($draft)`   * `param bool` $draft
* `name($name)`   * `param string` $name
* `description($description)`   * `param string` $description
* `prerelease($prerelease)`   * `param bool` $prerelease
* `comittish($comittish)`   * `param string` $comittish
* `appendDescription($description)`   * `param string` $description
* `changes(array $changes)` 
* `change($change)`   * `param string` $change
* `repo($repo)`   * `param string` $repo
* `owner($owner)`   * `param string` $owner
* `uri($uri)`   * `param string` $uri
* `user($user)`   * `param string` $user
* `password($password)`   * `param` $password
* `accessToken($token)`   * `param` $accessToken

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

* `compress($compress = null)`   * `param bool` $compress
* `stub($stub)`   * `param string` $stub
* `addStripped($path, $file)`   * `param string` $path
* `addFile($path, $file)`   * `param string` $path
* `addFiles($files)`   * `param \Symfony\Component\Finder\SplFileInfo[]` $files
* `executable($file)`   * `param string` $file

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

* `host($host)`   * `param string` $host
* `dir($path)`   * `param string` $path
* `simulate($context)`  {@inheritdoc}
* `arg($arg)`  Pass argument to executable. Its value will be automatically escaped.
* `args($args)`  Pass methods parameters as arguments to executable. Argument values
* `rawArg($arg)`  Pass the provided string in its raw (as provided) form as an argument to executable.
* `option($option, $value = null, $separator = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter.
* `options(array $options, $separator = null)`  Pass multiple options to executable. The associative array contains
* `optionList($option, $value = null, $separator = null)`  Pass an option with multiple values to executable. Value can be a string or array.

## SemVer


Helps to maintain `.semver` file.

```php
<?php
$this->taskSemVer('.semver')
     ->increment()
     ->run();
?>
```


* `__toString()`  @return string
* `version($version)` 
* `setFormat($format)`   * `param string` $format
* `setMetadataSeparator($separator)`   * `param string` $separator
* `setPrereleaseSeparator($separator)`   * `param string` $separator
* `increment($what = null)`   * `param string` $what
* `prerelease($tag = null)`   * `param string` $tag
* `metadata($data)`   * `param array|string` $data

