<?php
namespace Robo\Task\Testing;

use Robo\Contract\CommandInterface;
use Robo\Contract\PrintedInterface;
use Robo\Task\BaseTask;

/**
 * Runs PHPUnit tests
 *
 * ``` php
 * <?php
 * $this->taskPHPUnit()
 *  ->group('core')
 *  ->bootstrap('test/bootstrap.php')
 *  ->run()
 *
 * ?>
 * ```
 */
class PHPUnit extends BaseTask implements CommandInterface, PrintedInterface
{
    use \Robo\Common\ExecOneCommand;

    protected $command;

    public function __construct($pathToPhpUnit = null)
    {
        if ($pathToPhpUnit) {
            $this->command = $pathToPhpUnit;
        } elseif (file_exists('vendor/bin/phpunit')) {
            $this->command = 'vendor/bin/phpunit';
            if (defined('PHP_WINDOWS_VERSION_BUILD')) {
                $this->command = 'call ' . $this->command;
            }
        } elseif (file_exists('phpunit.phar')) {
            $this->command = 'php phpunit.phar';
        } elseif (is_executable('/usr/bin/phpunit')) {
            $this->command = '/usr/bin/phpunit';
        } elseif (is_executable('~/.composer/vendor/bin/phpunit')) {
            $this->command = '~/.composer/vendor/bin/phpunit';
        } else {
            throw new \Robo\Exception\TaskException(__CLASS__, "Neither local phpunit nor global composer installation not found");
        }
    }

    public function filter($filter)
    {
        $this->option('filter', $filter);
        return $this;
    }

    public function group($group)
    {
        $this->option("group", $group);
        return $this;
    }

    public function excludeGroup($group)
    {
        $this->option("exclude-group", $group);
        return $this;
    }

    /**
     * adds `log-json` option to runner
     *
     * @param string $file
     * @return $this
     */
    public function json($file = null)
    {
        $this->option("log-json", $file);
        return $this;
    }

    /**
     * adds `log-xml` option
     *
     * @param string $file
     * @return $this
     */
    public function xml($file = null)
    {
        $this->option("log-xml", $file);
        return $this;
    }

    public function tap($file = "")
    {
        $this->option("log-tap", $file);
        return $this;
    }

    public function bootstrap($file)
    {
        $this->option("bootstrap", $file);
        return $this;
    }

    public function configFile($file)
    {
        $this->option('-c', $file);
        return $this;
    }

    public function debug()
    {
        $this->option("debug");
        return $this;
    }

    public function getCommand()
    {
        return $this->command . $this->arguments;
    }

    public function run()
    {
        $this->printTaskInfo('Running PHPUnit ' . $this->arguments);
        return $this->executeCommand($this->getCommand());
    }
}