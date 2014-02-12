<?php
namespace Robo\Task;

use Robo\Result;

trait Composer {

    /**
     * @param null $pathToComposer
     * @return ComposerInstallTask
     */
    protected function taskComposerInstall($pathToComposer = null)
    {
        return new ComposerInstallTask($pathToComposer);
    }

    protected function taskComposerUpdate($pathToComposer = null)
    {
        return new ComposerUpdateTask($pathToComposer);
    }
}

abstract class BaseComposerTask {

    use \Robo\Output;

    protected $prefer;
    protected $dev = true;

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

    public function noDev()
    {
        $this->dev = false;
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
        } else {
            throw new TaskException(__CLASS__,"Neither local composer.phar nor global composer installation not found");
        }
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
 * ```
 *
 *
 * Class ComposerInstallTask
 * @package Robo\Task
 */
class ComposerInstallTask extends BaseComposerTask implements TaskInterface {

    public function run()
    {
        $options = $this->prefer;
        $this->dev ?: $options.= "--no-dev";
        $this->printTaskInfo('Installing Packages: '.$options);
        $line = system($this->command.' install '.$options, $code);
        return new Result($this, $code, $line);
    }

}

class ComposerUpdateTask extends BaseComposerTask implements TaskInterface {

    public function run()
    {
        $options = $this->prefer;
        $this->dev ?: $options.= "--no-dev";
        $this->printTaskInfo('Updating Packages: '.$options);
        $line = system($this->command.' update '.$options, $code);
        return new Result($this, $code, $line);
    }

}