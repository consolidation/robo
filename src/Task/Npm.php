<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\TaskException;

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
