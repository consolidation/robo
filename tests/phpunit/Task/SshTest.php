<?php

use Robo\Common\ProcessUtils;
use Robo\Robo;
use PHPUnit\Framework\TestCase;

class SshTest extends TestCase
{
    /**
     * The remote command is passed to ssh as one escaped argument. The
     * quoting depends on the local OS (single quotes on POSIX, double
     * quotes on Windows), so build the expected command the same way.
     */
    protected function expectedSshCommand($hostSpec, $remoteCommand)
    {
        return "ssh {$hostSpec} " . ProcessUtils::escapeArgument($remoteCommand);
    }

    // tests
    public function testBasicCommand()
    {
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', 'ls -la && chmod g+x logs'),
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('ls -la')
                ->exec('chmod g+x logs')
                ->getCommand()
        );
    }

    public function testCommandContainingQuotes()
    {
        $command = (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
            ->exec("echo 'hi'")
            ->getCommand();
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', "echo 'hi'"),
            $command
        );
        if ('\\' !== DIRECTORY_SEPARATOR) {
            $this->assertEquals("ssh user@remote.example.com 'echo '\\''hi'\\'''", $command);
        }
    }

    public function testStopOnFail()
    {
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', 'one ; two'),
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
        $config = new \Robo\Config\Config();
        \Robo\Task\Remote\Ssh::configure('remoteDir', '/some-dir', $config);
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', 'cd "/some-dir" && echo test'),
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig($config)
                ->exec('echo test')
                ->getCommand()
        );
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', 'cd "/other-dir" && echo test'),
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->remoteDir('/other-dir')
                ->exec('echo test')
                ->getCommand()
        );
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', 'cd "/some-dir" && echo test'),
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->setConfig($config)
                ->exec('echo test')
                ->getCommand()
        );
        \Robo\Task\Remote\Ssh::configure('remoteDir', null, $config);
        $this->assertEquals(
            $this->expectedSshCommand('user@remote.example.com', 'echo test'),
            (new \Robo\Task\Remote\Ssh('remote.example.com', 'user'))
                ->exec('echo test')
                ->getCommand()
        );
    }
}
