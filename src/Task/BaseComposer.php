<?php
namespace Robo\Task;

use Robo\Add\Output;
use Robo\TaskException;

abstract class BaseComposer {

    use Output;

    protected $prefer;
    protected $dev = true;

    public function preferDist()
    {
        $this->prefer = '--prefer-dist';
        return $this;
    }

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