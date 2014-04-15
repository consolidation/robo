<?php
namespace Robo\Task;

use Robo\Result;

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

	use \Robo\Output;

	protected $opts = [];

    /**
     * adds `allow-root` option to bower
     *
     * @return $this
     */
	public function allowRoot() {
		array_push($this->opts, '--allow-root');
		return $this;
	}

    /**
     * adds `force-latest` option to bower
     *
     * @return $this
     */
	public function forceLatest() {
		array_push($this->opts, '--force-latest');
		return $this;
	}

    /**
     * adds `production` option to bower
     *
     * @return $this
     */
	public function noDev() {
		array_push($this->opts, '--production');
		return $this;
	}

    /**
     * adds `offline` option to bower
     *
     * @return $this
     */
	public function offline() {
		array_push($this->opts, '--offline');
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
class BowerInstallTask extends BaseBowerTask implements TaskInterface {

	public function run() {
		$opts = implode(' ', $this->opts);
		$this->printTaskInfo('Install bower packages: ' . $opts);
		$line = system($this->command . ' install ' . $opts, $code);
		return new Result($this, $code, $line);
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

	public function run() {
		$opts = implode(' ', $this->opts);
		$this->printTaskInfo('Update bower packages: ' . $opts);
		$line = system($this->command . ' update ' . $opts, $code);
		return new Result($this, $code, $line);
	}
}
