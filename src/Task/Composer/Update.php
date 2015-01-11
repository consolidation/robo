<?php
namespace Robo\Task\Composer;

use Robo\Task\Composer;
use Robo\Task\Shared\TaskInterface;

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
class Update extends Base implements TaskInterface
{
    protected $action = 'update';

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Updating Packages: ' . $command);
        return $this->executeCommand($command);
    }

}