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

* `Development\Changelog filename(string $filename)` 
* `Development\Changelog anchor(string $anchor)` 
* `Development\Changelog version(string $version)` 

* `askForChanges()` 
* `changes(array $data)` 
* `change($change)` 
* `getChanges()` 

## GenerateMarkdownDoc


Simple documentation generator from source files.
Takes classes, properties and methods with their docblocks and writes down a markdown file.

``` php
<?php
$this->taskGenerateMarkdownDoc('models.md')
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
$this->taskGenerateMarkdownDoc('models.md')
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

* ` docClass(string $classname)`  put a class you want to be documented
* ` filterMethods(\Closure $func)`  using callback function filter out methods that won't be documented
* ` filterClasses(\Closure $func)`  using callback function filter out classes that won't be documented
* ` filterProperties(\Closure $func)`  using callback function filter out properties that won't be documented
* ` processClass(\Closure $func)`  post-process class documentation
* ` processClassSignature(\Closure $func)`  post-process class signature. Provide *false* to skip.
* ` processClassDocBlock(\Closure $func)`  post-process class docblock contents. Provide *false* to skip.
* ` processMethod(\Closure $func)`  post-process method documentation. Provide *false* to skip.
* ` processMethodSignature(\Closure $func)`  post-process method signature. Provide *false* to skip.
* ` processMethodDocBlock(\Closure $func)`  post-process method docblock contents. Provide *false* to skip.
* ` processProperty(\Closure $func)`  post-process property documentation. Provide *false* to skip.
* ` processPropertySignature(\Closure $func)`  post-process property signature. Provide *false* to skip.
* ` processPropertyDocBlock(\Closure $func)`  post-process property docblock contents. Provide *false* to skip.
* ` reorder(\Closure $func)`  use a function to reorder classes
* ` reorderMethods(\Closure $func)`  use a function to reorder methods in class
* ` prepend($text)`  inserts text into beginning of markdown file
* ` append($text)`  inserts text in the end of markdown file




## GitHubRelease


Publishes new GitHub release.

``` php
<?php
$this->taskGitHubRelease('0.1.0')
  ->uri('Codegyre/Robo')
  ->askDescription()
  ->run();
?>
```

* ` tag(string $tag)` 
* ` name(string $name)` 
* ` body(string $body)` 
* ` draft(boolean $isDraft)` 
* ` prerelease(boolean $isPrerelease)` 
* ` comittish(string $branch)` 

* `askName()` 
* `askDescription()` 
* `askForChanges()` 
* `changes(array $changes)` 
* `uri($uri)` 
* `askAuth()` 

## PackPhar


Creates Phar

``` php
<?php
$pharTask = $this->PackPhar('package/codecept.phar')
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

* `compress($compress = null)`   * `param boolean` $compress
* `stub($stub)`   * `param` $stub
* `addStripped($path, $file)` 
* `addFile($path, $file)` 
* `executable($file)` 

## PhpServer


Runs PHP server and stops it when task finishes.

``` php
<?php
// run server in /public directory
$this->taskPhpServer(8000)
 ->dir('public')
 ->run();

// run with IP 0.0.0.0
$this->taskPhpServer(8000)
 ->host('0.0.0.0')
 ->run();

// execute server in background
$this->taskPhpServer(8000)
 ->background()
 ->run();
?>
```

#### *public static* instances* `host($host)` 
* `dir($path)`  changes working directory of command
* `background()`  Executes command in background mode (asynchronously)
* `timeout($timeout)`  Stop command if it runs longer then $timeout in seconds
* `idleTimeout($timeout)`  Stops command if it does not output something for a while
* `arg($arg)`  Pass argument to executable
* `args($args)`  Pass methods parameters as arguments to executable
* `option($option, $value = null)`  Pass option to executable. Options are prefixed with `--` , value can be provided in second parameter
* `printed($arg)`  Should command output be printed

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
* `setFormat($format)` 
* `setMetadataSeparator($separator)` 
* `setPrereleaseSeparator($separator)` 
* `increment($what = null)` 
* `prerelease($tag = null)` 
* `metadata($data)` 

