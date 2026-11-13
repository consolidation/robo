<?php

namespace Robo;

use Symfony\Component\Filesystem\Filesystem;

class Fixtures
{
    protected $testDir;
    protected $tmpDirs = [];
    protected $clonedRepos = [];

    /**
     * Fixtures constructor
     */
    public function __construct()
    {
        $testDir = false;
    }

    /**
     * Clean up any temporary directories that may have been created
     */
    public function cleanup()
    {
        $fs = new Filesystem();
        foreach ($this->tmpDirs as $tmpDir) {
            try {
                $fs->remove($tmpDir);
            } catch (\Exception $e) {
                // Ignore problems with removing fixtures.
            }
        }
        $this->tmpDirs = [];
    }

    /**
     * Create a new temporary directory.
     *
     * @param string|bool $basedir Where to store the temporary directory
     * @return type
     */
    public function mktmpdir($basedir = false)
    {
        $tempfile = tempnam($basedir ?: $this->testDir ?: sys_get_temp_dir(), 'robo-tests');
        unlink($tempfile);
        mkdir($tempfile);
        $this->tmpDirs[] = $tempfile;
        return $tempfile;
    }

    public function createAndCdToSandbox()
    {
        $sourceSandbox = $this->sandboxDir();
        $targetSandbox = $this->mktmpdir();
        $fs = new Filesystem();
        $fs->mirror($sourceSandbox, $targetSandbox);
        chdir($targetSandbox);

        return $targetSandbox;
    }

    public function dataFile($filename)
    {
        return $this->fixturesDir() . '/' . $filename;
    }

    protected function fixturesDir()
    {
        return dirname(__DIR__) . '/_data';
    }

    protected function sandboxDir()
    {
        return $this->fixturesDir() . '/claypit';
    }

    protected function testDir()
    {
        if (!$this->testDir) {
            $this->testDir = $this->mktmpdir();
        }
        return $this->testDir;
    }
}
