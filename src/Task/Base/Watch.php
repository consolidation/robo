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
    /**
     * @var \Closure
     */
    protected $closure;

    /**
     * @var array
     */
    protected $monitor = [];

    /**
     * @var object
     */
    protected $bindTo;

    /**
     * @param $bindTo
     */
    public function __construct($bindTo)
    {
        $this->bindTo = $bindTo;
    }

    /**
     * @param string|string[] $paths
     * @param \Closure $callable
     * @param int|int[] $events
     *
     * @return $this
     */
    public function monitor($paths, \Closure $callable, $events = FilesystemEvent::MODIFY)
    {
        if (!is_array($paths)) {
            $paths = [$paths];
        }

        if (!is_array($events)) {
            $events = [$events];
        }

        $this->monitor[] = [$paths, $callable, $events];
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!class_exists('Lurker\\ResourceWatcher')) {
            return Result::errorMissingPackage($this, 'ResourceWatcher', 'henrikbjorn/lurker');
        }

        $watcher = new ResourceWatcher();

        foreach ($this->monitor as $k => $monitor) {
            /** @var \Closure $closure */
            $closure = $monitor[1];
            $closure->bindTo($this->bindTo);
            foreach ($monitor[0] as $i => $dir) {
                foreach ($monitor[2] as $j => $event) {
                    $watcher->track("fs.$k.$i.$j", $dir, $event);
                    $watcher->addListener("fs.$k.$i.$j", $closure);
                }
                $this->printTaskInfo('Watching {dir} for changes...', ['dir' => $dir]);
            }
        }

        $watcher->start();
        return Result::success($this);
    }
}
