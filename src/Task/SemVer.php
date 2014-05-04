<?php

namespace Robo\Task;

use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;
use Robo\Result;

/**
 * Tasks to help managing a project's semantic versioning.
 *
 * @package Robo\Task
 */
trait SemVer
{
    protected function taskSemVer($pathToSemVer = '.semver')
    {
        return new SemVerStackTask($pathToSemVer);
    }
}

class SemVerStackTask implements TaskInterface
{
    const SEMVER = "---\n:major: %d\n:minor: %d\n:patch: %d\n:special: '%s'\n:metadata: '%s'";

    const REGEX = "/^\-\-\-\n:major:\s(0|[1-9]\d*)\n:minor:\s(0|[1-9]\d*)\n:patch:\s(0|[1-9]\d*)\n:special:\s'([a-zA-z0-9]*\.?(?:0|[1-9]\d*)?)'\n:metadata:\s'((?:0|[1-9]\d*)?(?:\.[a-zA-z0-9\.]*)?)'/";

    protected $format = 'v%M.%m.%p%s';

    protected $specialSeparator = '-';

    protected $metadataSeparator = '+';

    protected $path;

    protected $version = [
        'major' => 0,
        'minor' => 0,
        'patch' => 0,
        'special' => '',
        'metadata' => ''
    ];

    public function __construct($pathToSemVer = '.semver')
    {
        $this->path = $pathToSemVer;

        if (file_exists($this->path)) {
            $this->parse();
        } else {
            $this->dump();
        }
    }

    public function __toString()
    {
        $search = ['%M', '%m', '%p', '%s'];
        $replace = $this->version + ['extra' => ''];

        foreach (['special', 'metadata'] as $key) {
            if (!empty($replace[$key])) {
                $separator = $key . 'Separator';
                $replace['extra'] .= $this->{$separator} . $replace[$key];
            }
            unset($replace[$key]);
        }

        return str_replace($search, $replace, $this->format);
    }

    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    public function setMetadataSeparator($separator)
    {
        $this->metadataSeparator = $separator;
        return $this;
    }

    public function setPrereleaseSeparator($separator)
    {
        $this->specialSeparator = $separator;
        return $this;
    }

    public function increment($what = 'patch')
    {
        $types = ['major', 'minor', 'patch'];
        if (!in_array($what, $types)) {
            throw new TaskException(
                $this,
                'Bad argument, only one of the following is allowed: ' .
                implode(', ', $types)
            );
        }

        $this->version[$what]++;
        return $this;
    }

    public function prerelease($tag = 'RC')
    {
        if (!is_string($tag)) {
            throw new TaskExecption($this, 'Bad argument, only strings allowed.');
        }

        $number = 0;

        if (!empty($this->version['special'])) {
            list($current, $number) = explode('.', $this->version['special']);
            if ($tag != $current) {
                $number = 0;
            }
        }

        $number++;

        $this->version['special'] = implode('.', [$tag, $number]);
        return $this;
    }

    public function metadata($data)
    {
        if (is_array($data)) {
            $data = implode('.', $data);
        }

        $this->version['metadata'] = $data;
        return $this;
    }

    public function run()
    {
        $this->dump();
        return (string)$this;
    }

    protected function dump()
    {
        extract($this->version);
        $semver = sprintf(self::SEMVER, $major, $minor, $patch, $special, $metadata);
        exec(sprintf('echo "%s" > %s', $semver, $this->path));
    }

    protected function parse()
    {
        exec('cat ' . $this->path, $output, $code);

        if (!preg_match_all(self::REGEX, implode("\n", $output), $matches)) {
            throw new TaskException($this, 'Bad semver file.');
        }

        list(, $major, $minor, $patch, $special, $metadata) = array_map('current', $matches);
        $this->version = compact('major', 'minor', 'patch', 'special', 'metadata');
    }
}
