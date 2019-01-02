# Changelog

### 1.4.0 - 1.4.3 1/2/2019

* BUGFIX: Back out 1.3.5, which contained breaking changes. Create a 1.x branch for continuation of compatible versions, and move breaking code to 2.x development (on master branch).

### 1.3.4 12/20/2018

* Allow for aborting completions or rollbacks by James Sansbury (#815)
* BUGFIX: Allow commands to declare '@param InputInterface' to satisfy code style checks

### 1.3.3 12/13/2018

* Add StdinHandler to the standard Robo DI container (#814)
* BUGFIX: Add test to ensure rollback order is in reverse by James Sansbury (#812)
* BUGFIX: Fix the main Robo script entrypoint to work as a phar. (#811)

### 1.3.2 11/21/2018

* Update to Composer Test Scenarios 3 (#803)
* Support Windows line endings in ".semver" file by Cédric Belin (#788)
* Ensure that environment variables are preserved in Exec by James Sansbury (#769)
* Correct Doxygen in \Robo\Task\Composer\loadTasks. (#772)

### 1.3.1 8/17/2018

* Move self:update command to consolidation/self-update project.
* Fix overzealous shebang function (#759)
* Actualize RoboFile of Codeception project link url in RADME.php by Valerij Ivashchenko (#756)
* Workaround - Move g1a/composer-test-scenarios from require-dev to require.
* Add --no-progress --no-suggest back in.
* Tell dependencies.io to use --no-dev when determining if a PR should be made.
* Omit --no-dev when the PR is actually being composed.
* Add `Events` as third parameter in watch function (#751)

### 1.3.0 5/26/2018

* Add EnvConfig to Robo: set configuration values via environment variables (#737)

### 1.2.4 5/25/2018

* Update 'Robo as a Framework' documentation to recommend https://github.com/g1a/starter
* Allow CommandStack to exec other tasks by Scott Falkingham (#726)
* Fix double escape when specifying a remoteShell with rsync by Rob Peck (#715)

### 1.2.3 4/5/2018

* Hide progress indicator prior to 'exec'. (#707)
* Dependencies.io config for version 2 preview by Dave Gaeddert (#699)
* Fix path to test script in try:para
* Correctly parameterize the app name in the self:update command help text.
* Refuse to start 'release' script if phar.readonly is set.

### 1.2.2 2/27/2018

* Experimental robo plugin mechanism (backwards compatibility not yet guarenteed)
* Allow traits to be documented
* Do not export scenarios directory
* *Breaking* Typo in `\Robo\Runner:errorCondtion()` fixed as `\Robo\Runner:errorCondition()`.

### 1.2.1 12/28/2017

* Fixes to tests / build only.

### 1.2.0 12/12/2017

* Support Symfony 4 Components (#651)
* Test multiple composer dependency permutations with https://github.com/greg-1-anderson/composer-test-scenarios

### 1.1.5 10/25/2017

* Load option default values from $input for all options defined in the Application's input definition (#642)
* BUGFIX: Store global options in 'options' namespace rather than at the top level of config.

### 1.1.4 10/16/2017

* Update order of command event hooks so that the option settings are injected prior to configuration being injected, so that dynamic options are available for config injection. (#636)
* Add shallow clone method to GithubStack task. by Stefan Lange (#633)
* Make Changelog task more flexible. by Matthew Grasmick(#631)
* Adding accessToken() to GitHub task. by Matthew Grasmick (#630)

### 1.1.3 09/23/2017

* Add self:update command to update Robo phar distributions to the latest available version on GitHub. by Alexander Menk
* Fix Robo\Task\Docker\Base to implement CommandInterface. by Alexei Gorobet (#625)
* Add overwrite argument to Robo\Task\Filesystem\loadShortcuts.php::_rename by Alexei Gorobets (#624)
* Add failGroup() method for Codeception run command. by Max Gorovenko (#622)
* Set up composer-lock-updater on cron. (#618)
* Fix robo.yml loader by exporting processor instead of loader. By thomscode (#612)

### 1.1.2 07/28/2017

* Inject option default values in help (#607)
* Add noRebuild() method for Codeception run command. By Max Gorovenko (#603)

### 1.1.1 07/07/2017

* Add an option to wait an interval of time between parallel processes. By Gemma Pou #601
* Do not print dire messages about Robo bootstrap problems when a valid command (e.g. help, list, init, --version) runs. #502

### 1.1.0 06/29/2017

* Configuration for multiple commands or multiple tasks may now be shared by attaching the configuration values to the task namespace or the command group. #597
* *Breaking* Task configuration taken from property `task.PARTIAL_NAMESPACE.CLASSNAME.settings` instead of `task.CLASSNAME.settings`. Breaks backwards compatibility only with experimental configuration features introduced in version 1.0.6. Config is now stable, as of this release; there will be no more breaking config changes until Robo 2.0. #596

### 1.0.8 06/02/2017

* Fix regression in 1.0.7: Allow tasks to return results of types other than \Robo\Result. #585
* Allow Copydir exclude method to specify subfolders by Alex Skrypnyk #590
* Add composer init task, and general rounding out of composer tasks. #586
* Enhance SemVer task so that it can be used with files or strings. #589

#### 1.0.7 05/30/2017

* Add a state system for collections to allow tasks to pass state to later tasks.
* Ensure that task results are returned when in stopOnFail() mode.
* Make rawArg() and detectInteractive chainable. By Matthew Grasmick #553 #558
* [CopyDir] Use Symfony Filesystem. By malikkotob #555
* [Composer] Implement CommandInterface. By Ivan Borzenkov #561

#### 1.0.6 03/31/2017

* Add configuration features to inject values into commandline option and task setter methods. Experimental; incompatible changes may be introduced prior to the stable release of configuration in version 1.1.0.

#### 1.0.5 11/23/2016

* Incorporate word-wrapping from output-formatters 3.1.5
* Incorporate custom event handlers from annotated-command 2.2.0

#### 1.0.4 11/15/2016

* Updated to latest changes in `master` branch. Phar and tag issues.

#### 1.0.0 10/10/2016

* [Collection] Add tasks to a collection, and implement them as a group with rollback
   * Tasks may be added to a collection via `$collection->add($task);`
   * `$collection->run();` runs all tasks in the collection
   * `$collection->addCode(function () { ... } );` to add arbitrary code to a collection
   * `$collection->progressMessage(...);` will log a message
   * `$collection->rollback($task);` and `$collection->rollbackCode($callable);` add a rollback function to clean up after a failed task
   * `$collection->completion($task);` and `$collection->completionCode($callable);` add a function that is called once the collection completes or rolls back.
   * `$collection->before();` and `$collection->after();` can be used to add a task or function that runs before or after (respectively) the specified named task. To use this feature, tasks must be given names via an optional `$taskName` parameter when they are added.
   * Collections may be added to collections, if desired. 
* [CollectionBuilder] Create tasks and add them to a collection in a single operation.
   * `$this->collectionBuilder()->taskExec('pwd')->taskExec('ls')->run()`
* Add output formatters
   * If a Robo command returns a string, or a `Result` object with a `$message`, then it will be printed
   * Commands may be annotated to describe output formats that may be used
   * Structured arrays returned from function results may be converted into different formats, such as a table, yml, json, etc.
   * Tasks must `use TaskIO` for output methods. It is no longer possible to `use IO` from a task. For direct access use `Robo::output()` (not recommended).   
* Use league/container to do Dependency Injection
   * *Breaking* Tasks' loadTasks traits must use `$this->task(TaskClass::class);` instead of `new TaskClass();`
   * *Breaking* Tasks that use other tasks must use `$this->collectionBuilder()->taskName();` instead of `new TaskClass();` when creating task objects to call. Implement `Robo\Contract\BuilderAwareInterface` and use `Robo\Contract\BuilderAwareTrait` to add the `collectionBuilder()` method to your task class.
* *Breaking* The `arg()`, `args()` and `option()` methods in CommandArguments now escape the values passed in to them. There is now a `rawArg()` method if you need to add just one argument that has already been escaped.
* *Breaking* taskWrite is now called taskWriteToFile
* [Extract] task added
* [Pack] task added
* [TmpDir], [WorkDir] and [TmpFile] tasks added
* Support Robo scripts that allows scripts starting with `#!/usr/bin/env robo` to define multiple robo commands.  Use `#!/usr/bin/env robo run` to define a single robo command implemented by the `run()` method.
* Provide ProgresIndicatorAwareInterface and ProgressIndicatorAwareTrait that make it easy to add progress indicators to tasks
* Add --simulate mode that causes tasks to print what they would have done, but make no changes
* Add `robo generate:task` code-generator to make new stack-based task wrappers around existing classes
* Add `robo sniff` by @dustinleblanc. Runs the PHP code sniffer followed by the code beautifier, if needed.
* Implement ArrayInterface for Result class, so result data may be accessed like an array 
* Defer execution of operations in taskWriteToFile until the run() method
* Add Write::textIfMatch() for taskWriteToFile
* ResourceExistenceChecker used for error checking in DeleteDir, CopyDir, CleanDir and Concat tasks by @burzum
* Provide ResultData base class for Result; ResultData may be used in instances where a specific `$task` instance is not available (e.g. in a Robo command)
* ArgvInput now available via $this->getInput() in RoboFile by Thomas Spigel
* Add optional message to git tag task by Tim Tegeler
* Rename 'FileSystem' to 'Filesystem' wherever it occurs.
* Current directory is changed with `chdir` only if specified via the `--load-from` option (RC2)

#### 0.6.0 10/30/2015

* Added `--load-from` option to make Robo start RoboFiles from other directories. Use it like `robo --load-from /path/to/where/RobFile/located`.
* Robo will not ask to create RoboFile if it does not exist, `init` command should be used.
* [ImageMinify] task added by @gabor-udvari
* [OpenBrowser] task added by @oscarotero
* [FlattenDir] task added by @gabor-udvari
* Robo Runner can easily extended for custom runner by passing RoboClass and RoboFile parameters to constructor. By @rdeutz See #232

#### 0.5.4 08/31/2015

* [WriteToFile] Fixed by @gabor-udvari: always writing to file regardless whether any changes were made or not. This can bring the taskrunner into an inifinite loop if a replaced file is being watched.
* [Scss] task added, requires `leafo/scssphp` library to compile by @gabor-udvari
* [PhpSpec] TAP formatter added by @orls
* [Less] Added ability to set import dir for less compilers by @MAXakaWIZARD
* [Less] fixed passing closure as compiler by @pr0nbaer
* [Sass] task added by *2015-08-31*

#### 0.5.3 07/15/2015

 * [Rsync] Ability to use remote shell with identity file by @Mihailoff
 * [Less] Task added by @burzum
 * [PHPUnit] allow to test specific files with `files` parameter by @burzum.
 * [GitStack] `tag` added by @SebSept
 * [Concat] Fixing concat, it breaks some files if there is no new line. @burzum *2015-03-03-13*
 * [Minify] BC fix to support Jsqueeze 1.x and 2.x @burzum *2015-03-12*
 * [PHPUnit] Replace log-xml with log-junit @vkunz *2015-03-06*
 * [Minify] Making it possible to pass options to the JS minification @burzum *2015-03-05*
 * [CopyDir] Create destination recursively @boedah *2015-02-28*

#### 0.5.2 02/24/2015

* [Phar] do not compress phar if more than 1000 files included (causes internal PHP error) *2015-02-24*
* _copyDir and _mirrorDir shortcuts fixed by @boedah *2015-02-24*
* [File\Write] methods replace() and regexReplace() added by @asterixcapri *2015-02-24*
* [Codecept] Allow to set custom name of coverage file raw name by @raistlin *2015-02-24*
* [Ssh] Added property `remoteDir` by @boedah *2015-02-24*
* [PhpServer] fixed passing arguments to server *2015-02-24*


#### 0.5.1 01/27/2015

* [Exec] fixed execution of background jobs, processes persist till the end of PHP script *2015-01-27*
* [Ssh] Fixed SSH task by @Butochnikov *2015-01-27*
* [CopyDir] fixed shortcut usage by @boedah *2015-01-27*
* Added default value options for Configuration trait by @TamasBarta *2015-01-27*

#### 0.5.0 01/22/2015

Refactored core

* All traits moved to `Robo\Common` namespace
* Interfaces moved to `Robo\Contract` namespace
* All task extend `Robo\Task\BaseTask` to use common IO.
* All classes follow PSR-4 standard
* Tasks are loaded into RoboFile with `loadTasks` trait
* One-line tasks are available as shortcuts loaded by `loadShortucts` and used like `$this->_exec('ls')`
* Robo runner is less coupled. Output can be set by `\Robo\Config::setOutput`, `RoboFile` can be changed to any provided class.
* Tasks can be used outside of Robo runner (inside a project)
* Timer for long-running tasks added
* Tasks can be globally configured (WIP) via `Robo\Config` class.
* Updated to Symfony >= 2.5
* IO methods added `askHidden`, `askDefault`, `confirm`
* TaskIO methods added `printTaskError`, `printTaskSuccess` with different formatting.
* [Docker] Tasks added
* [Gulp] Task added by @schorsch3000

#### 0.4.7 12/26/2014

* [Minify] Task added by @Rarst. Requires additional dependencies installed *2014-12-26*
* [Help command is populated from annotation](https://github.com/consolidation-org/Robo/pull/71) by @jonsa *2014-12-26*
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

#### 0.4.6 10/17/2014

* [Exec] Output from buffer is not spoiled by special chars *2014-10-17*
* [PHPUnit] detect PHPUnit on Windows or when is globally installed with Composer *2014-10-17*
* Output: added methods askDefault and confirm by @bkawakami *2014-10-17*
* [Svn] Task added by @anvi *2014-08-13*
* [Stack] added dir and printed options *2014-08-12*
* [ExecTask] now uses Executable trait with printed, dir, arg, option methods added *2014-08-12*


#### 0.4.5 08/05/2014

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
