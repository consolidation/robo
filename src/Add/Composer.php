<?php
namespace Robo\Add;

use Robo\Task\ComposerInstall;
use Robo\Task\ComposerUpdate;

trait Composer {

    protected function taskComposerInstall($pathToComposer = null)
    {
        return new ComposerInstall($pathToComposer);
    }

    protected function taskComposerUpdate($pathToComposer = null)
    {
        return new ComposerUpdate($pathToComposer);
    }
} 