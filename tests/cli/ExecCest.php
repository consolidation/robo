<?php
use \CliGuy;

class ExecCest
{
    // tests
    public function toExecLsCommand(CliGuy $I)
    {
        $res = $I->taskExec('ls')->run();
        verify($res->getMessage())->contains('src');
        verify($res->getMessage())->contains('codeception.yml');
    }
}