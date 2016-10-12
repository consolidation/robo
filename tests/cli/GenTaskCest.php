<?php

class GenTaskCest
{
    // tests
    public function toTestTaskGeneration(CliGuy $I)
    {
        $result = $I->taskGenTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')->run();
        verify($result->getMessage())->contains('protected function _chgrp($files, $group, $recursive = false)');
    }
}
