# Assets Tasks

## ImageMinify


Minifies images.

When the task is run without any specified minifier it will compress the
images based on the extension.

```php
$this->taskImageMinify('assets/images/*')
    ->to('dist/images/')
    ->run();
```

This will use the following minifiers based in the extension:

- PNG: optipng
- GIF: gifsicle
- JPG, JPEG: jpegtran
- SVG: svgo

When the required minifier is not installed on the system the task will try
to download it from the [imagemin](https://github.com/imagemin) repository
into a local directory.
This directory is `vendor/bin/` by default and may be changed:

```php
$this->taskImageMinify('assets/images/*')
    ->setExecutableDir('/tmp/imagemin/bin/)
    ->to('dist/images/')
    ->run();
```

When the minifier is specified the task will use that for all the input
files. In that case it is useful to filter the files with the extension:

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

* `setExecutableDir($directory)`

 * `param string` $directory
* `to($target)`

 * `param string` $target
* `minifier($minifier, array $options = array ( ))`

 * `param string` $minifier
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

* `importDir($dirs)`

 * `see` CssPreprocessor::setImportPaths
* `addImportPath($dir)`

 * `param string` $dir
* `setImportPaths($dirs)`

 * `param array|string` $dirs
* `setFormatter($formatterName)`

 * `param string` $formatterName
* `compiler($compiler, array $options = array ( ))`

 * `param string` $compiler
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

## Minify


Minifies an asset file (CSS or JS).

``` php
<?php
$this->taskMinify('web/assets/theme.css')
     ->run()
?>
```
Please install additional packages to use this task:

```
composer require patchwork/jsqueeze:^2.0
composer require natxet/cssmin:^3.0
```

* `to($dst)`

 * `param string` $dst
* `type($type)`

 * `param string` $type Allowed values: "css", "js".
* `singleLine($singleLine)`

 * `param bool` $singleLine
* `keepImportantComments($keepImportantComments)`

 * `param bool` $keepImportantComments
* `specialVarRx($specialVarRx)`

 * `param bool` $specialVarRx
* `__toString()`

 * `return string`
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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
"scssphp/scssphp ": "~1.0.0",
```

You can implement additional compilers by extending this task and adding a
method named after them and overloading the scssCompilers() method to
inject the name there.

* `setFormatter($formatterName)`

 * `link` https://scssphp.github.io/scssphp/docs/#output-formatting
* `importDir($dirs)`

 * `see` CssPreprocessor::setImportPaths
* `addImportPath($dir)`

 * `param string` $dir
* `setImportPaths($dirs)`

 * `param array|string` $dirs
* `compiler($compiler, array $options = array ( ))`

 * `param string` $compiler
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output


