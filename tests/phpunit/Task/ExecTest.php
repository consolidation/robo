<?php

namespace phpunit\Task;

use PHPUnit\Framework\TestCase;

class ExecTest extends TestCase
{

    // tests
    public function testBasicCommand()
    {
        $this->assertSame(
            'ls',
            (new \Robo\Task\Base\Exec('ls'))
                ->getCommand()
        );
    }
}
