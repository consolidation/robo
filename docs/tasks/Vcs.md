# Vcs Tasks
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
* `merge($branch)`  Performs git merge
* `checkout($branch)`  Executes `git checkout` command
* `exec($command)` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed

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
* `exec($command)` 
* `dir($dir)`  changes working directory of command
* `printed($arg)`  Should command output be printed


