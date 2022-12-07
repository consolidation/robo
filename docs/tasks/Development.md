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

* `filename($filename)`

 * `param string` $filename
* `setBody($body)`

 * `param string` $body
* `setHeader($header)`

 * `param string` $header
* `log($item)`

 * `param string` $item
* `anchor($anchor)`

 * `param string` $anchor
* `version($version)`

 * `param string` $version
* `changes(array $data)`

 * `param array` $data
* `change($change)`

 * `param string` $change
* `getChanges()`

 * `return array`
* `processLogRow($i)`

 * `param string` $i
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

* `docClass($item)`

 * `param string` $item
* `filterMethods($filterMethods)`

 * `param callable` $filterMethods
* `filterClasses($filterClasses)`

 * `param callable` $filterClasses
* `filterProperties($filterProperties)`

 * `param callable` $filterProperties
* `processClass($processClass)`

 * `param callable` $processClass
* `processClassSignature($processClassSignature)`

 * `param callable|false` $processClassSignature
* `processClassDocBlock($processClassDocBlock)`

 * `param callable|false` $processClassDocBlock
* `processMethod($processMethod)`

 * `param callable|false` $processMethod
* `processMethodSignature($processMethodSignature)`

 * `param callable|false` $processMethodSignature
* `processMethodDocBlock($processMethodDocBlock)`

 * `param callable|false` $processMethodDocBlock
* `processProperty($processProperty)`

 * `param callable|false` $processProperty
* `processPropertySignature($processPropertySignature)`

 * `param callable|false` $processPropertySignature
* `processPropertyDocBlock($processPropertyDocBlock)`

 * `param callable|false` $processPropertyDocBlock
* `reorder($reorder)`

 * `param callable` $reorder
* `reorderMethods($reorderMethods)`

 * `param callable` $reorderMethods
* `reorderProperties($reorderProperties)`

 * `param callable` $reorderProperties
* `filename($filename)`

 * `param string` $filename
* `prepend($prepend)`

 * `param string` $prepend
* `append($append)`

 * `param string` $append
* `text($text)`

 * `param string` $text
* `textForClass($item)`

 * `param string` $item
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

## Generate


Generate a Robo Task that is a wrapper around an existing class.

``` php
<?php
$this->taskGenerateTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')
  ->run();
```

* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output


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

 * `param string` $tag
* `draft($draft)`

 * `param bool` $draft
* `name($name)`

 * `param string` $name
* `description($description)`

 * `param string` $description
* `prerelease($prerelease)`

 * `param bool` $prerelease
* `comittish($comittish)`

 * `param string` $comittish
* `appendDescription($description)`

 * `param string` $description
* `changes(array $changes)`

 * `param array` $changes
* `change($change)`

 * `param string` $change
* `repo($repo)`

 * `param string` $repo
* `owner($owner)`

 * `param string` $owner
* `uri($uri)`

 * `param string` $uri
* `user($user)`

 * `param string` $user
* `password($password)`

 * `param string` $password
* `accessToken($token)`

 * `param string` $token
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

* `compress($compress = null)`

 * `param bool` $compress
* `stub($stub)`

 * `param string` $stub
* `addStripped($path, $file)`

 * `param string` $path
* `addFile($path, $file)`

 * `param string` $path
* `addFiles($files)`

 * `param \Symfony\Component\Finder\SplFileInfo[]` $files
* `executable($file)`

 * `param string` $file
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

 * `param string` $host
* `dir($path)`

 * `param string` $path
* `simulate($context)`

 * `param ` $context
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
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

 * `return string`
* `version($version)`

 * `param string` $version
* `setFormat($format)`

 * `param string` $format
* `setMetadataSeparator($separator)`

 * `param string` $separator
* `setPrereleaseSeparator($separator)`

 * `param string` $separator
* `increment($what = null)`

 * `param string` $what
* `prerelease($tag = null)`

 * `param string` $tag
* `metadata($data)`

 * `param array|string` $data


