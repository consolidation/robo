# Assets Tasks
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
* `__toString()`  @return string

