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
    /**
     * {@inheritdoc}
     */
    protected $action = 'dump-autoload';

    /**
     * @var string
     */
    protected $optimize;

    /**
     * @return $this
     */
    public function optimize()
    {
        $this->optimize = "--optimize";
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand()
    {
        $this->option($this->optimize);
        return parent::getCommand();
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Dumping Autoloader: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
