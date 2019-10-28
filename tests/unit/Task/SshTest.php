<?php

use Robo\Robo;

class SshTest extends \Codeception\TestCase\Test
{
    // tests
    public function testBasicCommand()
    {
        $this->assertEquals(
            "ssh user@remote.example.com 'ls -la && chmod g+x logs'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('ls -la')
                ->exec('chmod g+x logs')
                ->getCommand()
        );
    }

    public function testStopOnFail()
    {
        $this->assertEquals(
            "ssh user@remote.example.com 'one ; two'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->stopOnFail(false)
                ->exec('one')
                ->exec('two')
                ->getCommand()
        );
    }

    /**
     * Sets static configuration, then runs task without working dir, with working dir and again without.
     */
    public function testWorkingDirectoryStaticConfiguration()
    {
        \Robo\Task\Remote\Ssh::configure('remoteDir', '/some-dir');
        $this->assertEquals(
            "ssh user@remote.example.com 'cd \"/some-dir\" && echo test'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig(Robo::config())
                ->exec('echo test')
                ->getCommand()
        );
        $this->assertEquals(
            "ssh user@remote.example.com 'cd \"/other-dir\" && echo test'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->remoteDir('/other-dir')
                ->exec('echo test')
                ->getCommand()
        );
        $this->assertEquals(
            "ssh user@remote.example.com 'cd \"/some-dir\" && echo test'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig(Robo::config())
                ->exec('echo test')
                ->getCommand()
        );
        \Robo\Task\Remote\Ssh::configure('remoteDir', null);
        $this->assertEquals(
            "ssh user@remote.example.com 'echo test'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('echo test')
                ->getCommand()
        );
    }
}
