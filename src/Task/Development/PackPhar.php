<?php
namespace Robo\Task\Development;

use Robo\Common\Timer;
use Robo\Contract\PrintedInterface;
use Robo\Result;
use Robo\Task\BaseTask;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Creates Phar.
 *
 * ``` php
 * <?php
 * $pharTask = $this->PackPhar('package/codecept.phar')
 *   ->compress()
 *   ->stub('package/stub.php');
 *
 *  $finder = Finder::create()
 *      ->name('*.php')
 *        ->in('src');
 *
 *    foreach ($finder as $file) {
 *        $pharTask->addFile('src/'.$file->getRelativePathname(), $file->getRealPath());
 *    }
 *
 *    $finder = Finder::create()->files()
 *        ->name('*.php')
 *        ->in('vendor');
 *
 *    foreach ($finder as $file) {
 *        $pharTask->addStripped('vendor/'.$file->getRelativePathname(), $file->getRealPath());
 *    }
 *    $pharTask->run();
 *
 *    // verify Phar is packed correctly
 *    $code = $this->_exec('php package/codecept.phar');
 * ?>
 * ```
 */
class PackPhar extends BaseTask implements PrintedInterface
{
    use Timer;

    /**
     * @var \Phar
     */
    protected $phar;
    protected $compileDir = null;
    protected $filename;
    protected $compress = false;
    protected $stub;
    protected $bin;

    protected $stubTemplate = <<<EOF
#!/usr/bin/env php
<?php
Phar::mapPhar();
%s
__HALT_COMPILER();
EOF;

    protected $files = [];

    public function getPrinted()
    {
        return true;
    }

    public function __construct($filename)
    {
        $file = new \SplFileInfo($filename);
        $this->filename = $filename;
        if (file_exists($file->getRealPath())) {
            @unlink($file->getRealPath());
        }
        $this->phar = new \Phar($file->getPathname(), 0, $file->getFilename());
    }

    /**
     * @param boolean $compress
     * @return $this
     */
    public function compress($compress = true)
    {
        $this->compress = $compress;
        return $this;
    }

    /**
     * @param $stub
     * @return $this
     */
    public function stub($stub)
    {
        $this->phar->setStub(file_get_contents($stub));
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("Creating <info>{$this->filename}</info>");
        $this->phar->setSignatureAlgorithm(\Phar::SHA1);
        $this->phar->startBuffering();

        $this->printTaskInfo('Packing ' . count($this->files) . ' files into phar');

        $progress = new ProgressBar($this->getOutput());
        $progress->start(count($this->files));
        $this->startTimer();
        foreach ($this->files as $path => $content) {
            $this->phar->addFromString($path, $content);
            $progress->advance();
        }
        $this->phar->stopBuffering();
        $progress->finish();
        $this->getOutput()->writeln('');

        if ($this->compress and in_array('GZ', \Phar::getSupportedCompression())) {
            if (count($this->files) > 1000) {
                $this->printTaskInfo("Too many files. Compression DISABLED");
            } else {
                $this->printTaskInfo($this->filename . " compressed");
                $this->phar = $this->phar->compressFiles(\Phar::GZ);
            }
        }
        $this->stopTimer();
        $this->printTaskSuccess("<info>{$this->filename}</info> produced");
        return Result::success($this, '', ['time' => $this->getExecutionTime()]);
    }


    public function addStripped($path, $file)
    {
        $this->files[$path] = $this->stripWhitespace(file_get_contents($file));
        return $this;
    }

    public function addFile($path, $file)
    {
        $this->files[$path] = file_get_contents($file);
        return $this;
    }

    public function executable($file)
    {
        $source = file_get_contents($file);
        if (strpos($source, '#!/usr/bin/env php') === 0) {
            $source = substr($source, strpos($source, '<?php') + 5);
        }
        $this->phar->setStub(sprintf($this->stubTemplate, $source));
        return $this;
    }

    /**
     * Strips whitespace from source. Taken from composer
     * @param $source
     * @return string
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                // $output .= $token[1];
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }
}
