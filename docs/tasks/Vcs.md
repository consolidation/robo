# Vcs Tasks

## GitHubRelease


Publishes new GitHub release.

``` php
<?php
$this->taskGitHubRelease('0.1.0')
  ->uri('Codegyre/Robo')
  ->askDescription()
  ->run();
?>
```

@method \Robo\Task\Vcs\GitHubRelease tag(string $tag)
@method \Robo\Task\Vcs\GitHubRelease name(string $name)
@method \Robo\Task\Vcs\GitHubRelease body(string $body)
@method \Robo\Task\Vcs\GitHubRelease draft(boolean $isDraft)
@method \Robo\Task\Vcs\GitHubRelease prerelease(boolean $isPrerelease)
@method \Robo\Task\Vcs\GitHubRelease comittish(string $branch)











* `askName()` 
* `askDescription()` 
* `askForChanges()` 
* `changes(array $changes)` 

* `uri($uri)` 

* `askAuth()` 




















## GitStack


Runs Git commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.

``` php
<?php
$this->taskGitStack()
 ->stopOnFail()
 ->add('-A')
 ->commit('adding everything')
 ->push('origin','master')
 ->run()

$this->taskGitStack()
 ->stopOnFail()
 ->add('doc/*')
 ->commit('doc updated')
 ->push()
 ->run();
?>
```








* `cloneRepo($repo, $to = null)`  Executes `git clone`
* `add($pattern)`  Executes `git add` command with files to add pattern
* `commit($message, $options = null)`  Executes `git commit` command with a message
* `pull($origin = null, $branch = null)`  Executes `git pull` command.
* `push($origin = null, $branch = null)`  Executes `git push` command
* `checkout($branch)`  Executes `git checkout` command

* `getPrinted()` 

* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command
























## SvnStack


Runs Svn commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.

``` php
<?php
$this->taskSvnStack()
 ->checkout('http://svn.collab.net/repos/svn/trunk')
 ->run()

// alternatively
$this->_svnCheckout('http://svn.collab.net/repos/svn/trunk');

$this->taskSvnStack('username', 'password')
 ->stopOnFail()
 ->update()
 ->add('doc/*')
 ->commit('doc updated')
 ->run();
?>
```








* `update($path = null)`  Updates `svn update` command
* `add($pattern = null)`  Executes `svn add` command with files to add pattern
* `commit($message, $options = null)`  Executes `svn commit` command with a message
* `checkout($branch)`  Executes `svn checkout` command
* `getPrinted()` 

* `exec($command)` 
* `printed($arg)`  Should command output be printed
* `dir($dir)`  changes working directory of command


























