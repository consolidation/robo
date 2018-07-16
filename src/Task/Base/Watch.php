<?php
namespace Robo\Task\Base;

use Lurker\ResourceWatcher;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Runs task when specified file or dir was changed.
 * Uses Lurker library.
 * Monitor third parameter takes Lurker filesystem events types to watch.
 * By default its set to MODIFY event.
 *
 * ``` php
 * <?php
 * $this->taskWatch()
 *      ->monitor(
 *          'composer.json',
 *          function() {
 *              $this->taskComposerUpdate()->run();
 *          }
 *      )->monitor(
 *          'src',
 *          function() {
 *              $this->taskExec('phpunit')->run();
 *          },
 *          \Lurker\Event\FilesystemEvent::ALL
 *      )->monitor(
 *          'migrations',
 *          function() {
 *              //do something
 *          },
 *          [
 *              \Lurker\Event\FilesystemEvent::CREATE,
 *              \Lurker\Event\FilesystemEvent::DELETE
 *          ]
 *      )->run();
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
    public function monitor($paths, \Closure $callable, $events = 2)
    {
        $this->monitor[] = [(array)$paths, $callable, (array)$events];
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
