# Assets Tasks

## ImageMinify


Minifies images. When the required minifier is not installed on the system
the task will try to download it from the [imagemin](https://github.com/imagemin) repository.

When the task is run without any specified minifier it will compress the images
based on the extension.

```php
$this->taskImageMinify('assets/images/*')
    ->to('dist/images/')
    ->run();
```

This will use the following minifiers:

- PNG: optipng
- GIF: gifsicle
- JPG, JPEG: jpegtran
- SVG: svgo

When the minifier is specified the task will use that for all the input files. In that case
it is useful to filter the files with the extension:

```php
$this->taskImageMinify('assets/images/*.png')
    ->to('dist/images/')
    ->minifier('pngcrush');
    ->run();
```

The task supports the following minifiers:

- optipng
- pngquant
- advpng
- pngout
- zopflipng
- pngcrush
- gifsicle
- jpegoptim
- jpeg-recompress
- jpegtran
- svgo (only minification, no downloading)

You can also specifiy extra options for the minifiers:

```php
$this->taskImageMinify('assets/images/*.jpg')
    ->to('dist/images/')
    ->minifier('jpegtran', ['-progressive' => null, '-copy' => 'none'])
    ->run();
```

This will execute as:
`jpegtran -copy none -progressive -optimize -outfile "dist/images/test.jpg" "/var/www/test/assets/images/test.jpg"`

* `to($target)`  Sets the target directory where the files will be copied to.
* `minifier($minifier, array $options = Array ( ) )`  Sets the minifier.

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
```

You can implement additional compilers by extending this task and adding a
method named after them and overloading the lessCompilers() method to
inject the name there.

* `importDir($dirs)`  Sets import directories
* `addImportPath($dir)`  Adds import directory
* `setImportPaths($dirs)`  Sets import directories
* `setFormatter($formatterName)`   * `param string` $formatterName
* `compiler($compiler, array $options = Array ( ) )`  Sets the compiler.

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
->importDir('assets/styles')
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

* `setFormatter($formatterName)`  Sets the formatter for scssphp
* `importDir($dirs)`  Sets import directories
* `addImportPath($dir)`  Adds import directory
* `setImportPaths($dirs)`  Sets import directories
* `compiler($compiler, array $options = Array ( ) )`  Sets the compiler.

