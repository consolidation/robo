<?php
namespace Robo\Task;

use Naneau\SemVer\Parser;
use Naneau\SemVer\Version;
use Naneau\SemVer\Version\Build;
use Naneau\SemVer\Version\PreRelease;
use Robo\Output;
use Robo\Task\Exec;
use Robo\Task\FileSystem;
use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;
use Robo\Result;

/**
 * Tasks to help managing a project's semantic versioning.
 *
 * @package Robo\Task
 */
trait Semver
{
    protected function taskSemverInitialize()
    {
        return new TaskSemverInitialize();
    }

    protected function taskSemverIncrement($what)
    {
        $semver = new TaskSemverIncrement();
        return $semver->{$what}();
    }

    protected function taskSemverPreRelease($greek, int $releaseNumber = null)
    {
        $semver = new TaskSemverPreRelease();
        $semver->setGreek($greek);
        if (!empty($releaseNumber)) {
            $semver->setReleaseNumber($releaseNumber);
        }
        return $semver;
    }

    protected function taskSemverBuild($number = null, array $parts = [])
    {
        $semver = new TaskSemverBuild();
        $semver->setNumber($number);
        if (!empty($parts)) {
            $semver->setParts($parts);
        }
        return $semver;
    }

    protected function taskSemverTag()
    {
        return new TaskSemverTag();
    }
}

abstract class SemverBaseTask implements TaskInterface
{
    use Output;

    const FILEPATH = '.semver';

    public function isInitialized()
    {
        return file_exists(self::FILEPATH);
    }

    public function getVersion()
    {
        $tag = new TaskSemverTag();
        $result = $tag->parse();
        if ($result->wasSuccessful()) {
            return $result->getMessage();
        }
    }

}

abstract class SemverCommonTask extends SemverBaseTask
{
    use Exec;

    const SEMVER = "---\n:major: %d\n:minor: %d\n:patch: %d\n:special: '%s'\n:metadata: '%s'";

    protected $version;

    public function __construct()
    {
        if ($this->isInitialized()) {
            $this->version = Parser::parse($this->getVersion());
        }
    }

    protected function write()
    {
        $content = sprintf(self::SEMVER,
            $this->version->getMajor(),
            $this->version->getMinor(),
            $this->version->getPatch(),
            $this->version->getPreRelease(),
            substr($this->version->getBuild(), strlen('build.'))
        );
        $command = 'echo "' . $content . '" > ' . self::FILEPATH;

        $line = exec($command, $output, $code);
        return new Result($this, $code, $line, $output);
    }
}

/**
 * Semver tag.
 *
 * ``` php
 * <?php
 * $this->taskSemverTag()->run();
 *
 * $this->taskSemverTag()->parse();
 * ?>
 * ```
 */
class TaskSemverTag extends SemverBaseTask
{
    const REGEX = "/^\-\-\-\n:major:\s(0|[1-9]\d*)\n:minor:\s(0|[1-9]\d*)\n:patch:\s(0|[1-9]\d*)\n:special:\s'([a-zA-z0-9]*\.?(?:0|[1-9]\d*)?)'\n:metadata:\s'((?:0|[1-9]\d*)?(?:\.[a-zA-z0-9\.]*)?)'/";

    public function run()
    {
        $this->printTaskInfo('Retrieving semver tag...');

        return $this->parse();
    }

    public function parse()
    {
        if (!$this->isInitialized()) {
            return Result::error($this, 'Semver has not been initialized.');
        }

        $command = 'cat ' . self::FILEPATH;

        $line = exec($command, $output, $code);
        if ($code) {
            return Result::error($this, $line);
        }

        return Result::success($this, $this->parseSemver($output));
    }

    protected function parseSemver($output)
    {
        if (!preg_match_all(self::REGEX, implode("\n", $output), $matches)) {
            throw new TaskException();
        }

        list(, $major, $minor, $patch, $prerelease, $build) = array_map('current', $matches);

        $version = new Version();
        $version->setMajor($major)
            ->setMinor($minor)
            ->setPatch($patch);

        if (!empty($prerelease)) {
            $prerelease = explode('.', $prerelease);
            $prereleaseVersion = new PreRelease();
            $prereleaseVersion->setGreek($prerelease[0]);
            if (!empty($prerelease[1])) {
                $prereleaseVersion->setReleaseNumber($prerelease[1]);
            }
            $version->setPreRelease($prereleaseVersion);
        }

        if (!empty($build)) {
            $build = explode('.', $build);
            $buildVersion = new Build();
            $buildVersion->setNumber(array_shift($build));
            foreach ($build as $part) {
                $buildVersion->addPart($part);
            }
            $version->setBuild($buildVersion);
        }

        return (string) $version;
    }
}


