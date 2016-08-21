<?php
namespace Robo\Task\Development;

use Robo\Task\BaseTask;
use Robo\Task\File\Replace;
use Robo\Task\Filesystem;
use Robo\Result;
use Robo\Task\Development;
use Robo\Contract\BuilderAwareInterface;
use Robo\Common\BuilderAwareTrait;

/**
 * Helps to manage changelog file.
 * Creates or updates `changelog.md` file with recent changes in current version.
 *
 * ``` php
 * <?php
 * $version = "0.1.0";
 * $this->taskChangelog()
 *  ->version($version)
 *  ->change("released to github")
 *  ->run();
 * ?>
 * ```
 *
 * Changes can be asked from Console
 *
 * ``` php
 * <?php
 * $this->taskChangelog()
 *  ->version($version)
 *  ->askForChanges()
 *  ->run();
 * ?>
 * ```
 *
 * @method Development\Changelog filename(string $filename)
 * @method Development\Changelog anchor(string $anchor)
 * @method Development\Changelog version(string $version)
 */
class Changelog extends BaseTask implements BuilderAwareInterface
{
    use BuilderAwareTrait;

    protected $filename;
    protected $log = [];
    protected $anchor = "# Changelog";
    protected $version = "";

    public function filename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    public function log($item)
    {
        $this->log[] = $item;
        return $this;
    }

    public function anchor($anchor)
    {
        $this->anchor = $anchor;
        return $this;
    }

    public function version($version)
    {
        $this->version = $version;
        return $this;
    }

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function changes(array $data)
    {
        $this->log = array_merge($this->log, $data);
        return $this;
    }

    public function change($change)
    {
        $this->log[] = $change;
        return $this;
    }

    public function getChanges()
    {
        return $this->log;
    }

    public function run()
    {
        if (empty($this->log)) {
            return Result::error($this, "Changelog is empty");
        }
        $text = implode(
            "\n",
            array_map(
                function ($i) {
                        return "* $i *" . date('Y-m-d') . "*";
                },
                $this->log
            )
        ) . "\n";
        $ver = "#### {$this->version}\n\n";
        $text = $ver . $text;

        if (!file_exists($this->filename)) {
            $this->printTaskInfo('Creating {filename}', ['filename' => $this->filename]);
            $res = file_put_contents($this->filename, $this->anchor);
            if ($res === false) {
                return Result::error($this, "File {filename} cant be created", ['filename' => $this->filename]);
            }
        }

        // trying to append to changelog for today
        $result = $this->collectionBuilder()->taskReplace($this->filename)
            ->from($ver)
            ->to($text)
            ->run();

        if (!isset($result['replaced']) || !$result['replaced']) {
            $result = $this->collectionBuilder()->taskReplace($this->filename)
                ->from($this->anchor)
                ->to($this->anchor . "\n\n" . $text)
                ->run();
        }

        return new Result($this, $result->getExitCode(), $result->getMessage(), $this->log);
    }
}
