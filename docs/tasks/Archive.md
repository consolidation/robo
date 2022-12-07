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

* `to($to)`

 * `param string` $to
* `preserveTopDirectory($preserve = null)`

 * `param bool` $preserve
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `getBuiltTask($fn, $args)`

 * `param string` $fn

## Pack


Creates a zip or tar archive.

``` php
<?php
$this->taskPack(
<archiveFile>)
->add('README')                         // Puts file 'README' in archive at the root
->add('project')                        // Puts entire contents of directory 'project' in archinve inside 'project'
->addFile('dir/file.txt', 'file.txt')   // Takes 'file.txt' from cwd and puts it in archive inside 'dir'.
->exclude(['dir\/.*.zip', '.*.md'])      // Add regex (or array of regex) to the excluded patterns list.
->run();
?>
```

* `archiveFile($archiveFile)`

 * `param string` $archiveFile
* `addFile($placementLocation, $filesystemLocation)`

 * `param string` $placementLocation Relative path and name of item to store in archive.
* `addDir($placementLocation, $filesystemLocation)`

 * `param string` $placementLocation Relative path and name of directory to store in archive.
* `add($item)`

 * `param string|array` $item If given a string, should contain the relative filesystem path to the
* `exclude($ignoreList)`

 * `param ` $ignoreList
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output


