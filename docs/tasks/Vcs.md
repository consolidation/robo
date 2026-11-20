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

* `cloneRepo($repo, $to = '', $branch = '')` Executes `git clone`
* `cloneShallow($repo, $to = '', $branch = '', $depth = 1)` Executes `git clone` with depth 1 as default
* `add($pattern)` Executes `git add` command with files to add pattern
* `commit($message, $options = '')` Executes `git commit` command with a message
* `pull($origin = '', $branch = '')` Executes `git pull` command.
* `push($origin = '', $branch = '')` Executes `git push` command
* `merge($branch)` Performs git merge
* `checkout($branch)` Executes `git checkout` command
* `tag($tag_name, $message = '')` Executes `git tag` command
* `executable($executable)`
* `exec($command)`
* `stopOnFail($stopOnFail = true)`
* `result($result)`
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.

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

* `cloneRepo($repo, $to = '')` Executes `hg clone`
* `add($include = '', $exclude = '')` Executes `hg add` command with files to add by pattern
* `commit($message, $options = '')` Executes `hg commit` command with a message
* `pull($branch = '')` Executes `hg pull` command.
* `push($branch = '')` Executes `hg push` command
* `merge($revision = '')` Performs hg merge
* `tag($tag_name, $message = '')` Executes `hg tag` command
* `executable($executable)`
* `exec($command)`
* `stopOnFail($stopOnFail = true)`
* `result($result)`
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


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

* `update($path = '')` Updates `svn update` command
* `add($pattern = '')` Executes `svn add` command with files to add pattern
* `commit($message, $options = '')` Executes `svn commit` command with a message
* `checkout($branch)` Executes `svn checkout` command
* `executable($executable)`
* `exec($command)`
* `stopOnFail($stopOnFail = true)`
* `result($result)`
* `setProcessInput($input)` Pass an input to the process. Can be resource created with fopen() or string
* `dir($dir)` Changes working directory of command
* `setOutput($output)` Sets the Console Output.


