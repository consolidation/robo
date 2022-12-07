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

* `to($dst)`

 * `param string` $dst
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

* `filename($filename)`

 * `param string` $filename
* `from($from)`

 * `param string|string[]` $from
* `to($to)`

 * `param string|string[]` $to
* `regex($regex)`

 * `param string` $regex
* `limit($limit)`

 * `param int` $limit
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output


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

* `complete()`

 Delete this file when our collection completes.
* `filename($filename)`

 * `param string` $filename
* `append($append = null)`

 * `param bool` $append
* `line($line)`

 * `param string` $line
* `lines(array $lines)`

 * `param array` $lines
* `text($text)`

 * `param string` $text
* `textFromFile($filename)`

 * `param string` $filename
* `place($name, $val)`

 * `param string` $name
* `replace($string, $replacement)`

 * `param string` $string
* `regexReplace($pattern, $replacement)`

 * `param string` $pattern
* `appendIfMatches($pattern, $text)`

 * `param string` $pattern
* `appendUnlessMatches($pattern, $text)`

 * `param string` $pattern
* `originalContents()`

 * `return string`
* `wouldChange()`

 * `return bool`
* `getPath()`

 * `return string`
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

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

* `filename($filename)`

 * `param string` $filename
* `append($append = null)`

 * `param bool` $append
* `line($line)`

 * `param string` $line
* `lines(array $lines)`

 * `param array` $lines
* `text($text)`

 * `param string` $text
* `textFromFile($filename)`

 * `param string` $filename
* `place($name, $val)`

 * `param string` $name
* `replace($string, $replacement)`

 * `param string` $string
* `regexReplace($pattern, $replacement)`

 * `param string` $pattern
* `appendIfMatches($pattern, $text)`

 * `param string` $pattern
* `appendUnlessMatches($pattern, $text)`

 * `param string` $pattern
* `originalContents()`

 * `return string`
* `wouldChange()`

 * `return bool`
* `getPath()`

 * `return string`
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output

