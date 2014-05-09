<?php
namespace Robo\Task;

use Robo\Result;
use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;

trait Composer {

    /**
     * @param null $pathToComposer
     * @return ComposerInstallTask
     */
    protected function taskComposerInstall($pathToComposer = null)
    {
        return new ComposerInstallTask($pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return ComposerUpdateTask
     */
    protected function taskComposerUpdate($pathToComposer = null)
    {
        return new ComposerUpdateTask($pathToComposer);
    }

    /**
     * @param null $pathToComposer
     * @return ComposerDumpAutoloadTask
     */
    protected function taskComposerDumpAutoload($pathToComposer = null)
    {
        return new ComposerDumpAutoloadTask($pathToComposer);
    }
}

abstract class BaseComposerTask
{
    use \Robo\Output;
    use \Robo\Task\Shared\Executable;

    protected $prefer;
    protected $dev;
    protected $optimizeAutoloader;
    protected $arguments;

    /**
     * adds `prefer-dist` option to composer
     *
     * @return $this
     */
    public function preferDist()
    {
        $this->prefer = '--prefer-dist';
        return $this;
    }

    /**
     * adds `prefer-source` option to composer
     *
     * @return $this
     */
    public function preferSource()
    {
        $this->prefer = '--prefer-source';
        return $this;
    }

    /**
     * adds `no-dev` option to composer
     *
     * @return $this
     */
    public function noDev()
    {
        $this->dev = '--no-dev';
        return $this;
    }

    /**
     * adds `optimize-autoloader` option to composer
     *
     * @return $this
     */
    public function optimizeAutoloader()
    {
        $this->optimizeAutoloader = '--optimize-autoloader';
        return $this;
    }

    public function __construct($pathToComposer = null)
    {
        if ($pathToComposer) {
            $this->command = $pathToComposer;
        } elseif (file_exists('composer.phar')) {
            $this->command = 'php composer.phar';
        } elseif (is_executable('/usr/bin/composer')) {
            $this->command = '/usr/bin/composer';
        } elseif (is_executable('/usr/local/bin/composer')) {
			$this->command = '/usr/local/bin/composer';
		} else {
            throw new TaskException(__CLASS__,"Neither local composer.phar nor global composer installation not found");
        }
    }

    public function getCommand()
    {
        $this->option($this->prefer)
             ->option($this->dev)
             ->option($this->optimizeAutoloader);
        return "{$this->command} {$this->action}{$this->arguments}";
    }
}

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
 * ?>
 * 
 * // optimize autoloader with custom path
 * $this->taskComposerInstall('path/to/my/composer.phar')
 *      ->optimizeAutoloader()
 *      ->run();
 * ?>
 * ```
 */
class ComposerInstallTask extends BaseComposerTask implements TaskInterface {

    protected $action = 'install';

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Installing Packages: ' . $command);
        return $this->executeCommand($command);
    }

}

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
 * ?>
 * 
 * // optimize autoloader with custom path
 * $this->taskComposerUpdate('path/to/my/composer.phar')
 *      ->optimizeAutoloader()
 *      ->run();
 * ?>
 * ```
 */
class ComposerUpdateTask extends BaseComposerTask implements TaskInterface {

    protected $action = 'update';

    public function run()
    {
        $command = $this->getCommand();
        $this->printTaskInfo('Updating Packages: '.$command);
        return $this->executeCommand($command);
    }

}

/**
 * Composer Update
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
 * ?>
 * 
 * // optimize autoloader dump with custom path
 * $this->taskComposerDumpAutoload('path/to/my/composer.phar')
 *      ->optimize()
 *      ->run();
 * ?>
 * 
 * // optimize autoloader dump with custom path and no dev
 * $this->taskComposerDumpAutoload('path/to/my/composer.phar')
 *      ->optimize()
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class ComposerDumpAutoloadTask extends BaseComposerTask implements TaskInterface {

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
