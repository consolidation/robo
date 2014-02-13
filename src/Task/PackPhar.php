<?php
namespace Robo\Task;
use Robo\Result;
use Symfony\Component\Console\Helper\ProgressHelper;

/**
 * Packs files into phar
 */
trait PackPhar {

    /**
     * @param $filename
     * @return \Robo\Task\PackPhar
     */
    protected function taskPackPhar($filename)
    {
        return new PackPharTask($filename);
    }

}

/**
 * Creates Phar
 *
 * ``` php
 * <?php
 * $pharTask = $this->taskPackPhar('package/codecept.phar')
    ->compress()
    ->stub('package/stub.php');

    $finder = Finder::create()
        ->name('*.php')
        ->in('src');

    foreach ($finder as $file) {
        $pharTask->addFile('src/'.$file->getRelativePathname(), $file->getRealPath());
    }

    $finder = Finder::create()->files()
        ->name('*.php')
        ->in('vendor');

    foreach ($finder as $file) {
        $pharTask->addStripped('vendor/'.$file->getRelativePathname(), $file->getRealPath());
    }
    $pharTask->run();

    $code = $this->taskExec('php package/codecept.phar')->run();
 * ?>
 * ```
 */
class PackPharTask implements TaskInterface {
    use \Robo\Output;
    /**
     * @var \Phar
     */
    protected $phar;
    protected $compileDir = null;
    protected $filename;
    protected $compress = false;
    protected $stub;
    protected $bin;

    protected $files = [];

    public function __construct($filename)
    {
        $file = new \SplFileInfo($filename);
        $this->filename = $filename;
        @unlink($file->getRealPath());
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

    public function stub($stub)
    {
        $this->phar->setStub(file_get_contents($stub));
        return $this;
    }

    public function run()
    {
        $this->printTaskInfo("creating <info>{$this->filename}</info>");
        $this->phar->setSignatureAlgorithm(\Phar::SHA1);
        $this->phar->startBuffering();

        $this->printTaskInfo('packing '.count($this->files).' files into phar');

        $progress = new ProgressHelper();
        $progress->start($this->getOutput(), count($this->files));
        foreach ($this->files as $path => $content) {
            $this->phar->addFromString($path, $content);
            $progress->advance();
        }
        $this->phar->stopBuffering();
        $progress->finish();

        if($this->compress and in_array('GZ', \Phar::getSupportedCompression())) {
            $this->taskInfo($this->filename . " compressed");
            $this->phar = $this->phar->compressFiles(\Phar::GZ);
        }
        $this->printTaskInfo($this->filename." produced");
        return Result::success($this);
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

 