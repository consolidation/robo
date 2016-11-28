<?php

use Robo\Robo;

class SshTest extends \Codeception\TestCase\Test
{
    // tests
    public function testBasicCommand()
    {
        verify(
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('ls -la')
                ->exec('chmod g+x logs')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'ls -la && chmod g+x logs'");
    }

    public function testStopOnFail()
    {
        verify(
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->stopOnFail(false)
                ->exec('one')
                ->exec('two')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'one ; two'");
    }

    /**
     * Sets static configuration, then runs task without working dir, with working dir and again without.
     */
    public function testWorkingDirectoryStaticConfiguration()
    {
        \Robo\Task\Remote\Ssh::configure('remoteDir', '/some-dir');
        verify(
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig(Robo::config())
                ->exec('echo test')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'cd \"/some-dir\" && echo test'");
        verify(
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->remoteDir('/other-dir')
                ->exec('echo test')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'cd \"/other-dir\" && echo test'");
        verify(
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig(Robo::config())
                ->exec('echo test')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'cd \"/some-dir\" && echo test'");
        \Robo\Task\Remote\Ssh::configure('remoteDir', null);
        verify(
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('echo test')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'echo test'");
    }
}
