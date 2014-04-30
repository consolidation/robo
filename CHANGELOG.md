# Changelog

#### 0.4.0 04/27/2014

* CommandInterface added to make generated commands to be passed to other tasks
* PHPUnit task improved
* Codeception task
* Bower task by @jadb

#### 0.3.3 04/25/2014

* Task descriptions taken from first line of annotations
* ParallelExec task added
* Symfony Process component used for execution


#### 0.3.3 02/25/2014

* PHPUnit basic task
* fixed doc generation


#### 0.3.5 02/21/2014

* changed generated init template


#### 0.3.4 02/21/2014

* [PackPhar] ->executable command will remove hashbang when generated stub file
* [Git][Exec] stopOnFail option for Git and Exec stack
* [ExecStack] shortcut for executing bash commands in stack

#### 0.3.2 02/20/2014

* release process now includes phar
* phar executable method added
* git checkout added
* phar pack created


#### 0.3.0 02/11/2014

* Dynamic configuration via magic methods
* added WriteToFile task
* Result class for managing exit codes and error messages

#### 0.2.0 01/29/2014

* Merged Tasks and Traits to same file
* Added Watcher task
* Added GitHubRelease task
* Added Changelog task
* Added ReplaceInFile task