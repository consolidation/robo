<?php

namespace Robo\Task\Development;

use ChangeLog\ChangeLog as ChangeLogContainer;
use ChangeLog\Release;
use ChangeLog\IO\File;
use ChangeLog\Log;
use ChangeLog\Parser\KeepAChangeLog as KeepAChangeLogParser;
use ChangeLog\Renderer\KeepAChangeLog as KeepAChangeLogRenderer;
use Robo\Config;
use Robo\Task\BaseTask;
use Robo\Result;
use Robo\Task\Development;

/**
 * Helps to manage changelog file.
 * Creates or updates `changelog.md` file.
 */
class Changelog extends BaseTask
{
    /**
     * Location of the changelog file
     * @var string
     */
    protected $fileName;

    /**
     * @var ChangeLogContainer
     */
    protected $changeLog;

    /**
     * @var Log
     */
    protected $log;

    /**
     * @param string $filename
     * @return \Robo\Task\Development\Changelog
     * @deprecated Use $this->taskChangelog($filename) instead.
     */
    public static function init($filename = 'CHANGELOG.md')
    {
        return Config::getContainer()->get('taskChangelog', [$filename]);
    }

    public function __construct($filename)
    {
        // Make sure we always have a log to work with.
        $this->log = new Log();
        $this->setFileName($filename);
    }

    /**
     * Sets the change log file location.
     *
     * @param string $fileName
     *
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        $this->load();

        return $this;
    }

    /**
     * Adds a new release.
     *
     * @param string $name
     *
     * @return $this
     */
    public function addRelease($name)
    {
        $this->log->addRelease(new Release($name));

        return $this;
    }

    /**
     * Converts the "unreleased" release to a numbered version
     *
     * @param string $name Can be the semver of the new release or one of Log::VERSION_MAJOR, Log::VERSION_MINOR or
     *                     Log::VERSION PATCH for automatic numbering.
     * @param string $newLink
     * @param string $newLinkName
     *
     * @return $this
     */
    public function releaseVersion($name, $newLink = null, $newLinkName = null)
    {
        $newReleaseName = $this->log->getNextVersion($name);

        $release = $this->log->getRelease('unreleased');
        $release->setName($newReleaseName);

        if ($newLink === null) {
            $release->setLink(null);
            $release->setLinkName(null);
        } else {
            $newLinkName = $newLinkName === null ? $newReleaseName : $newLinkName;

            $release->setLink($newLink);
            $release->setLinkName($newLinkName);
        }

        return $this;
    }

    /**
     * Adds a change.
     *
     * @param string $type
     * @param string $change
     * @param string $release
     *
     * @return $this
     */
    public function addChange($type, $change, $release = null)
    {
        $this->getWorkingRelease($release)
            ->addChange($type, $change);

        return $this;
    }

    /**
     * Gets all changes.
     *
     * @param string|null $release
     *
     * @return array
     */
    public function getChanges($release = null)
    {
        return $this->getWorkingRelease($release)
            ->getAllChanges();
    }

    /**
     * Gets changes of a given type.
     *
     * @param string      $type
     * @param string|null $release
     *
     * @return null|string[]
     */
    public function getChangesByType($type, $release = null)
    {
        return $this->getWorkingRelease($release)
            ->getChanges($type);
    }

    /**
     * Loads or re-loads the changelog.
     */
    public function load()
    {
        $this->changeLog = new ChangeLogContainer();

        $fileIO = new File(['file' => $this->fileName]);
        $this->changeLog->setInput($fileIO);
        $this->changeLog->setParser(new KeepAChangeLogParser());
        $this->changeLog->setRenderer(new KeepAChangeLogRenderer());
        $this->changeLog->setOutput($fileIO);

        if (file_exists($this->fileName)) {
            $this->log = $this->changeLog->parse();
        }
    }

    /**
     * @param string $release
     *
     * @return \ChangeLog\Release
     */
    protected function getWorkingRelease($release)
    {
        if ($release === null) {
            return $this->log->getLatestRelease();
        }

        return $this->log->getRelease($release);
    }

    /**
     * Gets the active change log.
     *
     * @return \ChangeLog\Log
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Writes the changes to the log file.
     *
     * @return \Robo\Result
     */
    public function run()
    {
        $this->changeLog->write($this->log);

        return new Result($this, 0);
    }
}
