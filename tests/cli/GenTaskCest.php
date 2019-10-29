<?php

class GenTaskCest
{
    // tests
    public function toTestTaskGeneration(CliGuy $I)
    {
        $result = $I->taskGenTask('Symfony\Component\Filesystem\Filesystem', 'FilesystemStack')->run();
        $I->assertContains(
          'protected function _chgrp($files, $group, $recursive = false)',
          $result->getMessage());
    }
}
