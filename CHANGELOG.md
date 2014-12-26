# Changelog

Thanks to everyone for using it and submitting pull requests! You are awesome.

Next release is going to be 0.5.0 with some refactorings included.
File structure will be changed to match PSR-4 standard.

Thanks to everyone who submitted useful tasks. It's better to have many tasks than few.
However, once the core is stabilized we plan to move some tasks out of main repo but make them easily installed via composer.

#### 0.4.7

* [Minify] Task added by @Rarst. Requires additional dependencies installed *2014-12-26*
* [Help command is populated from annotation](https://github.com/Codegyre/Robo/pull/71) by @jonsa *2014-12-26*
* Allow empty values as defaults to optional options by @jonsa *2014-12-26*
* `PHP_WINDOWS_VERSION_BUILD` constant is used to check for Windows in tasks by @boedah *2014-12-26*
* [Copy][EmptyDir] Fixed infinite loop by @boedah *2014-12-26*
* [ApiGen] Task added by @drobert *2014-12-26*
* [FileSystem] Equalized `copy` and `chmod` argument to defaults by @Rarst (BC break) *2014-12-26*
* [FileSystem]  Added missing umask argument to chmod() method of FileSystemStack by @Rarst
* [SemVer] Fixed file read and exit code
* [Codeception] fixed codeception coverageHtml option by @gunfrank *2014-12-26*
* [phpspec] Task added by @SebSept *2014-12-26*
* Shortcut options: if option name is like foo|f, assign f as shortcut by @jschnare *2014-12-26*
* [Rsync] Shell escape rsync exclude pattern by @boedah. Fixes #77 (BC break) *2014-12-26*
* [Npm] Task added by @AAlakkad *2014-12-26*

#### 0.4.6

* [Exec] Output from buffer is not spoiled by special chars *2014-10-17*
* [PHPUnit] detect PHPUnit on Windows or when is globally installed with Composer *2014-10-17*
* Output: added methods askDefault and confirm by @bkawakami *2014-10-17*
* [Svn] Task added by @anvi *2014-08-13*
* [Stack] added dir and printed options *2014-08-12*
* [ExecTask] now uses Executable trait with printed, dir, arg, option methods added *2014-08-12*


#### 0.4.5

* [Watch] bugfix: Watch only tracks last file if given array of files #46 *2014-08-05*
* All executable tasks can configure working directory with `dir` option
* If no value for an option is provided, assume it's a VALUE_NONE option. #47 by @pfaocle
* [Changelog] changed style *2014-06-27*
* [GenMarkDown] fixed formatting annotations *2014-06-27*

#### 0.4.4 06/05/2014

* Output can be disabled in all executable tasks by ->printed(false)
* disabled timeouts by default in ParallelExec
* better descriptions for Result output
* changed ParallelTask to display failed process in list
* Changed Output to be stored globally in Robo\Runner class
* Added **SshTask** by @boedah
* Added **RsyncTask** by @boedah
* false option added to proceess* callbacks in GenMarkDownTask to skip processing


#### 0.4.3 05/21/2014

*  added `SemVer` task by **@jadb**
*  `yell` output method added
*  task `FileSystemStack` added
* `MirrorDirTask` added by **@devster**
* switched to Symfony Filesystem component
* options can be used to commands
* array arguments can be used in commands

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