<?php

class GenTaskCest
{
    public function _before(CliGuy $I)
    {
        $I->getContainer()->addServiceProvider(\Robo\Task\Development\loadTasks::getDevelopmentServices());
    }

    // tests
    public function toExecLsCommand(CliGuy $I)
    {
        $result = $I->taskGenTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')->run();
        verify($result->getMessage())->contains('protected function _chgrp($files, $group, $recursive = false)');
    }
}
