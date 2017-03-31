# Archive Tasks
## Extract


Extracts an archive.

Note that often, distributions are packaged in tar or zip archives
where the topmost folder may contain variable information, such as
the release date, or the version of the package.  This information
is very useful when unpacking by hand, but arbitrarily-named directories
are much less useful to scripts.  Therefore, by default, Extract will
remove the top-level directory, and instead store all extracted files
into the directory specified by $archivePath.

To keep the top-level directory when extracting, use
`preserveTopDirectory(true)`.

``` php
<?php
$this->taskExtract($archivePath)
 ->to($destination)
 ->preserveTopDirectory(false) // the default
 ->run();
?>
```

* `to($to)`  Location to store extracted files.
* `preserveTopDirectory($preserve = null)`   * `param bool` $preserve

## Pack


Creates a zip or tar archive.

``` php
<?php
$this->taskPack(
<archiveFile>)
->add('README')                         // Puts file 'README' in archive at the root
->add('project')                        // Puts entire contents of directory 'project' in archinve inside 'project'
->addFile('dir/file.txt', 'file.txt')   // Takes 'file.txt' from cwd and puts it in archive inside 'dir'.
->run();
?>
```

* `archiveFile($archiveFile)`   * `param string` $archiveFile
* `addFile($placementLocation, $filesystemLocation)`  Add an item to the archive. Like file_exists(), the parameter
* `addDir($placementLocation, $filesystemLocation)`  Alias for addFile, in case anyone has angst about using
* `add($item)`  Add a file or directory, or list of same to the archive.

