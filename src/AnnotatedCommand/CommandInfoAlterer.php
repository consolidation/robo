<?php

namespace Robo\AnnotatedCommand;

use Consolidation\AnnotatedCommand\Parser\CommandInfo;
use Consolidation\AnnotatedCommand\CommandInfoAltererInterface;

class CommandInfoAlterer implements CommandInfoAltererInterface
{
    public function alterCommandInfo(CommandInfo $commandInfo, $commandFileInstance)
    {
        // Public methods from the class Robo\Commo\IO that should not be added
        // as available commands.
        $ignoredMethods = [
            'currentState',
            'restoreState',
        ];
        if (in_array($commandInfo->getMethodName(), $ignoredMethods, true)) {
            $commandInfo->addAnnotation('ignored-command', '');
        }
    }
}
