<?php
namespace Robo\Task;

use Robo\Output;
use Robo\Task\Shared\CommandStack;

trait Drush
{

    protected function taskDrushStack($pathToDrush = 'drush')
    {
        return new DrushStackTask($pathToDrush);
    }

}

/**
 * Runs Drush commands in stack. You can use `stopOnFail()` to point that stack should be terminated on first fail.
 * You can define global options for all commands (like Drupal root and uri).
 * The option -y is always set, as it makes sense in a task runner.
 *
 * ``` php
 * $this->taskDrushStack()
 *     ->drupalRootDirectory('/var/www/html/some-site')
 *     ->uri('my-multi-site')
 *     ->maintenanceOn()
 *     ->updateDb()
 *     ->revertAllFeatures()
 *     ->maintenanceOff()
 *     ->run();
 * ```
 */
class DrushStackTask extends CommandStack
{
    use Output;
    use Shared\Executable;

    protected $drupalRootDirectory = '';

    protected $uri = '';

    protected $debug = false;

    protected $verbose = false;

    protected $simulate = false;

    /**
     * Fetched in constructor.
     *
     * @var string
     */
    protected $drushVersion;

    public function __construct($pathToDrush = 'drush')
    {
        $this->executable = $pathToDrush;
        $this->drushVersion = $this->getVersion();
    }

    public function drupalRootDirectory($drupalRootDirectory)
    {
        $this->printTaskInfo('Drupal root: <info>' . $drupalRootDirectory . '</info>');
        $this->option('-r', $drupalRootDirectory);

        return $this;
    }

    public function uri($uri)
    {
        $this->printTaskInfo('URI: <info>' . $uri . '</info>');
        $this->option('-l', $uri);

        return $this;
    }

    public function debug()
    {
        $this->option('-d');

        return $this;
    }

    public function verbose()
    {
        $this->option('-v');

        return $this;
    }

    public function simulate()
    {
        $this->option('-s');

        return $this;
    }

    /**
     * Echoes and returns the drush version.
     *
     * @return $this
     */
    public function getVersion()
    {
        $result = $this->executeCommand($this->executable . ' --version');
        $output = $result->getMessage();
        $drushVersion = 'unknown';
        if (preg_match('#[0-9.]+#', $output, $matches)) {
            $drushVersion = $matches[0];
        }

        return $drushVersion;
    }

    /**
     * Executes `drush status`
     *
     * @return $this
     */
    public function status()
    {
        return $this->exec('status');
    }

    /**
     * Clears the given cache.
     *
     * @param string $name cache name
     * @return $this
     */
    public function clearCache($name = 'all')
    {
        $this->printTaskInfo('Clear cache');

        return $this->exec('cc ' . $name);
    }

    /**
     * Runs pending database updates.
     *
     * @return $this
     */
    public function updateDb()
    {
        $this->printTaskInfo('Do database updates');
        $this->exec('updb');
        if (-1 === version_compare($this->drushVersion, '6.0')) {
            $this->printTaskInfo('Will clear cache after db updates for drush '
                . $this->drushVersion);
            $this->clearCache();
        } else {
            $this->printTaskInfo('Will not clear cache after db updates, since drush '
                . $this->drushVersion . ' should do it');
        }

        return $this;
    }

    /**
     * @param bool $force force revert even if Features assumes components' state are default
     * @param string $excludedFeatures space-delimited list of features to exclude from being reverted
     *
     * @return $this
     */
    public function revertAllFeatures($force = false, $excludedFeatures = '')
    {
        $this->printTaskInfo('Revert all features');
        $args = $excludedFeatures . ($force ? ' --force' : '');

        return $this->exec('fra ' . $args);
    }

    /**
     * Enables the maintenance mode.
     *
     * @return $this
     */
    public function maintenanceOn()
    {
        $this->printTaskInfo('Turn maintenance mode on');
        return $this->exec('vset --exact maintenance_mode 1');
    }

    /**
     * Disables the maintenance mode.
     *
     * @return $this
     */
    public function maintenanceOff()
    {
        $this->printTaskInfo('Turn maintenance mode off');
        return $this->exec('vdel --exact maintenance_mode');
    }

    /**
     * Runs the given drush command.
     *
     * @param string $command
     * @return $this
     */
    public function exec($command)
    {
        return parent::exec($this->prependOptions($command));
    }

    /**
     * @param string $command
     * @return string
     */
    protected function prependOptions($command)
    {
        if (is_array($command)) {
            $command = implode(' ', array_filter($command));
        }

        return $this->arguments . ' -y ' . $command;
    }

}
