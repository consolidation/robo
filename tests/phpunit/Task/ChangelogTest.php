<?php

namespace phpunit\Task;

use PHPUnit\Framework\TestCase;
use Robo\Task\Development\Changelog;

class ChangelogTest extends TestCase
{
    public function testChangeRowsHaveNoDate()
    {
        $changelog = new Changelog('changelog.md');
        $this->assertSame('* added stuff', $changelog->processLogRow('added stuff'));
    }

    public function testVersionHeaderContainsDate()
    {
        $changelog = new class ('changelog.md') extends Changelog {
            public function header()
            {
                return $this->generateHeader();
            }
        };
        $changelog->version('0.0.1');
        $this->assertSame(
            '#### 0.0.1 (' . date('Y-m-d') . ")\n\n",
            $changelog->header()
        );
    }
}
