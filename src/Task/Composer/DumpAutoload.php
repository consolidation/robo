<?php
namespace Robo\Task\Composer;

/**
 * Composer Dump Autoload
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskComposerDumpAutoload()->run();
 *
 * // dump auto loader with custom path
 * $this->taskComposerDumpAutoload('path/to/my/composer.phar')
 *      ->preferDist()
 *      ->run();
 *
 * // optimize autoloader dump with custom path
 * $this->taskComposerDumpAutoload('path/to/my/composer.phar')
 *      ->optimize()
 *      ->run();
 *
 * // optimize autoloader dump with custom path and no dev
 * $this->taskComposerDumpAutoload('path/to/my/composer.phar')
 *      ->optimize()
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class DumpAutoload extends Base
{
    protected $action = 'dump-autoload';

    protected $optimize;

    public function optimize()
    {
        $this->optimize = "--optimize";
        return $this;
    }

    public function getCommand()
    {
        $this->option($this->optimize);
        return parent::getCommand();
    }

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Dumping Autoloader: '.$command);
        return $this->executeCommand($command);
    }

}
