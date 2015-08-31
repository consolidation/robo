# Assets Tasks
## Less


Compiles less files.

```php
<?php
$this->taskLess([
    'less/default.less' => 'css/default.css'
])
->run();
?>
```

Use one of both less compilers in your project:

```
"leafo/lessphp": "~0.5",
"oyejorge/less.php": "~1.5"
```

Specify directory (string or array) for less imports lookup:
```php
<?php
$this->taskLess([
    'less/default.less' => 'css/default.css'
])
->importDir('less')
->compiler('lessphp')
->run();
?>
````

You can implement additional compilers by extending this task and adding a
method named after them and overloading the lessCompilers() method to
inject the name there.

* `compiler($compiler, array $options = Array ( ) )`  Sets the less compiler.
* `importDir($dirs)`  Sets import dir option for less compilers

## Minify


Minifies asset file (CSS or JS).

``` php
<?php
$this->taskMinify( 'web/assets/theme.css' )
     ->run()
?>
```
Please install additional dependencies to use:

```
"patchwork/jsqueeze": "~1.0",
"natxet/CssMin": "~3.0"
```


* `to($dst)`  Sets destination. Tries to guess type from it.
* `type($type)`  Sets type with validation.
* `singleLine($singleLine)`  Single line option for the JS minimisation.
* `keepImportantComments($keepImportantComments)`  keepImportantComments option for the JS minimisation.
* `specialVarRx($specialVarRx)`  specialVarRx option for the JS minimisation.
* `__toString()`  @return string

## Scss


Compiles scss files.

```php
<?php
$this->taskScss([
    'scss/default.scss' => 'css/default.css'
])
->run();
?>
```

Use the following scss compiler in your project:

```
"leafo/scssphp": "~0.1",
```

You can implement additional compilers by extending this task and adding a
method named after them and overloading the scssCompilers() method to
inject the name there.

* `compiler($compiler, array $options = Array ( ) )`  Sets the scss compiler.
* `addImportPath($path)`  Adds path to the importPath for scssphp
* `setImportPaths($paths)`  Sets the importPath for scssphp
* `setFormatter($formatterName)`  Sets the formatter for scssphp

