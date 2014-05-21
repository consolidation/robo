# Changelog

#### 0.4.2 05/21/2014

*  task added


#### 0.4.2 05/09/2014

* ask can now hide answers
* Trait Executable added to provide standard way for passing arguments and options
* added ComposerDumpAutoload task by **@pmcjury**
* added FileSystem task by **@jadb**
* added CommonStack metatsk to have similar interface for all stacked tasks by **@jadb**
* arguments and options can be passed into variable and used in exec task
* passing options into commands


#### 0.4.1 05/05/2014

* [BC] `taskGit` task renamed to `taskGitStack` for compatibility
* unit and functional tests added
* all command tasks now use Symfony\Process to execute them
* enabled Bower and Concat tasks
* added `printed` param to Exec task
* codeception `suite` method now returns `$this`
* timeout options added to Exec task


#### 0.4.0 04/27/2014

* Codeception task added
* PHPUnit task improved
* Bower task added by @jadb
* ParallelExec task added
* Symfony Process component used for execution
* Task descriptions taken from first line of annotations
* `CommandInterface` added to use tasks as parameters

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