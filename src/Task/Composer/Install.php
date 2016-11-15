<?php
namespace Robo\Task\Composer;

/**
 * Composer Install
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskComposerInstall()->run();
 *
 * // prefer dist with custom path
 * $this->taskComposerInstall('path/to/my/composer.phar')
 *      ->preferDist()
 *      ->run();
 *
 * // optimize autoloader with custom path
 * $this->taskComposerInstall('path/to/my/composer.phar')
 *      ->optimizeAutoloader()
 *      ->run();
 * ?>
 * ```
 */
class Install extends Base
{
    /**
     * {@inheritdoc}
     */
    protected $action = 'install';

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Installing Packages: {command}', ['command' => $command]);
        return $this->executeCommand($command);
    }
}
