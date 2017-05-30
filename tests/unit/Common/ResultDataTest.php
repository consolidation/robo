<?php

use Robo\ResultData;

/**
 * Class ResultDataTest.
 *
 * @coversDefaultClass \Robo\ResultData
 */
class ResultDataTest extends \Codeception\Test\Unit
{
    /**
     * @var \CodeGuy
     */
    protected $guy;

    public function testResultDataUpdate()
    {
        $a = new ResultData(ResultData::EXITCODE_OK, '', ['one' => 'first', 'two' => 'second']);
        $b = new ResultData(ResultData::EXITCODE_OK, '', ['one' => 'First', 'three' => 'Third']);

        $expected = ['one' => 'first', 'two' => 'second'];
        $this->guy->assertEquals($expected, $a->getData());

        $a->update($b);

        $expected = ['one' => 'First', 'two' => 'second', 'three' => 'Third'];
        $this->guy->assertEquals($expected, $a->getData());
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
        $this->guy->assertEquals($expected, $a->getData());
    }
}
