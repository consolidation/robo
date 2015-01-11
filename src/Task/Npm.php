<?php
namespace Robo\Task;

use Robo\Contract\CommandInterface;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

/**
 * Contains tasks for npm.
 *
 * @package  Robo\Task
 */
trait Npm {

    protected function taskNpmInstall($pathToNpm = null) {
        return new Npm\Install($pathToNpm);
    }

    protected function taskNpmUpdate($pathToNpm = null) {
        return new Npm\Update($pathToNpm);
    }
}
