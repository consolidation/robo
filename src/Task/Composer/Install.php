<?php
namespace Robo\Task\Composer;

use Robo\Task\Composer;
use Robo\Contract\TaskInterface;

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
class Install extends Base implements TaskInterface
{
    protected $action = 'install';

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Installing Packages: ' . $command);
        return $this->executeCommand($command);
    }

}