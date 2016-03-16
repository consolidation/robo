<?php

use AspectMock\Test as test;
use Robo\Config;

class RsyncTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $guy;

    protected $container;

    protected function _before()
    {
        $this->container = Config::getContainer();
        $this->container->addServiceProvider(\Robo\Task\Remote\ServiceProvider::class);
    }

    // tests
    public function testRsync()
    {
        verify(
            $this->container->get('taskRsync')
                ->fromPath('src/')
                ->toHost('localhost')
                ->toUser('dev')
                ->toPath('/var/www/html/app/')
                ->recursive()
                ->excludeVcs()
                ->checksum()
                ->wholeFile()
                ->verbose()
                ->progress()
                ->humanReadable()
                ->stats()
                ->getCommand()
        )->equals(sprintf('rsync --recursive --exclude %s --exclude %s --exclude %s --checksum --whole-file --verbose --progress --human-readable --stats src/ dev@localhost:/var/www/html/app/',
            escapeshellarg('.git/'),
            escapeshellarg('.svn/'),
            escapeshellarg('.hg/')
        ));
    }

}
