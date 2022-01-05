<?php

declare(strict_types = 1);

namespace unit\Common;

use PHPUnit\Framework\TestCase;
use Robo\Common\TimeKeeper;

class TimeKeeperTest extends TestCase
{

    public function casesFormatDuration(): array
    {
        return [
            'sec -1' => ['-1s', -1],
            'sec 0' => ['0s', 0],
            'sec 1' => ['1s', 1],
            'sec 59' => ['59s', 59],
            'sec 60' => ['01:00', 60],
            'sec 61' => ['01:01', 61],
            'sec 3599' => ['59:59', 3599],
            'sec 3600' => ['01:00:00', 3600],
            'sec 3601' => ['01:00:01', 3601],
            'sec 86399' => ['23:59:59', 86399],
            'sec 86400' => ['1 day 00:00:00', 86400],
            'sec 86401' => ['1 day 00:00:01', 86401],
            'sec 172799' => ['1 day 23:59:59', 172799],
            'sec 172800' => ['2 days 00:00:00', 172800],
            'sec 172801' => ['2 days 00:00:01', 172801],
            'sec 0.0004' => ['0s', 0.0004],
            'sec 0.004' => ['0.004s', 0.004],
            'sec 0.04' => ['0.04s', 0.04],
            'sec 0.4' => ['0.4s', 0.4],
            'sec 4.0002' => ['4s', 4.0002],
            'sec 4.002' => ['4.002s', 4.002],
            'sec 4.02' => ['4.02s', 4.02],
            'sec 4.2' => ['4.2s', 4.2],
            'sec 42.0003' => ['42s', 42.0003],
            'sec 42.003' => ['42.003s', 42.003],
            'sec 42.03' => ['42.03s', 42.03],
            'sec 42.3' => ['42.3s', 42.3],
            'sec 62.0003' => ['01:02', 62.0003],
            'sec 62.003' => ['01:02', 62.003],
            'sec 62.03' => ['01:02', 62.03],
            'sec 62.3' => ['01:02', 62.3],
        ];
    }

    /**
     * @dataProvider casesFormatDuration
     */
    public function testFormatDuration(string $expected, float $duration)
    {
        $this->assertSame($expected, TimeKeeper::formatDuration($duration));
    }
}
