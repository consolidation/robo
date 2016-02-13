<?php
namespace Robo\Task\File;

use Iterator;
use Robo\Common\ResourceExistenceChecker;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * Merges files into one. Used for preparing assets.
 *
 * ``` php
 * <?php
 * $this->taskConcat([
 *      'web/assets/screen.css',
 *      'web/assets/print.css',
 *      'web/assets/theme.css'
 *  ])
 *  ->to('web/assets/style.css')
 *  ->run()
 * ?>
 * ```
 */
class Concat extends BaseTask
{
    use ResourceExistenceChecker;

    /**
     * @var array|Iterator files
     */
    protected $files;

    /**
     * @var string dst
     */
    protected $dst;

    /**
     * Constructor.
     *
     * @param array|Iterator $files
     */
    public function __construct($files)
    {
        $this->files = $files;
    }

    /**
     * set the destination file
     *
     * @param string $dst
     *
     * @return Concat The current instance
     */
    public function to($dst)
    {
        $this->dst = $dst;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (is_null($this->dst) || "" === $this->dst) {
            return Result::error($this, 'You must specify a destination file with to() method.');
        }

        if (!$this->checkResources($this->files, 'file')) {
            return Result::error($this, 'Source files are missing!');
        }

        $dump = '';

        foreach ($this->files as $path) {
            foreach (glob($path) as $file) {
                $dump .= file_get_contents($file) . "\n";
            }
        }

        $this->printTaskInfo(sprintf('Writing <info>%s</info>', $this->dst));

        $dst = $this->dst . '.part';
        file_put_contents($dst, $dump);
        rename($dst, $this->dst);

        return Result::success($this);
    }
}
