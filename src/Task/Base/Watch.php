<?php
namespace Robo\Task\Base;

use Lurker\Event\FilesystemEvent;
use Lurker\ResourceWatcher;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Runs task when specified file or dir was changed.
 * Uses Lurker library.
 *
 * ``` php
 * <?php
 * $this->taskWatch()
 *  ->monitor('composer.json', function() {
 *      $this->taskComposerUpdate()->run();
 * })->monitor('src', function() {
 *      $this->taskExec('phpunit')->run();
 * })->run();
 * ?>
 * ```
 */
class Watch extends BaseTask
{
    protected $closure;
    protected $monitor = [];
    protected $bindTo;

    public function __construct($bindTo)
    {
        $this->bindTo = $bindTo;
    }

    public function monitor($paths, \Closure $callable)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }
        $this->monitor[] = [$paths, $callable];
        return $this;

    }

    public function run()
    {
        $watcher = new ResourceWatcher();

        foreach ($this->monitor as $k => $monitor) {
            $closure = $monitor[1];
            $closure->bindTo($this->bindTo);
            foreach ($monitor[0] as $i => $dir) {
                $watcher->track("fs.$k.$i", $dir, FilesystemEvent::MODIFY);
                $this->printTaskInfo("Watching <info>$dir</info> for changes...");
                $watcher->addListener("fs.$k.$i", $closure);
            }
        }

        $watcher->start();
        return Result::success($this);
    }

}
