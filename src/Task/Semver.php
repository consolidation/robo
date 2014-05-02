<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Task\Shared\TaskException;
use Robo\Task\Shared\TaskInterface;
use Robo\Result;

/**
 * Tasks to execute all semver commands.
 *
 * @package Robo\Task
 */
trait Semver {


	protected function taskSemverInitialize($pathToSemver = null)
	{
		$semver = new TaskSemver($pathToSemver);
		return $semver->initialize();
	}

	protected function taskSemverIncrement($arg = 'patch', $pathToSemver = null)
	{
		$semver = new TaskSemver($pathToSemver);
		return $semver->increment($arg);
	}

	protected function taskSemverPrerelease($arg, $pathToSemver = null)
	{
		$semver = new TaskSemver($pathToSemver);
		return $semver->prerelease($arg);
	}

	protected function taskSemverMetadata($arg, $pathToSemver = null)
	{
		$semver = new TaskSemver($pathToSemver);
		return $semver->metadata($arg);
	}

	protected function taskSemverFormat($arg, $pathToSemver = null)
	{
		$semver = new TaskSemver($pathToSemver);
		return $semver->format($arg);
	}

	protected function taskSemverTag($pathToSemver = null)
	{
		$semver = new TaskSemver($pathToSemver);
		return $semver->tag();
	}
}

/**
 * Semver wrapper.
 *
 * ``` php
 * <?php
 * $this->taskSemverTag()->run()->getMessage();
 *
 * $this->taskSemverIncrement('patch')->run();
 *
 * if ($this->taskSemverInitialize('/path/to/semver')->run()->wasSuccessful()) {
 *  $this->say('Semver initialized');
 * }
 * ?>
 * ```
 */
class TaskSemver implements TaskInterface {

	use \Robo\Output;

	protected $command;

	private $commands = array(
		'initialize' => false,
		'increment' => ['major', 'minor', 'patch'],
		'prerelease' => true,
		'metadata' => true,
		'format' => true,
		'tag' => false,
	);

	private $outputs = array(
		'initialize' => 'Initializing semver.',
		'increment' => 'Incrementing the %s version',
		'prerelease' => 'Setting pre-release version suffix to %s',
		'metadata' => 'Setting metadata version suffix to %s',
	);

	public function __construct($pathToSemver = null)
	{
		if ($pathToSemver) {
			$this->command = $pathToSemver;
		} else {
			$this->command = exec('which semver');
			if (empty($this->command)) {
				throw new TaskException();
			}
		}
	}

	public function __call($command, $args = null)
	{
		if (!array_key_exists($command, $this->commands)) {
			throw new TaskException('Invalid command.');
		}

		if (empty($args) && !empty($this->commands[$command])) {
			throw new TaskException('Missing required argument.');
		}

		$arg = current($args);

		if (is_array($this->commands[$command]) && !in_array($arg, $this->commands[$command])) {
			throw new TaskException('Invalid argument.');
		}

		if (array_key_exists($command, $this->outputs)) {
			$message = $this->outputs[$command];
			if (!empty($arg)) {
				$message = sprintf($message, $arg);
			}
			$this->printTaskInfo($message);
		}

		$this->command .= ' ' . $command;
		if (!empty($arg)) {
			$this->command .= ' ' . $arg;
		}

		return $this;
	}

	public function run()
	{
		$line = exec($this->command, $output, $code);
		return new Result($this, $code, $line);
	}

}
