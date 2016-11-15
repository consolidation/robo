<?php

use AspectMock\Test as test;
use Robo\Robo;

class RsyncTest extends \Codeception\TestCase\Test
{
    /**
     * @var \CodeGuy
     */
    protected $guy;

    // tests
    public function testRsync()
    {
        verify(
            (new \Robo\Task\Remote\Rsync())
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
        )->equals(
                'rsync --recursive --exclude .git --exclude .svn --exclude .hg --checksum --whole-file --verbose --progress --human-readable --stats src/ \'dev@localhost:/var/www/html/app/\''
        );

        // From the folder 'foo bar' (with space) in 'src' directory
        verify(
            (new \Robo\Task\Remote\Rsync())
                ->fromPath('src/foo bar/baz')
                ->toHost('localhost')
                ->toUser('dev')
                ->toPath('/var/path/with/a space')
                ->getCommand()
        )->equals(
                'rsync \'src/foo bar/baz\' \'dev@localhost:/var/path/with/a space\''
        );

        // Copy two folders, 'src/foo' and 'src/bar'
        verify(
            (new \Robo\Task\Remote\Rsync())
                ->fromPath(['src/foo', 'src/bar'])
                ->toHost('localhost')
                ->toUser('dev')
                ->toPath('/var/path/with/a space')
                ->getCommand()
        )->equals(
                'rsync src/foo src/bar \'dev@localhost:/var/path/with/a space\''
        );
    }
}
