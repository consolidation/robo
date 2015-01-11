<?php


class SshTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Remote\loadTasks;
    // tests
    public function testSshCommand()
    {
        verify(
            $this->taskSshExec('remote.example.com', 'user')
                ->exec('cd /var/www/html')
                ->exec('ls -la')
                ->exec('chmod g+x logs')
                ->getCommand()
        )->equals("ssh user@remote.example.com 'cd /var/www/html && ls -la && chmod g+x logs'");
    }

}