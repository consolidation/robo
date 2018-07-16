<?php

namespace Tests\Unit\Task;

use AspectMock\Test as test;

class WatchTest extends \Codeception\TestCase\Test
{
    /**
     * @var \AspectMock\Proxy\AnythingClassProxy
     */
    protected $resourceWatcher;

    public function _before()
    {
        if (!class_exists('Lurker\\ResourceWatcher')) {
            $this->resourceWatcher = test::spec(
                'Lurker\ResourceWatcher',
                [
                    'start' => true,
                    'track' => true,
                    'addListener' => true
                ]
            )->make();
        } else {
            $this->resourceWatcher = test::double(
                'Lurker\ResourceWatcher',
                [
                    'start' => true,
                    'track' => true,
                    'addListener' => true
                ]
            );
        }
    }

    public function testMonitorWithOneEvent()
    {
        $task = new \Robo\Task\Base\Watch($this);

        $task->monitor(
            'src',
            function () {
                //do nothing
            },
            1 // CREATE
        )->run();

        $this->resourceWatcher->verifyInvokedOnce('track');
    }

    public function testMonitorWithTwoEvents()
    {
        $task = new \Robo\Task\Base\Watch($this);

        $task->monitor(
            'src',
            function () {
                //do nothing
            },
            [
                1, //CREATE
                4, //DELETE
            ]
        )->run();

        $this->resourceWatcher->verifyInvokedMultipleTimes('track', 2);
    }
}
