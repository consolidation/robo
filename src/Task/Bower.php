<?php
namespace Robo\Task;

use Robo\Task\Shared\CommandInterface;
use Robo\Task\Shared\TaskInterface;
use Robo\Task\Shared\TaskException;

/**
 * Contains tasks for bower.
 *
 * @package  Robo\Task
 */
trait Bower {

	protected function taskBowerInstall($pathToBower = null) {
		return new BowerInstallTask($pathToBower);
	}

	protected function taskBowerUpdate($pathToBower = null) {
		return new BowerUpdateTask($pathToBower);
	}
}

abstract class BaseBowerTask {
    use \Robo\Task\Shared\Executable;
	use \Robo\Output;

	protected $opts = [];
    protected $action = '';

    /**
     * adds `allow-root` option to bower
     *
     * @return $this
     */
	public function allowRoot() {
        $this->option('allow-root');
		return $this;
	}

    /**
     * adds `force-latest` option to bower
     *
     * @return $this
     */
	public function forceLatest() {
		$this->option('force-latest');
		return $this;
	}

    /**
     * adds `production` option to bower
     *
     * @return $this
     */
	public function noDev() {
		$this->option('production');
		return $this;
	}

    /**
     * adds `offline` option to bower
     *
     * @return $this
     */
	public function offline() {
		$this->option('offline');
		return $this;
	}

	public function __construct($pathToBower = null) {
		if ($pathToBower) {
			$this->command = $pathToBower;
		} elseif (is_executable('/usr/bin/bower')) {
			$this->command = '/usr/bin/bower';
		} elseif (is_executable('/usr/local/bin/bower')) {
			$this->command = '/usr/local/bin/bower';
		} else {
			throw new TaskException(__CLASS__, "Executable not found.");
		}
	}

    public function getCommand()
    {
        return "{$this->command} {$this->action}{$this->arguments}";
    }
}

/**
 * Bower Install
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskBowerInstall()->run();
 *
 * // prefer dist with custom path
 * $this->taskBowerInstall('path/to/my/bower')
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class BowerInstallTask extends BaseBowerTask implements TaskInterface, CommandInterface  {

    protected $action = 'install';

	public function run() {
		$this->printTaskInfo('Install Bower packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
	}
}

/**
 * Bower Update
 *
 * ``` php
 * <?php
 * // simple execution
 * $this->taskBowerUpdate()->run();
 *
 * // prefer dist with custom path
 * $this->taskBowerUpdate('path/to/my/bower')
 *      ->noDev()
 *      ->run();
 * ?>
 * ```
 */
class BowerUpdateTask extends BaseBowerTask implements TaskInterface {

    protected $action = 'update';

	public function run() {
		$this->printTaskInfo('Update Bower packages: ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
	}
}
