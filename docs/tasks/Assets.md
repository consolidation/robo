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

You can implement additional compilers by extending this task and adding a
method named after them and overloading the lessCompilers() method to
inject the name there.

* `compiler($compiler, array $options = Array ( ) )`  Sets the less compiler.

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

