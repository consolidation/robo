# Styling Output

You may customize Robo's command line and log output in a variety of ways.


### Using your own IO trait

By default, Robo uses the `\Robo\Common\IO` trait to provide methods like `say()` and `yell()`. You may extend this trait with your own custom IO trait and thus override the default `say()`, `yell()` methods with your own custom implementations.

First, define create your custom IO trait:

```php
<?php

namespace My\Application\Robo\Common;

trait IO {

  use \Robo\Common\IO;

  protected function say($text) {
    // Customize output.
  }
}
````

Then, in your command file, use your custom trait rather than `\Robo\Common\IO`:

```php
<?php
class RoboFile extends \Robo\Tasks
{
  use My\Application\Robo\Common\IO;
}

```

### Using your own LogOutputStyle

All logged messages are passed through a Log Output Style before being written to the screen.  If you are [creating your own container](framework.md#using-your-own-dependency-injection-container-with-robo-advanced) then you may replace the default `Robo\Log\RoboLogStyle` with your own Log style.


```php
<?php
 
 namespace My\Application\Robo\Log;
 
 use Robo\Log\RoboLogStyle;
 
 class MyLogStyle extends RoboLogStyle {

   protected function formatMessage($label, $message, $context, $taskNameStyle, $messageStyle = '')
   {
     $message = parent::formatMessage($label, $message, $context, $taskNameStyle, $messageStyle);
 
     return $message;
   }
 }
````
 
 ```php
 <?php
$container->share('logStyler', MyLogStyle::class);
```


