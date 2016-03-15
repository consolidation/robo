<?php

class ExecCest
{
    public function _before(CliGuy $I)
    {
        $I->getContainer()->addServiceProvider(\Robo\Task\Base\ServiceProvider::class);
    }

    // tests
    public function toExecLsCommand(CliGuy $I)
    {
        $command = strncasecmp(PHP_OS, 'WIN', 3) == 0 ? 'dir' : 'ls';
        $res = $I->task('Exec', $command)->run();
        verify($res->getMessage())->contains('src');
        verify($res->getMessage())->contains('codeception.yml');
    }
}
