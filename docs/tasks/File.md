# File Tasks
## Concat


Merges files into one. Used for preparing assets.

``` php
<?php
$this->taskConcat([
     'web/assets/screen.css',
     'web/assets/print.css',
     'web/assets/theme.css'
 ])
 ->to('web/assets/style.css')
 ->run()
?>
```

* `to($dst)`  set the destination file

## Replace


Performs search and replace inside a files.

``` php
<?php
$this->taskReplaceInFile('VERSION')
 ->from('0.2.0')
 ->to('0.3.0')
 ->run();

$this->taskReplaceInFile('README.md')
 ->from(date('Y')-1)
 ->to(date('Y'))
 ->run();

$this->taskReplaceInFile('config.yml')
 ->regex('~^service:~')
 ->to('services:')
 ->run();

$this->taskReplaceInFile('box/robo.txt')
 ->from(array('##dbname##', '##dbhost##'))
 ->to(array('robo', 'localhost'))
 ->run();
?>
```

* `filename($filename)`   * `param string` $filename
* `from($from)`  String(s) to be replaced.
* `to($to)`  Value(s) to be set as a replacement.
* `regex($regex)`  Regex to match string to be replaced.

## TmpFile


Create a temporary file that is automatically cleaned up
once the task collection is is part of completes. When created,
it is given a random filename.

This temporary file may be manipulated exacatly like taskWrite().
It is deleted as soon as the collection it is a part of completes
or rolls back.

``` php
<?php
$collection = $this->collectionBuilder();
$tmpFilePath = $collection->taskTmpFile()
     ->line('-----')
     ->line(date('Y-m-d').' '.$title)
     ->line('----')
     ->getPath();
$collection->run();
?>
```

* `complete()`  Delete this file when our collection completes.
* `filename($filename)`   * `param string` $filename
* `append($append = null)`   * `param bool` $append
* `line($line)`  add a line.
* `lines(array $lines)`  add more lines.
* `text($text)`  add a text.
* `textFromFile($filename)`  add a text from a file.
* `place($name, $val)`  substitute a placeholder with value, placeholder must be enclosed by `{}`.
* `replace($string, $replacement)`  replace any string with value.
* `regexReplace($pattern, $replacement)`  replace any string with value using regular expression.
* `appendIfMatches($pattern, $text)`  Append the provided text to the end of the buffer if the provided
* `appendUnlessMatches($pattern, $text)`  Append the provided text to the end of the buffer unless the provided
* `originalContents()`  @return string
* `wouldChange()`  @return bool
* `getPath()`  @return string

## Write


Writes to file.

``` php
<?php
$this->taskWriteToFile('blogpost.md')
     ->line('-----')
     ->line(date('Y-m-d').' '.$title)
     ->line('----')
     ->run();
?>
```

* `filename($filename)`   * `param string` $filename
* `append($append = null)`   * `param bool` $append
* `line($line)`  add a line.
* `lines(array $lines)`  add more lines.
* `text($text)`  add a text.
* `textFromFile($filename)`  add a text from a file.
* `place($name, $val)`  substitute a placeholder with value, placeholder must be enclosed by `{}`.
* `replace($string, $replacement)`  replace any string with value.
* `regexReplace($pattern, $replacement)`  replace any string with value using regular expression.
* `appendIfMatches($pattern, $text)`  Append the provided text to the end of the buffer if the provided
* `appendUnlessMatches($pattern, $text)`  Append the provided text to the end of the buffer unless the provided
* `originalContents()`  @return string
* `wouldChange()`  @return bool
* `getPath()`  @return string

