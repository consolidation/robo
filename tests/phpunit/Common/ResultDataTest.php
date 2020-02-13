<?php

use PHPUnit\Framework\TestCase;
use Robo\ResultData;

/**
 * Class ResultDataTest.
 *
 * @coversDefaultClass \Robo\ResultData
 */
class ResultDataTest extends TestCase
{
    public function testResultDataUpdate()
    {
        $a = new ResultData(ResultData::EXITCODE_OK, '', ['one' => 'first', 'two' => 'second']);
        $b = new ResultData(ResultData::EXITCODE_OK, '', ['one' => 'First', 'three' => 'Third']);

        $expected = ['one' => 'first', 'two' => 'second'];
        $this->assertEquals($expected, $a->getData());

        $a->update($b);

        $expected = ['one' => 'First', 'two' => 'second', 'three' => 'Third'];
        $this->assertEquals($expected, $a->getData());
    }

    public function testResultDataMergeData()
    {
        $a = new ResultData(ResultData::EXITCODE_OK, '', ['one' => 'first', 'two' => 'second']);

        $to_be_merged = [
            ['one' => 'ignored',],
            ['three' => 'new',],
        ];

        foreach ($to_be_merged as $mergeThis) {
            $a->mergeData($mergeThis);
        }

        $expected = ['one' => 'first', 'two' => 'second', 'three' => 'new'];
        $this->assertEquals($expected, $a->getData());
    }
}
