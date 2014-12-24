<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;

trait Phpspec {
    protected function taskPhpspec($pathToPhpspec = null)
    {
        return new PhpspecTask($pathToPhpspec);
    }
}

/**
 * Executes Phpspec tests
 *
 * ``` php
 * <?php
 * $this->taskPhpspec()
 *      ->run();
 * ?>
 * ```
 *
 */
class PhpspecTask implements TaskInterface, CommandInterface{
    use \Robo\Output;
    use \Robo\Task\Shared\Executable;

    protected $command;

    /**
    * @var array $formaters available formaters for format option
    */
    protected $formaters = ['progress', 'html', 'pretty', 'junit', 'dot'];

    /**
    * @var array $verbose_levels available verbose levels
    */
    protected $verbose_levels = ['v', 'vv', 'vvv'];

    public function __construct($pathToPhpspec = null)
    {
        if ($pathToPhpspec) {
            $this->command = "$pathToPhpspec";
            $this->arg('run');
        } elseif (file_exists('vendor/phpspec/phpspec/bin/phpspec')) {
            $this->command = 'vendor/phpspec/phpspec/bin/phpspec run';
        } else {
            throw new Shared\TaskException(__CLASS__,"Neither composer nor phar installation of Phpspec found");
        }
    }

    public function stopOnFailure() {
        $this->option('stop-on-failure');
        return $this;
    }

    public function noCodeGeneration() {
        $this->option('no-code-generation');
        return $this;
    }

    public function quiet() {
        $this->option('quiet');
        return $this;
    }

    public function verbose($level = 'v') {
        if(!in_array($level, $this->verbose_levels)) {
            throw new \InvalidArgumentException('expected '.implode(',', $this->verbose_levels));
        }
        $this->option('-'.$level);
        return $this;
    }

    public function noAnsi() {
        $this->option('no-ansi');
        return $this;
    }

    public function noInteraction() {
        $this->option('no-interaction');
        return $this;
    }

    public function config($config_file) {
        $this->option('config', $config_file);
        return $this;
    }

    public function format($formater) {
        if(!in_array($formater, $this->formaters )) {
            throw new \InvalidArgumentException('expected '.implode(',', $this->formaters));
        }
        $this->option('format', $formater);
        return $this;
    }

    public function getCommand()
    {
        return $this->command . $this->arguments;
    }

    public function run()
    {
        $this->printTaskInfo('Running phpspec '. $this->arguments);
        return $this->executeCommand($this->getCommand());
    }

} 