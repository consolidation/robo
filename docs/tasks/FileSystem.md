# FileSystem Tasks

## CleanDir


Deletes all files from specified dir, ignoring git files.

``` php
<?php
$this->taskCleanDir(['tmp','logs'])->run();
// as shortcut
$this->_cleanDir('app/cache');
?>
```



## CopyDir


Copies one dir into another

``` php
<?php
$this->taskCopyDir(['dist/config' => 'config'])->run();
// as shortcut
$this->_copyDir(['dist/config' => 'config']);
?>
```



## DeleteDir


Deletes dir

``` php
<?php
$this->taskDeleteDir('tmp')->run();
// as shortcut
$this->_deleteDir(['tmp', 'log']);
?>
```



## FilesystemStack


Wrapper for [Symfony FileSystem](http://symfony.com/doc/current/components/filesystem.html) Component.
Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.

``` php
<?php
$this->taskFileSystemStack()
     ->mkdir('logs')
     ->touch('logs/.gitignore')
     ->chgrp('www', 'www-data')
     ->symlink('/var/log/nginx/error.log', 'logs/error.log')
     ->run();

// one line
$this->_touch('.gitignore');
$this->_mkdir('logs');

?>
```

* `stopOnFail($stop = null)` 
* `mkdir($dir)` 
* `touch($file)` 
* `copy($from, $to, $force = null)` 
* `chmod($file, $permissions, $umask = null, $recursive = null)` 
* `remove($file)` 
* `rename($from, $to)` 
* `symlink($from, $to)` 
* `mirror($from, $to)` 
* `chgrp($file, $group)` 
* `chown($file, $user)` 

## MirrorDir


Mirrors a directory to another

``` php
<?php
$this->taskMirrorDir(['dist/config/' => 'config/'])->run();
// or use shortcut
$this->_mirrorDir(['dist/config/' => 'config/']);

?>
```




