<?php
namespace Robo\Task\Development;

use Robo\Task\BaseTask;
use Robo\Task\File\Replace;
use Robo\Result;
use Robo\Task\Development;

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
 * Changes may be formatted into a custom file format. Handler can be either a function,
 * a public method or a closure.
 *
 * ``` php
 * <?php
 * function myChangelogHandler($changelogTask)
 * {
 *     // how to manage contents of your CHANGELOG file
 * }
 *
 * $this->taskChangelog()
 *  ->handler('myChangelogHandler')
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
class Changelog extends BaseTask
{
    use \Robo\Common\DynamicParams;

    protected $filename;
    protected $log = [];
    protected $anchor = "# Changelog";
    protected $version = "";
    protected $handler;
    protected $bindTo;

    /**
     * @param string $filename
     * @return \Robo\Task\Development\Changelog
     */
    public static function init($filename = 'CHANGELOG.md')
    {
        return new Changelog($filename, $this);
    }

    public function askForChanges()
    {
        while ($resp = $this->ask("Changed in this release: ")) {
            $this->log[] = $resp;
        }
        return $this;
    }

    public function __construct($filename, $bindTo)
    {
        $this->filename = $filename;
        $this->bindTo   = $bindTo;
        $this->handler  = $this->getClosure();
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

    public function getFilename()
    {
        return $this->filename;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function run()
    {
        if (empty($this->log)) {
            return Result::error($this, "Changelog is empty");
        }

        if (is_callable($this->handler)) {
            return call_user_func($this->handler, $this);

        } elseif (method_exists($this->bindTo, $this->handler)) {
            return $this->bindTo->{$this->handler}($this);

        } else {
            $message = sprintf('Invalid handler "%s"', $this->handler);
            return Result::error($this, $message);
        }

        return new Result($this, $result->getExitCode(), $result->getMessage(), $this->log);
    }

    public function handler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    protected function getClosure()
    {
        return function () {
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
                $this->printTaskInfo("Creating {$this->filename}");
                $res = file_put_contents($this->filename, $this->anchor);
                if ($res === false) {
                    return Result::error($this, "File {$this->filename} cant be created");
                }
            }

            // trying to append to changelog for today
            $result = (new Replace($this->filename))
                ->from($ver)
                ->to($text)
                ->run();

            if (!$result->getData()['replaced']) {
                $result = (new Replace($this->filename))
                    ->from($this->anchor)
                    ->to($this->anchor . "\n\n" . $text)
                    ->run();
            }
            return $result;
        };
    }
}
