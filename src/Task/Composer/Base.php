<?php
namespace Robo\Task\Composer;

use Robo\Task\BaseTask;
use Robo\Exception\TaskException;

abstract class Base extends BaseTask
{
    use \Robo\Common\ExecOneCommand;

    protected $prefer;
    protected $dev;
    protected $optimizeAutoloader;
    protected $dir;

    /**
     * Action to use
     *
     * @var string
     */
    protected $action = '';

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
            throw new TaskException(__CLASS__, "Neither local composer.phar nor global composer installation not found");
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
