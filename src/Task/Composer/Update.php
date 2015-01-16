<?php
namespace Robo\Task\Composer;

/**
 * Composer Update
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskComposerUpdate()->run();
 *
 * // prefer dist with custom path
 * $this->taskComposerUpdate('path/to/my/composer.phar')
 *      ->preferDist()
 *      ->run();
 *
 * // optimize autoloader with custom path
 * $this->taskComposerUpdate('path/to/my/composer.phar')
 *      ->optimizeAutoloader()
 *      ->run();
 * ?>
 * ```
 */
class Update extends Base
{
    protected $action = 'update';

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Updating Packages: ' . $command);
        return $this->executeCommand($command);
    }

}