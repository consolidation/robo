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
 ->tag('0.6.0')
 ->push('origin','0.6.0')
 ->run()

$this->taskGitStack()
 ->stopOnFail()
 ->add('doc/*')
 ->commit('doc updated')
 ->push()
 ->run();
?>
```

* `cloneRepo($repo, $to = null, $branch = null)`  Executes `git clone`
* `cloneShallow($repo, $to = null, $branch = null, $depth = null)`  Executes `git clone` with depth 1 as default
* `add($pattern)`  Executes `git add` command with files to add pattern
* `commit($message, $options = null)`  Executes `git commit` command with a message
* `pull($origin = null, $branch = null)`  Executes `git pull` command.
* `push($origin = null, $branch = null)`  Executes `git push` command
* `merge($branch)`  Performs git merge
* `checkout($branch)`  Executes `git checkout` command
* `tag($tag_name, $message = null)`  Executes `git tag` command
* `executable($executable)`   * `param string` $executable
* `exec($command)`   * `param string|string[]` $command
* `stopOnFail($stopOnFail = null)`   * `param bool` $stopOnFail
* `result($result)` 
* `dir($dir)`  Changes working directory of command

## HgStack


Runs hg commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.

``` php
<?php
$this->hgStack
 ->cloneRepo('https://bitbucket.org/durin42/hgsubversion')
 ->pull()
 ->add()
 ->commit('changed')
 ->push()
 ->tag('0.6.0')
 ->push('0.6.0')
 ->run();
?>
```

* `cloneRepo($repo, $to = null)`  Executes `hg clone`
* `add($include = null, $exclude = null)`  Executes `hg add` command with files to add by pattern
* `commit($message, $options = null)`  Executes `hg commit` command with a message
* `pull($branch = null)`  Executes `hg pull` command.
* `push($branch = null)`  Executes `hg push` command
* `merge($revision = null)`  Performs hg merge
* `tag($tag_name, $message = null)`  Executes `hg tag` command
* `executable($executable)`   * `param string` $executable
* `exec($command)`   * `param string|string[]` $command
* `stopOnFail($stopOnFail = null)`   * `param bool` $stopOnFail
* `result($result)` 
* `dir($dir)`  Changes working directory of command

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
* `executable($executable)`   * `param string` $executable
* `exec($command)`   * `param string|string[]` $command
* `stopOnFail($stopOnFail = null)`   * `param bool` $stopOnFail
* `result($result)` 
* `dir($dir)`  Changes working directory of command

