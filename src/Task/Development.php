<?php
namespace Robo\Task;
trait_exists('Robo\Task\FileSystem', true);

use Robo\Output;
use Robo\Result;
use Robo\Task\FileSystem\ReplaceInFile;
use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;

/**
 * Contains simple tasks to simplify documenting of development process.
 * @package Robo\Task
 */
trait Development
{
    protected function taskChangelog($filename = 'CHANGELOG.md')
    {
        return new Development\Changelog($filename);
    }

    protected function taskGenDoc($filename)
    {
        return new Development\GenerateMarkdownDoc($filename);
    }

     protected function taskSemVer($pathToSemVer = '.semver')
     {
         return new Development\SemVer($pathToSemVer);
     }

}
