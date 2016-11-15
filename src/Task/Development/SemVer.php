<?php
namespace Robo\Task\Development;

use Robo\Result;
use Robo\Contract\TaskInterface;
use Robo\Exception\TaskException;

/**
 * Helps to maintain `.semver` file.
 *
 * ```php
 * <?php
 * $this->taskSemVer('.semver')
 *      ->increment()
 *      ->run();
 * ?>
 * ```
 *
 */
class SemVer implements TaskInterface
{
    const SEMVER = "---\n:major: %d\n:minor: %d\n:patch: %d\n:special: '%s'\n:metadata: '%s'";

    const REGEX = "/^\-\-\-\n:major:\s(0|[1-9]\d*)\n:minor:\s(0|[1-9]\d*)\n:patch:\s(0|[1-9]\d*)\n:special:\s'([a-zA-z0-9]*\.?(?:0|[1-9]\d*)?)'\n:metadata:\s'((?:0|[1-9]\d*)?(?:\.[a-zA-z0-9\.]*)?)'/";

    /**
     * @var string
     */
    protected $format = 'v%M.%m.%p%s';

    /**
     * @var string
     */
    protected $specialSeparator = '-';

    /**
     * @var string
     */
    protected $metadataSeparator = '+';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $version = [
        'major' => 0,
        'minor' => 0,
        'patch' => 0,
        'special' => '',
        'metadata' => ''
    ];

    /**
     * @param string $filename
     */
    public function __construct($filename = '')
    {
        $this->path = $filename;

        if (file_exists($this->path)) {
            $this->parse();
        }
    }

    /**
     * @return string
     */
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

    /**
     * @param string $format
     *
     * @return $this
     */
    public function setFormat($format)
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setMetadataSeparator($separator)
    {
        $this->metadataSeparator = $separator;
        return $this;
    }

    /**
     * @param string $separator
     *
     * @return $this
     */
    public function setPrereleaseSeparator($separator)
    {
        $this->specialSeparator = $separator;
        return $this;
    }

    /**
     * @param string $what
     *
     * @return $this
     *
     * @throws \Robo\Exception\TaskException
     */
    public function increment($what = 'patch')
    {
        switch ($what) {
            case 'major':
                $this->version['major']++;
                $this->version['minor'] = 0;
                $this->version['patch'] = 0;
                break;
            case 'minor':
                $this->version['minor']++;
                $this->version['patch'] = 0;
                break;
            case 'patch':
                $this->version['patch']++;
                break;
            default:
                throw new TaskException(
                    $this,
                    'Bad argument, only one of the following is allowed: major, minor, patch'
                );
        }
        return $this;
    }

    /**
     * @param string $tag
     *
     * @return $this
     *
     * @throws \Robo\Exception\TaskException
     */
    public function prerelease($tag = 'RC')
    {
        if (!is_string($tag)) {
            throw new TaskException($this, 'Bad argument, only strings allowed.');
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

    /**
     * @param array|string $data
     *
     * @return $this
     */
    public function metadata($data)
    {
        if (is_array($data)) {
            $data = implode('.', $data);
        }

        $this->version['metadata'] = $data;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $written = $this->dump();
        return new Result($this, (int)($written === false), $this->__toString());
    }

    /**
     * @return bool
     *
     * @throws \Robo\Exception\TaskException
     */
    protected function dump()
    {
        extract($this->version);
        $semver = sprintf(self::SEMVER, $major, $minor, $patch, $special, $metadata);
        if (is_writeable($this->path) === false || file_put_contents($this->path, $semver) === false) {
            throw new TaskException($this, 'Failed to write semver file.');
        }
        return true;
    }

    /**
     * @throws \Robo\Exception\TaskException
     */
    protected function parse()
    {
        $output = file_get_contents($this->path);

        if (!preg_match_all(self::REGEX, $output, $matches)) {
            throw new TaskException($this, 'Bad semver file.');
        }

        list(, $major, $minor, $patch, $special, $metadata) = array_map('current', $matches);
        $this->version = compact('major', 'minor', 'patch', 'special', 'metadata');
    }
}
