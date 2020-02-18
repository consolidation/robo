<?php

use Robo\Robo;
use PHPUnit\Framework\TestCase;

class SshTest extends TestCase
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
<<<<<<< HEAD:tests/unit/Task/SshTest.php
        \Robo\Task\Remote\Ssh::configure('remoteDir', '/some-dir');
=======
        $config = new \Robo\Config\Config();
        \Robo\Task\Remote\Ssh::configure('remoteDir', '/some-dir', $config);
>>>>>>> master:tests/phpunit/Task/SshTest.php
        $this->assertEquals(
            "ssh user@remote.example.com 'cd \"/some-dir\" && echo test'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig($config)
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
                ->setConfig($config)
                ->exec('echo test')
                ->getCommand()
        );
<<<<<<< HEAD:tests/unit/Task/SshTest.php
        \Robo\Task\Remote\Ssh::configure('remoteDir', null);
=======
        \Robo\Task\Remote\Ssh::configure('remoteDir', null, $config);
>>>>>>> master:tests/phpunit/Task/SshTest.php
        $this->assertEquals(
            "ssh user@remote.example.com 'echo test'",
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('echo test')
                ->getCommand()
        );
    }
}
