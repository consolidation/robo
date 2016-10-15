<?php
namespace Robo\Task\Testing;

use Robo\Contract\PrintedInterface;
use Robo\Task\BaseTask;
use Robo\Contract\CommandInterface;

/**
 * Executes Phpspec tests
 *
 * ``` php
 * <?php
 * $this->taskPhpspec()
 *      ->format('pretty')
 *      ->noInteraction()
 *      ->run();
 * ?>
 * ```
 *
 */
class Phpspec extends BaseTask implements CommandInterface, PrintedInterface
{
    use \Robo\Common\ExecOneCommand;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var string[] $formaters available formaters for format option
     */
    protected $formaters = ['progress', 'html', 'pretty', 'junit', 'dot', 'tap'];

    /**
     * @var array $verbose_levels available verbose levels
     */
    protected $verbose_levels = ['v', 'vv', 'vvv'];

    public function __construct($pathToPhpspec = null)
    {
        $this->command = $pathToPhpspec;
        if (!$this->command) {
            $this->command = $this->findExecutable('phpspec');
        }
        if (!$this->command) {
            throw new \Robo\Exception\TaskException(__CLASS__, "Neither composer nor phar installation of Phpspec found");
        }
        $this->arg('run');
    }

    public function stopOnFail()
    {
        $this->option('stop-on-failure');
        return $this;
    }

    public function noCodeGeneration()
    {
        $this->option('no-code-generation');
        return $this;
    }

    public function quiet()
    {
        $this->option('quiet');
        return $this;
    }

    public function verbose($level = 'v')
    {
        if (!in_array($level, $this->verbose_levels)) {
            throw new \InvalidArgumentException('expected ' . implode(',', $this->verbose_levels));
        }
        $this->option('-' . $level);
        return $this;
    }

    public function noAnsi()
    {
        $this->option('no-ansi');
        return $this;
    }

    public function noInteraction()
    {
        $this->option('no-interaction');
        return $this;
    }

    public function config($config_file)
    {
        $this->option('config', $config_file);
        return $this;
    }

    public function format($formater)
    {
        if (!in_array($formater, $this->formaters)) {
            throw new \InvalidArgumentException('expected ' . implode(',', $this->formaters));
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
        $this->printTaskInfo('Running phpspec {arguments}', ['arguments' => $this->arguments]);
        return $this->executeCommand($this->getCommand());
    }
}
