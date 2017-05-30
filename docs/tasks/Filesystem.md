# Filesystem Tasks

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
$this->_copyDir('dist/config', 'config');
?>
```

* `dirPermissions($value)`  Sets the default folder permissions for the destination if it doesn't exist
* `exclude($exclude = null)`  List files to exclude.
* `overwrite($overwrite)`  Destination files newer than source files are overwritten.

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


Wrapper for [Symfony Filesystem](http://symfony.com/doc/current/components/filesystem.html) Component.
Comands are executed in stack and can be stopped on first fail with `stopOnFail` option.

``` php
<?php
$this->taskFilesystemStack()
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

* `$this mkdir(string|array|\Traversable $dir, int $mode = 0777)` 
* `$this touch(string|array|\Traversable $file, int $time = null, int $atime = null)` 
* `$this copy(string $from, string $to, bool $force = false)` 
* `$this chmod(string|array|\Traversable $file, int $permissions, int $umask = 0000, bool $recursive = false)` 
* `$this chgrp(string|array|\Traversable $file, string $group, bool $recursive = false)` 
* `$this chown(string|array|\Traversable $file, string $user, bool $recursive = false)` 
* `$this remove(string|array|\Traversable $file)` 
* `$this rename(string $from, string $to, bool $force = false)` 
* `$this symlink(string $from, string $to, bool $copyOnWindows = false)` 
* `$this mirror(string $from, string $to, \Traversable $iterator = null, array $options = [])` 

* `stopOnFail($stop = null)`   * `param bool` $stop

## FlattenDir


Searches for files in a nested directory structure and copies them to
a target directory with or without the parent directories. The task was
inspired by [gulp-flatten](https://www.npmjs.com/package/gulp-flatten).

Example directory structure:

```
└── assets
    ├── asset-library1
    │   ├── README.md
    │   └── asset-library1.min.js
    └── asset-library2
        ├── README.md
        └── asset-library2.min.js
```

The following code will search the `*.min.js` files and copy them
inside a new `dist` folder:

``` php
<?php
$this->taskFlattenDir(['assets/*.min.js' => 'dist'])->run();
// or use shortcut
$this->_flattenDir('assets/*.min.js', 'dist');
?>
```

You can also define the target directory with an additional method, instead of
key/value pairs. More similar to the gulp-flatten syntax:

``` php
<?php
$this->taskFlattenDir(['assets/*.min.js'])
  ->to('dist')
  ->run();
?>
```

You can also append parts of the parent directories to the target path. If you give
the value `1` to the `includeParents()` method, then the top parent will be appended
to the target directory resulting in a path such as `dist/assets/asset-library1.min.js`.

If you give a negative number, such as `-1` (the same as specifying `array(0, 1)` then
the bottom parent will be appended, resulting in a path such as
`dist/asset-library1/asset-library1.min.js`.

The top parent directory will always be starting from the relative path to the current
directory. You can override that with the `parentDir()` method. If in the above example
you would specify `assets`, then the top parent directory would be `asset-library1`.

``` php
<?php
$this->taskFlattenDir(['assets/*.min.js' => 'dist'])
  ->parentDir('assets')
  ->includeParents(1)
  ->run();
?>
```

* `dirPermissions($permission)`  Sets the default folder permissions for the destination if it does not exist.
* `includeParents($parents)`  Sets the value from which direction and how much parent dirs should be included.
* `parentDir($dir)`  Sets the parent directory from which the relative parent directories will be calculated.
* `to($target)`  Sets the target directory where the files will be copied to.

## MirrorDir


Mirrors a directory to another

``` php
<?php
$this->taskMirrorDir(['dist/config/' => 'config/'])->run();
// or use shortcut
$this->_mirrorDir('dist/config/', 'config/');

?>
```



## TmpDir


Create a temporary directory that is automatically cleaned up
once the task collection is is part of completes.

Use WorkDir if you do not want the directory to be deleted.

``` php
<?php
// Delete on rollback or on successful completion.
// Note that in this example, everything is deleted at
// the end of $collection->run().
$collection = $this->collectionBuilder();
$tmpPath = $collection->tmpDir()->getPath();
$collection->taskFilesystemStack()
          ->mkdir("$tmpPath/log")
          ->touch("$tmpPath/log/error.txt");
$collection->run();
// as shortcut (deleted when program exits)
$tmpPath = $this->_tmpDir();
?>
```

* `cwd($shouldChangeWorkingDirectory = null)`  Flag that we should cwd to the temporary directory when it is
* `complete()`  Delete this directory when our collection completes.
* `getPath()`  Get a reference to the path to the temporary directory, so that

## WorkDir


Create a temporary working directory that is automatically renamed to its
final desired location if all of the tasks in the collection succeed.  If
there is a rollback, then the working directory is deleted.

``` php
<?php
$collection = $this->collectionBuilder();
$workingPath = $collection->workDir("build")->getPath();
$collection->taskFilesystemStack()
          ->mkdir("$workingPath/log")
          ->touch("$workingPath/log/error.txt");
$collection->run();
?>
```

* `complete()`  Move our working directory into its final destination once the
* `rollback()`  Delete our working directory
* `getPath()`  Get a reference to the path to the temporary directory, so that
* `cwd($shouldChangeWorkingDirectory = null)`  Flag that we should cwd to the temporary directory when it is

