<?php


class RsyncTest extends \Codeception\TestCase\Test
{
    use \Robo\Task\Rsync;
   /**
    * @var \CodeGuy
    */
    protected $guy;

    // tests
    public function testRsync()
    {
        verify(
            $this->taskRsync()
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
        )->equals('rsync --recursive --exclude .git/ --exclude .svn/ --exclude .hg/ --checksum --whole-file --verbose --progress --human-readable --stats src/ dev@localhost:/var/www/html/app/');
    }

}