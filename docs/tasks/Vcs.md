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

* `cloneRepo($repo, $to = null, $branch = null)`

 * `param string` $repo
* `cloneShallow($repo, $to = null, $branch = null, $depth = null)`

 * `param string` $repo
* `add($pattern)`

 * `param string` $pattern
* `commit($message, $options = null)`

 * `param string` $message
* `pull($origin = null, $branch = null)`

 * `param string` $origin
* `push($origin = null, $branch = null)`

 * `param string` $origin
* `merge($branch)`

 * `param string` $branch
* `checkout($branch)`

 * `param string` $branch
* `tag($tag_name, $message = null)`

 * `param string` $tag_name
* `executable($executable)`

 * `param string` $executable
* `exec($command)`

 * `param string|string[]|CommandInterface` $command
* `stopOnFail($stopOnFail = null)`

 * `param bool` $stopOnFail
* `result($result)`

 * `param ` $result
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir

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

* `cloneRepo($repo, $to = null)`

 * `param string` $repo
* `add($include = null, $exclude = null)`

 * `param string` $include
* `commit($message, $options = null)`

 * `param string` $message
* `pull($branch = null)`

 * `param string` $branch
* `push($branch = null)`

 * `param string` $branch
* `merge($revision = null)`

 * `param string` $revision
* `tag($tag_name, $message = null)`

 * `param string` $tag_name
* `executable($executable)`

 * `param string` $executable
* `exec($command)`

 * `param string|string[]|CommandInterface` $command
* `stopOnFail($stopOnFail = null)`

 * `param bool` $stopOnFail
* `result($result)`

 * `param ` $result
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir


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

* `update($path = null)`

 * `param string` $path
* `add($pattern = null)`

 * `param string` $pattern
* `commit($message, $options = null)`

 * `param string` $message
* `checkout($branch)`

 * `param string` $branch
* `executable($executable)`

 * `param string` $executable
* `exec($command)`

 * `param string|string[]|CommandInterface` $command
* `stopOnFail($stopOnFail = null)`

 * `param bool` $stopOnFail
* `result($result)`

 * `param ` $result
* `setOutput($output)`

 * `param \Symfony\Component\Console\Output\OutputInterface` $output
* `setProcessInput($input)`

 * `param resource|string` $input
* `dir($dir)`

 * `param string` $dir


