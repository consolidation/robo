## Trait Robo\Task\Composer


### Task BaseComposerTask


### Task ComposerInstallTask


Composer Install

``` php
<?php
// simple execution
$this->taskComposerInstall()->run();

// prefer dist with custom path
$this->taskComposerInstall('path/to/my/composer.phar')
     ->preferDist()
     ->run();
?>
```


Class ComposerInstallTask
@package Robo\Task

### Task ComposerUpdateTask


## Trait Robo\Task\Development


### Task ChangelogTask


@method ChangelogTask filename(string $filename)
@method ChangelogTask anchor(string $anchor)
@method ChangelogTask version(string $version)

@package Robo\Task

## Trait Robo\Task\Exec


### Task ExecTask


Executes shell script. Closes it when running in background mode.
Initial code from https://github.com/tiger-seo/PhpBuiltinServer by tiger-seo

Class Exec
@package Robo\Task

## Trait Robo\Task\FileSystem


### Task RequireTask


### Task BaseDirTask


### Task CleanDirTask


### Task CopyDirTask


### Task DeleteDirTask


### Task ReplaceInFileTask


@method ReplaceInFileTask filename(string)
@method ReplaceInFileTask from(string)
@method ReplaceInFileTask to(string)

Class ReplaceInFileTask
@package Robo\Task

### Task WriteToFileTask


## Trait Robo\Task\GitHub


### Task GitHubTask


@method GitHubTask repo(string)


### Task GitHubReleaseTask


@method GitHubReleaseTask tag(string $tag)
@method GitHubReleaseTask name(string $name)
@method GitHubReleaseTask body(string $body)
@method GitHubReleaseTask draft(boolean $isDraft)
@method GitHubReleaseTask prerelease(boolean $isPrerelease)
@method GitHubReleaseTask comittish(string $branch)

Class GitHubReleaseTask
@package Robo\Task

## Trait Robo\Task\PhpServer


### Task PhpServerTask


## Trait Robo\Task\SymfonyCommand


### Task SymfonyCommandTask


## Trait Robo\Task\Watch


### Task WatchTask