/**
 * Initializes (optionally by force) the project's semantic
 * versioning by creating a `.semver` file.
 *
 * ``` php
 * <?php
 * $this->taskSemverInitialize()->run();
 *
 * $this->taskSemverInitialize()->force()->run();
 * ?>
 * ```
 */
class TaskSemverInitialize extends SemverCommonTask
{
    protected $force;

    public function force()
    {
        $this->force = true;
        return $this;
    }

    public function run()
    {
        if (!$this->force && $this->isInitialized()) {
            return Result::error($this, 'Semver has already been initialized.');
        }

        $this->printTaskInfo('Initializing .semver...');

        $this->version = new Version();

        if (!$this->write()->wasSuccessful()) {
            return Result::error($this, 'Failed to initialize semver.');
        }
        return Result::success($this, 'Semver initialized.');
    }
}

/**
 * Increment semantic version.
 *
 * ``` php
 * <?php
 * $this->taskSemverIncrement()->major()->run();
 *
 * $this->taskSemverIncrement()->minor()->run();
 *
 * $this->taskSemverIncrement()->patch()->run();
 * ?>
 * ```
 */
class TaskSemverIncrement extends SemverCommonTask
{
    protected $name;

    public function __call($name, $args)
    {
        if (!in_array($name, ['major', 'minor', 'patch'])) {
            throw new TaskException('Invalid semantic version part.');
        }

        $this->name = ucfirst($name);
        return $this;
    }

    public function run()
    {
        if (!$this->isInitialized()) {
            return Result::error($this, 'Semver has already been initialized.');
        }

        $getter = 'get' . $this->name;
        $setter = 'set' . $this->name;

        $this->version->{$setter}((int)$this->version->{$getter}() + 1);

        $this->printTaskInfo('Updating version to ' . $this->version);
        return $this->write();
    }
}

/**
 * Update semantic version's pre-release.
 *
 * ``` php
 * <?php
 * $this->taskSemverPreRelease('alpha', 3)->run();
 *
 * // If version is '1.2.3-RC.2',
 * $this->taskSemverPreRelease('RC')->run();
 * // will auto-increment the pre-release number
 * // and the result will be '1.2.3-RC.3'
 * ?>
 * ```
 */
class TaskSemverPreRelease extends SemverCommonTask
{
    protected $greek;

    protected $releaseNumber;

    public function setGreek($greek)
    {
        $this->greek = $greek;
        return $this;
    }

    public function setReleaseNumber($releaseNumber)
    {
        $this->releaseNumber = $releaseNumber;
        return $this;
    }

    public function run()
    {
        if (!$this->isInitialized()) {
            return Result::error($this, 'Semver has already been initialized.');
        }

        if (null === $this->releaseNumber && $this->version->getPreRelease()->getGreek() == $this->greek) {
            $this->releaseNumber = $this->version->getPreRelease()->getReleaseNumber() + 1;
        }

        $prerelease = new PreRelease();
        $prerelease->setGreek($this->greek);
        if (null !== $this->releaseNumber) {
            $prerelease->setReleaseNumber($this->releaseNumber);
        }
        $this->version->setPreRelease($prerelease);

        $this->printTaskInfo('Updating version to ' . $this->version);
        return $this->write();
    }
}

/**
 * Update semantic version's build.
 *
 * ``` php
 * <?php
 * $this->taskSemverBuild(substr(exec('git rev-parse --verify HEAD'), 0, 5), ['php55', 'win'])->run();
 * ?>
 * ```
 */
class TaskSemverBuild extends SemverCommonTask
{
    protected $number;

    protected $parts;

    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    public function setParts(array $parts)
    {
        foreach ($parts as $part) {
            $this->addPart($part);
        }
        return $this;
    }

    public function addPart($part)
    {
        $this->parts[] = $part;
        return $this;
    }

    public function run()
    {
        if (!$this->isInitialized()) {
            return Result::error($this, 'Semver has already been initialized.');
        }

        $currentBuild = $this->version->getBuild();
        if ($currentBuild instanceof Build && null === $this->number && $currentBuild->getParts() == $this->parts) {
            $this->number = (int)$this->version->getBuild()->getNumber() + 1;
        }

        $build = new Build();
        $build->setNumber((int)$this->number);
        if (!empty($this->parts)) {
            $build->setParts($this->parts);
        }
        $this->version->setBuild($build);

        $this->printTaskInfo('Updating version to ' . $this->version);
        return $this->write();
    }
}
