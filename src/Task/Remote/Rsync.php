<?php
namespace Robo\Task\Remote;

use Robo\Contract\CommandInterface;
use Robo\Task\BaseTask;
use Robo\Task\Remote;
use Robo\Exception\TaskException;

/**
 * Executes rsync in a flexible manner.
 *
 * ``` php
 * $this->taskRsync()
 *   ->fromPath('src/')
 *   ->toHost('localhost')
 *   ->toUser('dev')
 *   ->toPath('/var/www/html/app/')
 *   ->recursive()
 *   ->excludeVcs()
 *   ->checksum()
 *   ->wholeFile()
 *   ->verbose()
 *   ->progress()
 *   ->humanReadable()
 *   ->stats()
 *   ->run();
 * ```
 *
 * You could also clone the task and do a dry-run first:
 *
 * ``` php
 * $rsync = $this->taskRsync()
 *   ->fromPath('src/')
 *   ->toPath('example.com:/var/www/html/app/')
 *   ->archive()
 *   ->excludeVcs()
 *   ->progress()
 *   ->stats();
 *
 * $dryRun = clone $rsync;
 * $dryRun->dryRun()->run();
 * if ('y' === $this->ask('Do you want to run (y/n)')) {
 *   $rsync->run();
 * }
 * ```
 *
 * @method \Robo\Task\Remote\Rsync fromUser(string $user)
 * @method \Robo\Task\Remote\Rsync fromHost(string $hostname)
 * @method \Robo\Task\Remote\Rsync toUser(string $user)
 * @method \Robo\Task\Remote\Rsync toHost(string $hostname)
 */
class Rsync extends BaseTask implements CommandInterface
{
    use \Robo\Common\ExecOneCommand;
    use \Robo\Common\DynamicParams;

    protected $fromUser;

    protected $fromHost;

    protected $fromPath;

    protected $toUser;

    protected $toHost;

    protected $toPath;

    public static function init()
    {
        return new static();
    }

    public function __construct()
    {
        $this->command = 'rsync';
    }

    /**
     * This can either be a full rsync path spec (user@host:path) or just a path.
     * In case of the former do not specify host and user.
     *
     * @param string $path
     * @return $this
     */
    public function fromPath($path)
    {
        $this->fromPath = $path;

        return $this;
    }

    /**
     * This can either be a full rsync path spec (user@host:path) or just a path.
     * In case of the former do not specify host and user.
     *
     * @param string $path
     * @return $this
     */
    public function toPath($path)
    {
        $this->toPath = $path;

        return $this;
    }

    public function progress()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function stats()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function recursive()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function verbose()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function checksum()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function archive()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function compress()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function owner()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function group()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function times()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function delete()
    {
        $this->option(__FUNCTION__);

        return $this;
    }

    public function timeout($seconds)
    {
        $this->option(__FUNCTION__, $seconds);

        return $this;
    }

    public function humanReadable()
    {
        $this->option('human-readable');

        return $this;
    }

    public function wholeFile()
    {
        $this->option('whole-file');

        return $this;
    }

    public function dryRun()
    {
        $this->option('dry-run');

        return $this;
    }

    public function itemizeChanges()
    {
        $this->option('itemize-changes');

        return $this;
    }

    /**
     * Excludes .git/, .svn/ and .hg/ folders.
     *
     * @return $this
     */
    public function excludeVcs()
    {
        $this->exclude('.git/')
            ->exclude('.svn/')
            ->exclude('.hg/');

        return $this;
    }

    public function exclude($pattern)
    {
        return $this->option('exclude', escapeshellarg($pattern));
    }

    public function excludeFrom($file)
    {
        if (!is_readable($file)) {
            throw new TaskException($this, "Exclude file $file is not readable");
        }

        return $this->option('exclude-from', $file);
    }

    public function filesFrom($file)
    {
        if (!is_readable($file)) {
            throw new TaskException($this, "Files-from file $file is not readable");
        }

        return $this->option('files-from', $file);
    }

    /**
     * @return \Robo\Result
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo("running <info>{$command}</info>");

        return $this->executeCommand($command);
    }

    /**
     * Returns command that can be executed.
     * This method is used to pass generated command from one task to another.
     *
     * @return string
     */
    public function getCommand()
    {
        $this->option(null, $this->getPathSpec('from'))
            ->option(null, $this->getPathSpec('to'));

        return $this->command . $this->arguments;
    }

    protected function getPathSpec($type)
    {
        if ($type !== 'from' && $type !== 'to') {
            throw new TaskException($this, 'Type must be "from" or "to".');
        }
        foreach (['host', 'user', 'path'] as $part) {
            $varName = $type . ucfirst($part);
            $$part = $this->$varName;
        }
        $spec = isset($path) ? $path : '';
        if (!empty($host)) {
            $spec = "{$host}:{$spec}";
        }
        if (!empty($user)) {
            $spec = "{$user}@{$spec}";
        }

        return $spec;
    }
}